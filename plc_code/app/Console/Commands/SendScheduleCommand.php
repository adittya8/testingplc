<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduleCommands;
use App\Models\RemoteTerminal;
use App\Models\SubGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plc:send-schedule-command';

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
        try {
            $now = date('Y-m-d H:i:s');
            $weekdayToday = date('w');
            $tasks = DB::table('dimming_task_schedules as DTS')
                ->join('dimming_tasks as DT', 'DTS.dimming_task_id', '=', 'DT.id')
                ->join('dimming_task_weekdays as DTW', 'DTW.dimming_task_id', '=', 'DT.id')
                ->where(DB::raw("TIME_FORMAT(DTS.time, '%H:%i')"), date('H:i'))
                ->where('DT.date_from', '<=', $now)
                ->where('DT.date_to', '>=', $now)
                ->where('DTW.weekday', $weekdayToday)
                ->where('DT.is_active', 1)
                ->select('DT.id', 'DTS.brightness', 'DT.type')
                ->get();

            $groups = DB::table('dimming_task_groups')->whereIn('dimming_task_id', $tasks->pluck('id'))->get();
            $rtus = RemoteTerminal::whereIn('id', $groups->pluck('rtu_id'))->with('concentrator')->get();
            $subGroups = SubGroup::whereIn('id', $groups->pluck('sub_group_id'))->get();

            $data = [];
            foreach ($tasks as $task) {
                $taskGroups = $groups->whereIn('dimming_task_id', $task->id);
                if ($task->type == 1) {
                    $taskSubGroups = $subGroups->whereIn('id', $taskGroups->pluck('sub_group_id'));
                    foreach ($taskSubGroups as $subGroup) {
                        if (!$subGroup->rtus || !count($subGroup->rtus)) {
                            continue;
                        }

                        foreach ($subGroup->rtus as $rtu) {
                            $data[] = [
                                'dcu_code' => $rtu->concentrator?->concentrator_no,
                                'rtu_code' => $rtu->code,
                                'dcu_id' => $rtu->concentrator?->id,
                                'rtu_id' => $rtu->id,
                                'brightness' => $task->brightness,
                            ];
                        }
                    }
                } else {
                    $taskRtus = $rtus->whereIn('id', $taskGroups->pluck('rtu_id'));
                    foreach ($taskRtus as $rtu) {
                        $data[] = [
                            'dcu_code' => $rtu->concentrator?->concentrator_no,
                            'rtu_code' => $rtu->code,
                            'dcu_id' => $rtu->concentrator?->id,
                            'rtu_id' => $rtu->id,
                            'brightness' => $task->brightness,
                        ];
                    }
                }
            }

            if (count($data)) {
                SendScheduleCommands::dispatch($data);
            }

            Log::info(count($data) . " commands to queue.");
        } catch (Throwable $t) {
            Log::channel('cmd')->error("Schedule command error => {$t->getMessage()}");
        }
    }
}
