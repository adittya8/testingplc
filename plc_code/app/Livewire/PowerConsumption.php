<?php

namespace App\Livewire;

use App\Models\Road;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PowerConsumption extends Component
{
    public $filterMonth = '';
    public $filterRoad = '';
    public $dateStart = '';
    public $dateEnd = '';
    public $chartData = [];
    public $totalPowerConsumption = 0;

    public function mount()
    {
        $this->setStartEndDates();
        $this->chartData = $this->getChartData();
        $this->totalPowerConsumption = isset($this->chartData['datasets'][0]['data']) && $this->chartData['datasets'][0]['data'] instanceof Collection
            ? $this->chartData['datasets'][0]['data']->sum()
            : 0;
    }

    public function render()
    {
        hasPermissionTo('View Power Consumption');

        return view('livewire.power-consumption')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'roads' => Road::where('project_id', config('project_id'))->get(),
            ]);
    }

    public function filterChart()
    {
        $this->setStartEndDates();
        $data = $this->getChartData();
        $this->totalPowerConsumption = isset($data['datasets'][0]['data']) && $data['datasets'][0]['data'] instanceof Collection
            ? $data['datasets'][0]['data']->sum()
            : 0;

        $this->dispatch('updateChart', $data);
    }

    private function setStartEndDates()
    {
        $this->dateStart = $this->filterMonth != '' ? date("Y-{$this->filterMonth}-01 00:00:00") : date('Y-m-d 00:00:00', strtotime('-29 days'));
        $this->dateEnd = $this->filterMonth != '' ? date("Y-m-t 00:00:00", strtotime($this->dateStart)) : date('Y-m-d 23:59:59');
    }

    private function getChartData()
    {
        $dateFrom = $this->dateStart;
        $date1 = new DateTime($this->dateStart);
        $date2 = new DateTime($this->dateEnd);
        $diff = $date1->diff($date2)->days + 1;
        $dateGroups = collect();
        $parts = ceil($diff / 5);
        $parts = $parts > 6 ? 6 : $parts;

        for ($i = 0; $i < $parts; $i++) {
            $dateFromTS = strtotime($dateFrom);
            $endTs = $i + 1 == $parts ? strtotime($this->dateEnd) : strtotime('+4 days', $dateFromTS);
            $dateGroups->push([
                'start' => $dateFrom,
                'end' => date('Y-m-d 23:59:59', $endTs),
                'label' => date('d M', $dateFromTS) . " - " . date('d M', $endTs)
            ]);

            $dateFrom = date('Y-m-d 00:00:00', strtotime('+5 days', strtotime($dateFrom)));
        }

        $powerConsumptions = $dateGroups->map(function ($dg, $i) {
            $count = DB::table('power_consumptions as PC')
                ->when($this->filterRoad, function ($q) {
                    $q->join('concentrators as C', 'C.concentrator_no', '=', 'PC.dcu_code')
                        ->where('C.road_id', $this->filterRoad);
                })
                ->whereBetween('PC.device_time', [$dg['start'], $dg['end']])
                ->sum('PC.power');

            return $dg += [
                'total_consumption' => $count,
            ];
        });

        return [
            'labels' => $dateGroups->map(fn($i) => $i['label']),
            'datasets' => [[
                'label' => 'Power Consumption',
                'data' => $powerConsumptions->map(fn($i) => $i['total_consumption']),
                'fill' => false,
                'borderColor' => 'rgb(75, 192, 192)',
            ]]
        ];
    }
}
