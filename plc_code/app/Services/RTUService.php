<?php

namespace App\Services;

use App\Models\Concentrator;
use App\Models\Luminary;
use App\Models\Remote\Command;
use App\Models\RemoteTerminal;
use App\Models\SubGroup;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use Throwable;

class RTUService
{
    public static function sendRtuDataReadingCommand(string $rtuCode, string $dcuId)
    {
        try {
            $dcu = Concentrator::find($dcuId);
            if (!$dcu || !$dcu->concentrator_no) {
                return;
            }

            $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
            $mqtt->connect();
            $channel = "/mqtt-plc-center/{$dcu->concentrator_no}/service/down";
            $rtus = RemoteTerminal::where('concentrator_id', $dcuId)->get();
            $rtus = $rtus->pluck('code')->toArray() + [$rtuCode];

            $id0 = floor(microtime(true) * 1000);
            $ti0 = (int) substr($id0, 0, -1);
            $command0 = [
                "pa" => [
                    "ti" => $ti0,
                    "va" => [
                        "zdbmd01" => [
                            $rtus
                        ],
                        "bmddqbs" => 1,
                        "bmdzbs" => 1
                    ]
                ],
                "ac" => 1,
                "id" => $id0,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command0Encoded = json_encode($command0);
            $mqtt->publish($channel, $command0Encoded, 0);
            sleep(1);

            $id1 = floor(microtime(true) * 1000);
            $ti1 = (int) substr($id1, 0, -1);
            $command1 = [
                "pa" => [
                    "ti" => $ti1,
                    "va" => [
                        "lxzq" => 5
                    ]
                ],
                "ac" => 1,
                "id" => $id1,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command1Encoded = json_encode($command1);
            $mqtt->publish($channel, $command1Encoded, 0);
            sleep(1);

            $id2 = floor(microtime(true) * 1000);
            $ti2 = (int) substr($id2, 0, -1);
            $command2 = [
                "pa" => [
                    "ti" => $ti2,
                    "va" => [
                        "lxkg" => 1
                    ]
                ],
                "ac" => 1,
                "id" => $id2,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command2Encoded = json_encode($command2);
            $mqtt->publish($channel, $command2Encoded, 0);
            sleep(1);

            $cmdHex = "8A0000{$rtuCode}FF14";
            $cmdHex .= getCommandXor($cmdHex);
            $id3 = floor(microtime(true) * 1000);
            $ti3 = (int) substr($id3, 0, -1);
            $command3 = [
                "pa" => [
                    "ti" => $ti3,
                    "va" => [
                        "ddid" => $rtuCode,
                        "u8data" => strtoupper($cmdHex),
                    ]
                ],
                "ac" => 1,
                "id" => $id3,
                "idi" => "method.ddtc",
                "ve" => "1.0"
            ];
            $command3Encoded = json_encode($command3);
            $mqtt->publish($channel, $command3Encoded, 0);
        } catch (Throwable $t) {
            Log::error("RTU add => Command sending error: {$rtuCode}");
        }
    }

    public static function syncRtuToDcu(string $dcuId)
    {
        try {
            $dcu = Concentrator::find($dcuId);
            if (!$dcu || !$dcu->concentrator_no) {
                return [
                    'code' => 404,
                    'message' => 'DCU not found!'
                ];
            }

            $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
            $mqtt->connect();
            $channel = "/mqtt-plc-center/{$dcu->concentrator_no}/service/down";
            $rtus = RemoteTerminal::where('concentrator_id', $dcuId)->get();
            $rtusCount = $rtus->count();
            if (!$rtusCount) {
                return [
                    'code' => 404,
                    'message' => 'There are no RTUs assigned to this DCU. Please add all RTUs of this DCU in the web panel before syncing.'
                ];
            }
            if ($rtusCount > 1024) {
                return [
                    'code' => 404,
                    'message' => "There are $rtusCount RTUs assigned to this DCU. Only 1024 RTUs can be added under 1 DCU. Please remove or reassign some of the RTUs."
                ];
            }

            $rtus = $rtus->pluck('code');
            $rtuChunks = $rtus->chunk(128);
            $va = [];
            foreach ($rtuChunks as $key => $chunk) {
                $va['zdbmd0' . $key + 1] = $chunk->toArray();
            }
            $va['bmddqbs'] = 1;
            $va['bmdzbs'] = 1;

            $id0 = floor(microtime(true) * 1000);
            $ti0 = (int) substr($id0, 0, -1);
            $command0 = [
                "pa" => [
                    "ti" => $ti0,
                    "va" => $va
                ],
                "ac" => 1,
                "id" => $id0,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command0Encoded = json_encode($command0);
            $mqtt->publish($channel, $command0Encoded, 0);
            sleep(1);

            $id1 = floor(microtime(true) * 1000);
            $ti1 = (int) substr($id1, 0, -1);
            $command1 = [
                "pa" => [
                    "ti" => $ti1,
                    "va" => [
                        "lxzq" => (int) env('RTU_DATA_INTERVAL', 5)
                    ]
                ],
                "ac" => 1,
                "id" => $id1,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command1Encoded = json_encode($command1);
            $mqtt->publish($channel, $command1Encoded, 0);
            sleep(1);

            $id2 = floor(microtime(true) * 1000);
            $ti2 = (int) substr($id2, 0, -1);
            $command2 = [
                "pa" => [
                    "ti" => $ti2,
                    "va" => [
                        "lxkg" => 1
                    ]
                ],
                "ac" => 1,
                "id" => $id2,
                "idi" => "method.set",
                "ve" => "1.0"
            ];
            $command2Encoded = json_encode($command2);
            $mqtt->publish($channel, $command2Encoded, 0);
            sleep(1);

            // $cmdHex = "8A0000{$rtuCode}FF14";
            // $cmdHex .= getCommandXor($cmdHex);
            // $id3 = floor(microtime(true) * 1000);
            // $ti3 = (int) substr($id3, 0, -1);
            // $command3 = [
            //     "pa" => [
            //         "ti" => $ti3,
            //         "va" => [
            //             "ddid" => $rtuCode,
            //             "u8data" => strtoupper($cmdHex),
            //         ]
            //     ],
            //     "ac" => 1,
            //     "id" => $id3,
            //     "idi" => "method.ddtc",
            //     "ve" => "1.0"
            // ];
            // $command3Encoded = json_encode($command3);
            // $mqtt->publish($channel, $command3Encoded, 0);
            // return response()->json([$command0, $command1, $command2]);
            return [
                'code' => 200,
                'message' => 'RTU sync command sent.'
            ];
        } catch (Throwable $t) {
            Log::error("RTU sync => Command sending error: {$t->getMessage()}");

            return [
                'code' => 500,
                'message' => 'Something went wrong!'
            ];
        }
    }
}
