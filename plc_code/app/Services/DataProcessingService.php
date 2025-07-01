<?php

namespace App\Services;

use App\Enums\AlarmTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DataProcessingService
{
    public static function calculatePowerConsumption(string $rtuCode, mixed $runningTime, mixed $power, mixed $deviceTime, ?string $dcuCode = null)
    {
        try {
            if (!is_numeric($runningTime) || !is_numeric($power))
                return;

            // $rtu = DB::table('remote_terminals')->where('code', $rtuCode)->first();
            // if (!$rtu) return;

            $now = date('Y-m-d H:i:s');
            $totalPowerConsumption = $power / 60 * 5;
            // $totalPowerConsumption = $power / 60 * $runningTime;
            DB::table('power_consumptions')->insert([
                'dcu_code' => $dcuCode,
                'rtu_code' => $rtuCode,
                'power' => $totalPowerConsumption,
                'device_time' => $deviceTime,
                'created_at' => $now,
            ]);

            $today = date('Y-m-d');
            $data = DB::table('daily_running_times')->where('rtu_code', $rtuCode)->where('date', $today)->first();
            if ($power > 0) {
                if ($data) {
                    DB::table('daily_running_times')->where('rtu_code', $rtuCode)->where('date', $today)->update([
                        'running_time' => $data->running_time + 5
                    ]);
                } else {
                    DB::table('daily_running_times')->insert([
                        'rtu_code' => $rtuCode,
                        'dcu_code' => $dcuCode,
                        'running_time' => 5,
                        'date' => $today,
                        'created_at' => $now,
                    ]);
                }
            }
        } catch (Throwable $t) {
            Log::channel('data_processing')->error("Power consumption calculation error: {$t->getMessage()}");
        }
    }

    public static function processAlertData(string $rtuCode, mixed $voltage, mixed $brightness, mixed $deviceTime, ?string $dcuCode = null)
    {
        try {
            $alertData = [];
            if ($voltage > env('OVER_VOLTAGE', 260)) {
                $alertData = [
                    'alert_type' => AlarmTypes::OVERVOLTAGE->value,
                    'alert_details' => json_encode(['voltage' => $voltage]),
                ];
            } elseif ($voltage < env('UNDER_VOLTAGE', 180) && $brightness > 0) {
                $alertData = [
                    'alert_type' => AlarmTypes::UNDERVOLTAGE->value,
                    'alert_details' => json_encode(['voltage' => $voltage]),
                ];
            }
            // elseif ($current > 1) {
            //     $alertData = [
            //         'alert_type' => AlertTypes::OVERCURRENT->value,
            //         'alert_details' => json_encode(['main_light_current' => $current]),
            //     ];
            // } elseif ($current < 0.1) {
            //     $alertData = [
            //         'alert_type' => AlertTypes::UNDERCURRENT->value,
            //         'alert_details' => json_encode(['main_light_current' => $current]),
            //     ];
            // }
            if (!empty($alertData)) {
                $alertData['dcu_code'] = $dcuCode;
                $alertData['rtu_code'] = $rtuCode;
                $alertData['created_at'] = date('Y-m-d H:i:s');
                $alertData['device_time'] = $deviceTime;

                DB::table('alerts')->insert($alertData);
            }
        } catch (Throwable $t) {
            Log::channel('data_processing')->error("Alert processing error: {$t->getMessage()}");
        }
    }

    public static function processRunningTime(string $rtuCode, mixed $runningTime, mixed $deviceTime, ?string $dcuCode = null) {}
}
