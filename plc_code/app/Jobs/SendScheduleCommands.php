<?php

namespace App\Jobs;

use App\Enums\CommandTypes;
use App\Models\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PhpMqtt\Client\MqttClient;
use Throwable;

class SendScheduleCommands implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dbCommands = [];
        $rtusToUpdate = [];
        $now = date('Y-m-d H:i:s');
        $count = 0;

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
        $mqtt->connect();

        foreach ($this->data as $data) {
            if (!isset($data['rtu_code']) || !$data['rtu_code'] || !isset($data['dcu_code']) || !$data['dcu_code'] || !isset($data['brightness']) || !$data['brightness']) {
                continue;
            }

            $cmdHex = "8A0006{$data['rtu_code']}FF15001E";
            $dimHex = str_pad(dechex($data['brightness']), 2, "0", STR_PAD_LEFT);
            $cmdHex .= $dimHex;
            $cmdHex .= '320032';
            $cmdHex .= getCommandXor($cmdHex);

            $id1 = floor(microtime(true) * 1000);
            $ti1 = (int) substr($id1, 0, -1);
            $command1 = [
                "pa" => [
                    "ti" => (int) $ti1,
                    "va" => [
                        "ddid" => $data['rtu_code'],
                        "u8data" => strtoupper($cmdHex),
                    ]
                ],
                "ac" => 1,
                "id" => $id1,
                "idi" => "method.ddtc",
                "ve" => "1.0"
            ];
            $command1Encoded = json_encode($command1);
            $channel = "/mqtt-plc-center/{$data['dcu_code']}/event/down";
            $mqtt->publish($channel, $command1Encoded, 0);

            if (isset($rtu_id['rtu_id']) && $rtu_id['rtu_id']) {
                $dbCommands[] = [
                    'command' => $command1Encoded,
                    'concentrator_id' => $rtu_id['dcu_id'] ?? null,
                    'rtu_id' => $rtu_id['rtu_id'],
                    'command_type' => CommandTypes::RTU_DIMMING,
                    'is_sent' => 1,
                    'created_at' => $now
                ];
                $rtusToUpdate[] = [
                    'rtu_id' => $rtu_id['rtu_id'],
                    'brightness' =>  $rtu_id['brightness']
                ];
            }
            $count++;
            sleep(1);
        }
        $mqtt->disconnect();
        Log::channel('cmd')->info("$count Shcedule command sent.");

        try {
            if ($count > 0) {
                Command::insert($dbCommands);

                $tempTableName = 'rtu_brightness_temp';
                Schema::dropIfExists($tempTableName);
                Schema::create($tempTableName, function (Blueprint $table) {
                    $table->integer('rtu_id');
                    $table->integer('brightness');
                    $table->temporary();
                });
                DB::table($tempTableName)->insert($rtusToUpdate);

                DB::statement("
                    UPDATE remote_terminals
                    JOIN $tempTableName AS T ON T.rtu_id = remote_terminals.id
                    SET remote_terminals.last_command_brightness = T.brightness
                ");
            }
        } catch (Throwable $t) {
            Log::channel('cmd')->error("Error after sending schedule commands. {$t->getMessage()}");
        }
    }
}
