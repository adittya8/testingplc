<?php

namespace App\Livewire;

use App\Enums\DimTypes;
use App\Models\DimmingSchedule;
use App\Models\Road;
use App\Models\Zone;
use Illuminate\Validation\Rule;
use Livewire\Component;

class DimmingScheduleCreate extends Component
{
    public DimmingSchedule $schedule;
    public $dims = [];
    public $name = '';
    public $zone_id = '';
    public $road_id = '';
    public $dimStart = '';
    public $dimEnd = '';
    public $dimType = '';
    public $dimStatus = '';
    public $roads = [];
    public $pageTitle = 'Add Dimming Schedule';

    public function mount($schedule = null)
    {
        if ($schedule) {
            $this->schedule = is_numeric($schedule) ? DimmingSchedule::findOrFail($schedule) : $schedule;
            $this->name = $this->schedule->name;
            $this->road_id = $this->schedule->road_id;
            $this->zone_id = $this->schedule->road?->zone_id;
            $this->dims[] = [
                'start' => $this->schedule->start_time,
                'end' => $this->schedule->end_time,
                'type' => $this->schedule->dimming_type,
                'status' => $this->schedule->status,
            ];
            $this->roads = Road::where('zone_id', $this->zone_id)->get();
            $this->pageTitle = 'Edit Dimming Schedule';
        }
    }

    public function render()
    {
        return view('livewire.dimming-schedule-create')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'zones' => Zone::where('project_id', config('project_id'))->get(),
            ]);
    }

    public function setDim()
    {
        $this->validate([
            'dimStart' => 'required|date_format:H:i',
            'dimEnd' => 'nullable|date_format:H:i|gte:dimStart',
            'dimType' => ['required', Rule::enum(DimTypes::class)],
        ]);
        $this->dims[] = [
            'start' => $this->dimStart,
            'end' => $this->dimEnd,
            'type' => $this->dimType,
            'status' => $this->dimStatus,
        ];

        $this->dispatch('close-modal', modalId: 'addDimModal');
    }

    public function removeDimItem($key)
    {
        if (isset($this->dims[$key])) {
            unset($this->dims[$key]);
        }
    }

    public function updateRoadList()
    {
        $this->roads = Road::where('project_id', config('project_id'))
            ->where('zone_id', $this->zone_id)
            ->get();
    }

    public function storeSchedule()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());

        DimmingSchedule::create($validated + [
            'dimming_type' => $this->dimType,
            'start_time' => $this->dimStart,
            'end_time' => $this->dimEnd ?: null,
            'status' => $this->dimStatus,
        ]);

        to_route('dimming-schedule', ['project' => config('project_id')])
            ->with('success', 'Dimming schedule has been added.');
    }

    public function updateSchedule()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());

        $this->schedule->update($validated + [
            'dimming_type' => $this->dimType,
            'start_time' => $this->dimStart,
            'end_time' => $this->dimEnd,
            'status' => $this->dimStatus,
        ]);

        to_route('dimming-schedule', ['project' => config('project_id')])
            ->with('success', 'Dimming schedule has been updated.');
    }

    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'road_id' => 'required|exists:roads,id',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter schedule name.',
            'road_id.required' => 'Please select a road.',
            'road_id.exists' => 'Select road could not be found.',
        ];
    }
}
