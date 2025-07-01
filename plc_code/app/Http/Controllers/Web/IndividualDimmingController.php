<?php

namespace App\Http\Controllers\Web;

use App\Enums\CommandTypes;
use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\Concentrator;
use App\Models\RemoteTerminal;
use App\Models\SchedulePreset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpMqtt\Client\MqttClient;

class IndividualDimmingController extends Controller
{
    public function dimDcu(Request $request, string $id)
    {
        if ($request->integer('dim') < 0 || ($request->integer('dim') > 0 && $request->integer('dim') < 12) || !is_numeric($request->dim)) {
            return response()->json(['message' => 'Please provide a valid brightness value. (0 or 12+)'], 400);
        }

        $dimValue = $request->dim ?? 0;
        $dcu = Concentrator::findOrFail($id);

        if (!$dcu || !$dcu->concentrator_no) {
            return response()->json(['message' => 'DCU does not have a valid ID.'], 400);
        }

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
        $mqtt->connect();

        $id1 = floor(microtime(true) * 1000);
        $ti1 = (int) substr($id1, 0, -1);
        $command1 = [
            "id" => $id1,
            "ve" => "1.0",
            "ac" => 1,
            "pa" => [
                "va" => [
                    "ddqkrbms" => 0
                ],
                "ti" => $ti1
            ],
            "idi" => "method.set"
        ];
        $command1Encoded = json_encode($command1);
        $mqtt->publish("/mqtt-plc-center/{$dcu->concentrator_no}/service/down", $command1Encoded, 0);
        $cmd1 = [
            'command' => $command1Encoded,
            'concentrator_id' => $dcu->id,
            'is_sent' => 1,
            'command_type' => CommandTypes::METHOD_UNSET_SCHEDULE
        ];
        $commands[] = $cmd1;
        Command::create($cmd1);

        $id = floor(microtime(true) * 1000);
        $ti = substr($id, 0, -1);
        $command2 = [
            "id" => $id,
            "ve" => "1.0",
            "pa" => [
                "va" => [
                    "skddqk" => [100, (int) $dimValue, 0, 0, 0]
                ],
                "ti" => (int) $ti
            ],
            "idi" => "method.skddqk"
        ];
        $command2Encoded = json_encode($command2);
        $mqtt->publish("/mqtt-plc-center/{$dcu->concentrator_no}/event/down", $command2Encoded, 0);
        $cmd2 = [
            'command' => $command2Encoded,
            'concentrator_id' => $dcu->id,
            'is_sent' => 1,
            'command_type' => CommandTypes::DCU_DIMMING
        ];
        $commands[] = $cmd2;
        Command::create($cmd2);
        $mqtt->disconnect();

        activity('dimming')
            ->causedBy(Auth::user())
            ->event('DCU dimming')
            ->performedOn($dcu)
            ->withProperties(['commands' => $commands])
            ->log('DCU dimming');

        return response()->json([
            'message' => 'Command sent successfully.',
            'commands' => [$command1, $command2]
        ]);
    }

    public function dimRtu(Request $request, string $id)
    {
        if (!validateBrightness($request->dim, $request->power)) {
            return response()->json(['message' => 'Please provide a valid brightness value. (0 or  between 12 and 100)'], 400);
        }

        $dimValue = $request->dim ?? 0;
        $power = $request->power;
        $rtu = RemoteTerminal::findOrFail($id);
        if ($power && $power > 0 && $rtu->rated_power) {
            $dimValue = $power > $rtu->rated_power ? 100 : number_format(ceil($power / $rtu->rated_power * 100));
        }

        if (!$rtu || !$rtu->code) {
            return response()->json(['message' => 'RTU does not have a valid ID.'], 400);
        }
        if (!$rtu->concentrator?->concentrator_no) {
            return response()->json(['message' => 'DCU does not have a valid ID.'], 400);
        }

        $cmdHex = "8A0006{$rtu->code}FF15001E";
        $dimHex = str_pad(dechex($dimValue), 2, "0", STR_PAD_LEFT);
        $cmdHex .= $dimHex;
        $cmdHex .= '320032';
        $cmdHex .= getCommandXor($cmdHex);

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
        $mqtt->connect();

        $id1 = floor(microtime(true) * 1000);
        $ti1 = (int) substr($id1, 0, -1);
        $command1 = [
            "pa" => [
                "ti" => (int) $ti1,
                "va" => [
                    "ddid" => $rtu->code,
                    "u8data" => strtoupper($cmdHex),
                ]
            ],
            "ac" => 1,
            "id" => $id1,
            "idi" => "method.ddtc",
            "ve" => "1.0"
        ];
        $command1Encoded = json_encode($command1);
        $channel = "/mqtt-plc-center/{$rtu->concentrator->concentrator_no}/event/down";
        $mqtt->publish($channel, $command1Encoded, 0);
        $dbCommand = [
            'command' => $command1Encoded,
            'concentrator_id' => $rtu->concentrator_id,
            'rtu_id' => $rtu->id,
            'command_type' => CommandTypes::RTU_DIMMING,
            'is_sent' => 1,
        ];
        Command::create($dbCommand);
        $mqtt->disconnect();

        $rtu->update([
            'last_command_brightness' => CommandTypes::RTU_DIMMING,
        ]);

        activity('dimming')
            ->causedBy(Auth::user())
            ->event('RTU dimming')
            ->performedOn($rtu)
            ->withProperties(['commands' => $dbCommand])
            ->log('RTU dimming');

        return response()->json([
            'message' => 'Command sent successfully.',
            'commands' => [$command1],
            'channel' => $channel
        ]);
    }

    public function schedule(Request $request, string $projectId, string $id)
    {
        $validator = Validator::make($request->all(), $this->scheduleValidationRules(), $this->scheduleValidationMessages());
        if ($validator->fails()) {
            return response()->json([
                'message' => array_map(fn($i) => $i[0], array_values(array_values($validator->getMessageBag()->toArray())))
            ], 422);
        }
        // $onlyTimes = array_map(fn($i) => $i['time'], $request->times);
        // for ($i = 0; $i < count($onlyTimes) - 1; $i++) {
        //     if (strtotime($onlyTimes[$i]) >= strtotime($onlyTimes[$i + 1])) {
        //         return response()->json(['message' => 'Times must be in ascending order.'], 422);
        //     }
        // }

        $preset = SchedulePreset::find($request->preset_id);
        if (!$preset->schedule || !is_array($preset->schedule)) {
            return response()->json([
                'message' => 'Selected preset has invalid data.'
            ], 422);
        }

        $dcu = Concentrator::findOrFail($id);
        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
        $mqtt->connect();

        $id1 = floor(microtime(true) * 1000);
        $ti1 = (int) substr($id1, 0, -1);
        $command1 = [
            "id" => $id1,
            "ve" => "1.0",
            "ac" => 1,
            "pa" => [
                "va" => [
                    "ddqkrbms" => 1
                ],
                "ti" => $ti1
            ],
            "idi" => "method.set"
        ];
        $command1Encoded = json_encode($command1);
        $mqtt->publish("/mqtt-plc-center/{$dcu->concentrator_no}/service/down", $command1Encoded, 0);
        Command::create([
            'command' => $command1Encoded,
            'concentrator_id' => $dcu->id,
            'is_sent' => 1,
            'command_type' => CommandTypes::DCU_DIMMING
        ]);
        sleep(1);

        $timesString = '';
        // $times = $request->times;
        foreach ($preset->schedule as $key => $time) {
            if ($key == 0) {
                $timesString .= "0,{$time['brightness']},0,0,0,0";
            } else {
                $exploded = explode(':', $time['time']);
                $hour = $exploded[0];
                $minute = $exploded[1];
                $timeSeconds = $hour * 3600 + $minute * 60;
                $timesString .= ",$timeSeconds,{$time['brightness']},0,0,0,0";
            }
        }

        $id2 = floor(microtime(true) * 1000);
        $ti2 = (int) substr($id2, 0, -1);
        $command2 = [
            "id" => $id2,
            "ve" => "1.0",
            "ac" => 1,
            "pa" => [
                "va" => [
                    "ddqkzr" => array_map(fn($i) => (int) $i, explode(',', $timesString)),
                ],
                "ti" => $ti2
            ],
            "idi" => "method.set"
        ];
        $command2Encoded = json_encode($command2);
        $mqtt->publish("/mqtt-plc-center/{$dcu->concentrator_no}/service/down", $command2Encoded, 0);
        Command::create([
            'command' => $command2Encoded,
            'concentrator_id' => $dcu->id,
            'is_sent' => 1,
            'command_type' => CommandTypes::DCU_DIMMING
        ]);
        $dcu->update(['schedule_preset_id' => $preset->id]);

        return response()->json([
            'message' => 'Command sent successfully.',
            'commands' => [$command1, $command2]
        ]);
    }

    private function scheduleValidationRules()
    {
        return [
            // 'times' => 'required|array|size:6',
            // 'times.*.time' => 'required|date_format:H:i',
            // 'times.*.brightness' => [
            //     'required',
            //     'integer',
            //     function ($attribute, $value, $fail) {
            //         if (!($value == 0 || ($value >= 12 && $value <= 100))) {
            //             $fail("Brightness must be 0 or between 12 and 100.");
            //         }
            //     }
            // ],
            'preset_id' => 'required|exists:schedule_presets,id'
        ];
    }

    private function scheduleValidationMessages()
    {
        return [
            'times.required' => 'Please enter times.',
            'times.array' => 'Invalid data.',
            'times.size' => 'Please provide exactly :size times.',
            'times.*.time.required' => 'Please enter time.',
            'times.*.time.time' => 'Please enter a valid time.',
            'times.*.brightness.required' => 'Please enter brightness.',
            'times.*.brightness.integer' => 'Brightness must be between 12 and 100.',
            'times.*.brightness.between' => 'Brightness must be between 12 and 100.',
        ];
    }
}
