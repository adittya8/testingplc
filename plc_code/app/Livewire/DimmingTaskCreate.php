<?php

namespace App\Livewire;

use App\Models\DimmingSchedule;
use App\Models\DimmingTask;
use App\Models\DimmingTaskWeekday;
use App\Models\SubGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class DimmingTaskCreate extends Component
{
    public DimmingTask $task;
    public $name = '';
    public $weekdays = [0, 1, 2, 3, 4, 5, 6];
    public $dates;
    public $groupTasks = [];
    public $lumSubGroup = '';
    public $lumDimSchedule = '';
    public $lumLuminaryNo = '';
    public $lumDcuNo = '';
    public $luminaryType = 1;
    public $pageTitle = 'Add Dimming Task';

    public function mount($task = null)
    {
        if ($task) {
            $this->task = $task;
            $this->name = $task->name;
            $this->dates = date('Y-m-d', strtotime($task->date_from)) . " - " . date('Y-m-d', strtotime($task->date_to));
            $this->weekdays = $task->weekdays && count($task->weekdays) ? $task->weekdays->map(fn($i) => $i['weekday']) : [];
            // if ($task->schedules) {
            //     foreach ($task->schedules as $sch) {
            //         dd($sch);
            //         $this->groupTasks[$sch->id] = [
            //             "dimming_task_id" => $sch->dimming_task_id,
            //             "dimming_schedule_id" => $sch->pivot->dimming_schedule_id,
            //             "sub_group_id" => $sch->pivot->sub_group_id,
            //             "luminary_id" => $sch->pivot->luminary_id,
            //         ];
            //     }
            // }
            $this->pageTitle = 'Edit Dimming Task';
        } else {
            $this->dates = date('Y-m-') . '01 - ' . date('Y-m-t');
        }
    }

    public function render()
    {
        return view('livewire.dimming-task-create')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'subGroups' => SubGroup::all(),
                'schedules' => DimmingSchedule::all(),
            ]);
    }

    public function setLuminary()
    {
        $this->validate([
            'lumSubGroup' => 'required|exists:sub_groups,id',
            'lumDimSchedule' => 'nullable|exists:dimming_schedules,id',
            'lumLuminaryNo' => 'nullable|exists:luminaries,node_id',
            'lumDcuNo' => 'nullable|exists:concentrators,concentrator_no',
        ]);
        $data = [
            'dimming_schedule_id' => $this->lumDimSchedule,
        ];

        if ($this->luminaryType == 1) {
            $data += [
                'sub_group_id' => $this->lumSubGroup,
            ];
        } else {
            $data += [
                'luminary_no' => $this->lumLuminaryNo,
                'concentrator_no' => $this->lumDcuNo,
            ];
        }

        $this->groupTasks[$this->lumDimSchedule] = $data;

        $this->dispatch('close-modal', modalId: 'addGroupTaskModal');
    }

    public function storeTask()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $dates = explode(' - ', $validated['dates']);

        DB::beginTransaction();
        try {
            $task = DimmingTask::create($validated + [
                'name' => $this->name,
                'date_from' => date('Y-m-d 00:00:00', strtotime($dates[0])),
                'date_to' => date('Y-m-d 23:59:59', strtotime($dates[1] ?? $dates[0])),
                'project_id' => config('project_id'),
            ]);
            foreach ($validated['weekdays'] as $wkd) {
                DimmingTaskWeekday::create([
                    'dimming_task_id' => $task->id,
                    'weekday' => $wkd,
                ]);
            }
            DB::commit();

            to_route('dimming-task', ['project' => config('project_id')])
                ->with('success', 'Dimming task has been added.');
        } catch (Throwable $t) {
            DB::rollBack();
            dd($t);
            Log::error("Error during dimming task store => {$t->getMessage()}");

            $this->dispatch('show-toast', type: 'danger', message: 'Something went wrong!');
        }
    }


    public function updateTask()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $dates = explode(' - ', $validated['dates']);

        DB::beginTransaction();
        try {
            $this->task->update($validated + [
                'name' => $this->name,
                'date_from' => date('Y-m-d 00:00:00', strtotime($dates[0])),
                'date_to' => date('Y-m-d 23:59:59', strtotime($dates[1] ?? $dates[0])),
            ]);
            DB::table('dimming_task_weekdays')->where('dimming_task_id', $this->task->id)->delete();
            foreach ($validated['weekdays'] as $wkd) {
                DimmingTaskWeekday::create([
                    'dimming_task_id' => $this->task->id,
                    'weekday' => $wkd,
                ]);
            }
            DB::commit();

            to_route('dimming-task', ['project' => config('project_id')])
                ->with('success', 'Dimming task has been updated.');
        } catch (Throwable $t) {
            DB::rollBack();
            Log::error("Error during dimming task update => {$t->getMessage()}");

            $this->dispatch('show-toast', type: 'danger', message: 'Something went wrong!');
        }
    }

    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'dates' => 'required|string',
            'weekdays' => 'required|array',
            'weekdays.*' => 'required|integer|between:0,6',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter schedule task name.',
            'road_id.required' => 'Please select a road.',
            'road_id.exists' => 'Selected road could not be found.',
            'weekdays.required' => 'Please select at least 1 weekday.',
            'weekdays.*.integer' => 'At least on of the selected weekdays is invalid.',
            'weekdays.*.between' => 'At least on of the selected weekdays is invalid.',
        ];
    }
}
