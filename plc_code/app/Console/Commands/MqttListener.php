<?php

namespace App\Console\Commands;

use App\Services\DataProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;

class MqttListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mqtt = new MqttClient(env('MQTT_SERVER'), env('MQTT_PORT'), env('MQTT_CLIENT'));
        if (!$mqtt->isConnected()) {
            $mqtt->connect();
        }

        $dcus = DB::table('concentrators')->get();
        $channels = $dcus->pluck('concentrator_no')->map(fn($i) => "/mqtt-plc-center/$i/event/up");
        foreach ($channels as $channel) {
            $mqtt->subscribe($channel, function ($topic, $message) use ($channel) {
                echo "Received: $message \n";
                // StoreDeviceData::dispatch($channel, $message, $topic);

                $now = date('Y-m-d H:i:s');
                $messageEncoded = json_decode($message, true);
                $dcuCode = explode('/', $channel)[2];
                DB::table('device_data')->insert([
                    'device_code' => $dcuCode,
                    'data' => $message,
                    'topic' => $topic,
                    'created_at' => $now,
                ]);

                //report data
                if (isset($messageEncoded['idi']) && $messageEncoded['idi'] == 'event.ddlx') {
                    $rtuCode = $messageEncoded['pa']['va']['ddid'];
                    DB::table('remote_terminals')->where('code', $rtuCode)->update([
                        'status_updated_at' => $now,
                    ]);

                    //process report data start
                    $data = $messageEncoded['pa']['va']['u8data'];
                    $deviceTime = $messageEncoded['pa']['ti'] ?? null;
                    $actualData = substr($data, 20, strlen($data));

                    $voltage = hexdec(substr($actualData, 0, 4));

                    $mainLightCurrentHex = substr($actualData, 4, 8);
                    $mainLightCurrentBin = strrev(hex2bin($mainLightCurrentHex));
                    $mainLightCurrent = unpack("f", $mainLightCurrentBin)[1];

                    $mainLightPowerHex = substr($actualData, 20, 4);
                    $mainLightPower = hexdec($mainLightPowerHex);

                    $tempHex = substr($actualData, 44, 2);
                    $temp = hexdec($tempHex);

                    $runningTimeHex = substr($actualData, 52, 2);
                    $runningTime = hexdec($runningTimeHex);

                    $runningModeHex = substr($actualData, 54, 2);
                    $runningMode = hexdec($runningModeHex);

                    $mainLightBrightnessHex = substr($actualData, 56, 2);
                    $mainLightBrightness = hexdec($mainLightBrightnessHex);

                    $mainLightColorTempHex = substr($actualData, 58, 2);
                    $mainLightColorTemp = hexdec($mainLightColorTempHex);
                    $deviceTimeFormatted = null;
                    if (isValidTimeStamp($deviceTime)) {
                        $deviceTimeFormatted = date('Y-m-d H:i:s', $deviceTime > time() + 18000 ? $deviceTime - 21600 : $deviceTime);
                    }

                    DB::table('report_data')->insert([
                        'rtu_code' => $rtuCode,
                        'voltage' => $voltage,
                        'main_light_current' => $mainLightCurrent,
                        'main_light_power' => $mainLightPower,
                        'temperature' => $temp,
                        'main_light_brightness' => $mainLightBrightness,
                        'main_light_color_temp' => $mainLightColorTemp,
                        'running_time' => $runningTime,
                        'running_mode' => $runningMode,
                        'raw_data' => $message,
                        'device_time' => $deviceTimeFormatted,
                        'created_at' => $now,
                    ]);
                    //process report data end

                    DataProcessingService::calculatePowerConsumption($rtuCode, $runningTime, $mainLightPower, $deviceTimeFormatted, $dcuCode);

                    //process alert data start
                    DataProcessingService::processAlertData($rtuCode, $voltage, $mainLightBrightness, $deviceTimeFormatted, $dcuCode);
                    //process alert data end
                } else if (isset($messageEncoded['idi']) && $messageEncoded['idi'] == 'event.post') {
                    DB::table('concentrators')->where('concentrator_no', $dcuCode)->update([
                        'status_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    // echo "no data\n";
                }
            }, 0);
        }

        $mqtt->loop(true);
        $mqtt->disconnect();
    }
}
