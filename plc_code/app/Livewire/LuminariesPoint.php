<?php

namespace App\Livewire;

use App\Enums\AlarmTypes;
use App\Models\Alert;
use App\Models\Luminary;
use App\Models\RemoteTerminal;
use App\Models\Road;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LuminariesPoint extends Component
{
    public $filterMonth = '';
    public $filterRoad = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';

    public function mount()
    {
        $thisMonth = $this->filterMonth && $this->filterMonth != '' ? $this->filterMonth : date('m', );
        $this->filterDateStart = date("Y-{$thisMonth}-01");
        $this->filterDateEnd = date("Y-m-t", strtotime($this->filterDateStart));
    }

    public function render()
    {
        $dateFrom = date('Y-m-d', strtotime('-34 days')) . ' 00:00:00';
        $dateGroups = collect();
        $c = 0;
        for ($i = 0; $i < 7; $i++) {
            $startTimestamp = strtotime("+$c days", strtotime($dateFrom));
            $endTimestamp = strtotime('+' . ($c + 4) . ' days', strtotime($dateFrom));

            $dateGroups->push([
                'start' => date('Y-m-d', $startTimestamp) . ' 00:00:00',
                'end' => date('Y-m-d', $endTimestamp) . ' 23:59:59',
                'label' => date('d M', $startTimestamp) . " - " . date('d M', $endTimestamp),
            ]);
            $c += 5;
        }

        $luminaries = RemoteTerminal::whereBetween('created_at', [$dateGroups->first()['start'], $dateGroups->last()['end']])->select('created_at')->get();
        $luminaryPointsCount = $dateGroups->map(function ($dg) use ($luminaries) {
            return $luminaries->whereBetween('created_at', [$dg['start'], $dg['end']])->count();
        });

        return view('livewire.luminaries-point')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'luminaryPointsCount' => $luminaryPointsCount,
                'luminaryPointsSum' => $luminaryPointsCount->sum(),
                'roads' => Road::all(),
                'labels' => $dateGroups->map(fn($i) => $i['label']),
            ]);
    }
}
