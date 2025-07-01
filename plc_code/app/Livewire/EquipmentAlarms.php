<?php

namespace App\Livewire;

use App\Models\Concentrator;
use App\Models\RemoteTerminal;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentAlarms extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $type = '';
    public $remarks = '';

    public $brand;
    public $editMode = false;
    public $modalTitle = 'Add Brand';

    public function render()
    {
        $dcus = Concentrator::where('project_id', config('project_id'))->get();
        $rtus = RemoteTerminal::where('project_id', config('project_id'))->with('lastReportData', 'concentrator')->get();

        $now = date('Y-m-d H:i:s');
        $alarms = collect();
        $dcuAlarmCount = 0;
        foreach ($dcus as $dcu) {
            if (!isDcuOnline($dcu->status_updated_at)) {
                $alarms->push([
                    'device_code' => $dcu->concentrator_no,
                    'device_type' => 'DCU',
                    'alarm_type' => 'Offline',
                    'road' => $dcu->road?->name,
                    'time' => $now
                ]);
                $dcuAlarmCount++;
            }
        }

        $rtuAlarmCount = 0;
        foreach ($rtus as $rtu) {
            if (!$rtu->lastReportData) {
                $alarms->push([
                    'device_code' => $rtu->code,
                    'device_type' => 'RTU',
                    'alarm_type' => 'No Data Received',
                    'road' => $rtu->concentrator?->road?->name,
                    'time' => $now
                ]);
                $rtuAlarmCount++;
            } elseif (!$rtu->lastReportData?->created_at || !isRtuOnline($rtu->lastReportData->created_at)) {
                $alarms->push([
                    'device_code' => $rtu->code,
                    'device_type' => 'RTU',
                    'alarm_type' => 'Offline',
                    'road' => $rtu->concentrator?->road?->name,
                    'time' => $now
                ]);
                $rtuAlarmCount++;
            } else if ($rtu->last_command_brightness >= 12 && (!$rtu->lastReportData?->created_at || !isRtuOnline($rtu->lastReportData->created_at))) {
                $alarms->push([
                    'device_code' => $rtu->code,
                    'device_type' => 'RTU',
                    'alarm_type' => "<span class='text-danger'>Not Running</span>",
                    'road' => $rtu->concentrator?->road?->name,
                    'time' => $now
                ]);
                $rtuAlarmCount++;
            }
        }

        return view('livewire.equipment-alarms')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'dcus' => $dcus,
                'rtus' => $rtus,
                'alarms' => $alarms,
                'rtuAlarmCount' => $rtuAlarmCount,
                'dcuAlarmCount' => $dcuAlarmCount,
                // 'alarms' => Alert::with('dcu', 'rtu')->paginate(50)
            ]);
    }
}
