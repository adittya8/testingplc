<?php

namespace App\Livewire;

use App\Models\RemoteTerminal;
use App\Models\Road;
use App\Models\SubGroup;
use Livewire\Component;
use Livewire\WithPagination;

class LampData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $filterNodeId = '';
    public $filterRoad = '';
    public $filterSubGroup = '';
    public $filterDcuNo = '';

    public function render()
    {
        // dd(RemoteTerminal::where('project_id', config('project_id'))
        //     ->with('concentrator', 'lastReportData')
        //     ->when($this->filterNodeId, fn($q) => $q->whereLike('node_id', "%{$this->filterNodeId}%"))
        //     ->when($this->filterSubGroup, fn($q) => $q->where('sub_group_id', $this->filterSubGroup))
        //     ->when($this->filterDcuNo, function ($q) {
        //         $q->whereHas('concentrator', function ($q) {
        //             $q->where('concentrators.concentrator_no', $this->filterDcuNo)
        //                 ->orWhere('concentrators.name', $this->filterDcuNo);
        //         });
        //     })->where('code', '5F1001018A')->first());
        return view('livewire.lamp-data')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'rtus' => RemoteTerminal::where('project_id', config('project_id'))
                    ->with('concentrator', 'lastReportData')
                    ->when($this->filterNodeId, fn($q) => $q->whereLike('node_id', "%{$this->filterNodeId}%"))
                    ->when($this->filterSubGroup, fn($q) => $q->where('sub_group_id', $this->filterSubGroup))
                    ->when($this->filterDcuNo, function ($q) {
                        $q->whereHas('concentrator', function ($q) {
                            $q->where('concentrators.concentrator_no', $this->filterDcuNo)
                                ->orWhere('concentrators.name', $this->filterDcuNo);
                        });
                    })
                    ->paginate(50),
                'roads' => Road::all(),
                'subGroups' => SubGroup::all(),
            ]);
    }
}
