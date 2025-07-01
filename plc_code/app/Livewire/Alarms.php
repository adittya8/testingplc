<?php

namespace App\Livewire;

use App\Enums\AlarmTypes;
use App\Models\Alert;
use App\Models\Concentrator;
use App\Models\Road;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Alarms extends Component
{
    public $filterDates = '';
    public $filterDateStart = '';
    public $filterDateTimeStart = '';
    public $filterDateEnd = '';
    public $filterDateTimeEnd = '';
    public $filterRoad = '';

    public function mount()
    {
        $this->filterDateStart = date('Y-m-d', strtotime('-30 days'));
        $this->filterDateTimeStart = "{$this->filterDateStart} 00:00:00";
        $this->filterDateEnd = date('Y-m-d');
        $this->filterDateTimeEnd = "{$this->filterDateEnd} 23:59:59";
        $this->filterDates = "{$this->filterDateStart} - {$this->filterDateEnd}";
    }

    public function render()
    {
        if ($this->filterDates) {
            $dates = explode(' - ', $this->filterDates);
            if (isset($dates[0])) {
                $this->filterDateStart = date('Y-m-d', strtotime($dates[0]));
                $this->filterDateTimeStart = "{$this->filterDateStart} 00:00:00";
            }
            if (isset($dates[1])) {
                $this->filterDateEnd = date('Y-m-d', strtotime($dates[1]));
                $this->filterDateTimeEnd = "{$this->filterDateEnd} 23:59:59";
            }
        }

        $startDt = new DateTime($this->filterDateTimeStart);
        $endDt = new DateTime($this->filterDateTimeEnd);
        $diff = $endDt->diff($startDt)->format("%a") + 1;
        $groupCount = $diff / 5 + ($diff % 5 > 0 ? 1 : 0);

        $dateGroups = collect();
        $c = 0;
        for ($i = 0; $i < $groupCount; $i++) {
            $startTimestamp = strtotime("+$c days", strtotime($this->filterDateTimeStart));
            $endTimestamp = strtotime('+' . ($c + 4) . ' days', strtotime($this->filterDateTimeStart));

            $dateGroups->push([
                'start' => date('Y-m-d', $startTimestamp) . ' 00:00:00',
                'end' => date('Y-m-d', $endTimestamp) . ' 23:59:59',
                'label' => date('d M', $startTimestamp) . " - " . date('d M', $endTimestamp),
            ]);
            $c += 5;
        }

        $alarmCounts = $dateGroups->map(function ($dg) {
            $count = Alert::whereBetween('created_at', [$dg['start'], $dg['end']])
                ->select(
                    DB::raw("COUNT(CASE WHEN rtu_code IS NULL THEN 1 END) as concentrator"),
                    DB::raw("COUNT(CASE WHEN dcu_code IS NULL THEN 1 END) as luminary")
                )->first();

            return $dg += [
                'concentrator_count' => $count->concentrator,
                'luminary_count' => $count->luminary,
            ];
        });

        $alarmTypesCounts = Alert::whereBetween('created_at', [$this->filterDateTimeStart, $this->filterDateTimeEnd])
            ->whereNull('dcu_code')
            ->select(
                // DB::raw("COUNT(CASE WHEN alert_type = " . AlarmTypes::OVERCURRENT->value . " THEN 1 END) as over_current"),
                // DB::raw("COUNT(CASE WHEN alert_type = " . AlarmTypes::UNDERCURRENT->value . " THEN 1 END) as under_current"),
                DB::raw("COUNT(CASE WHEN alert_type = " . AlarmTypes::OVERVOLTAGE->value . " THEN 1 END) as over_voltage"),
                DB::raw("COUNT(CASE WHEN alert_type = " . AlarmTypes::UNDERVOLTAGE->value . " THEN 1 END) as under_voltage"),
            )->first();
        $alarmTypes = array_map(function ($i) {
            return [
                'text' => $i->getText(),
                'color' => $i::getColorFromValue($i->value),
            ];
        }, AlarmTypes::cases());

        return view('livewire.alarms')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'roads' => Road::all(),
                'alarmCounts' => $alarmCounts,
                'alarmTypesCounts' => $alarmTypesCounts,
                'alarmTypes' => $alarmTypes,
                'dcus' => Concentrator::where('project_id', config('project_id'))
                    ->where('status_updated_at', '<', date('Y-m-d H:i:s', strtotime('-5 minutes')))
                    ->paginate(20, ['*'], 'dcus')
            ]);
    }
}
