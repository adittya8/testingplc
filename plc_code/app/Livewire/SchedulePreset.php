<?php

namespace App\Livewire;

use App\Models\SchedulePreset as SchedulePresetModel;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SchedulePreset extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $schedule = [];
    public $time_1 = '00:00';
    public $brightness_1 = '';
    public $time_2 = '';
    public $brightness_2 = '';
    public $time_3 = '';
    public $brightness_3 = '';
    public $time_4 = '';
    public $brightness_4 = '';
    public $time_5 = '';
    public $brightness_5 = '';
    public $time_6 = '';
    public $brightness_6 = '';
    public $schedulePreset;
    public $editMode = false;
    public $modalTitle = 'Add Preset';
    public $timeAscendingError = '';

    public function render()
    {
        return view('livewire.schedule-preset')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'presets' => SchedulePresetModel::where('project_id', config('project_id'))
                    ->paginate(10, ['*'], 'presets'),
            ]);
    }

    public function storeSchedulePreset()
    {
        $fields = ['name' => $this->name];
        $schedule = [];
        for ($i = 1; $i <= 6; $i++) {
            $fields["time_$i"] = $this->{"time_$i"};
            $fields["brightness_$i"] = $this->{"brightness_$i"};

            if (isset($this->{"time_" . ($i + 1)}) && strtotime($this->{"time_$i"}) >= strtotime($this->{"time_" . ($i + 1)})) {
                $this->timeAscendingError = 'Times must be in ascending order.';
                return;
            } else {
                $this->timeAscendingError = '';
            }

            $schedule[] = [
                'time' => $this->{"time_$i"},
                'brightness' => $this->{"brightness_$i"},
            ];
        }

        Validator::make($fields, $this->validationRules(), $this->validationMessages())->validate();
        SchedulePresetModel::create([
            'name' => $this->name,
            'schedule' => $schedule,
            'project_id' => config('project_id'),
        ]);
        $this->reset();
        $this->dispatch('close-modal', modalId: 'presetFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Preset has been added.');
    }

    public function editSchedulePreset(string $id)
    {
        $this->schedulePreset = SchedulePresetModel::findOrFail($id);
        $this->name = $this->schedulePreset->name;
        $this->schedule = $this->schedulePreset->schedule;
        foreach ($this->schedulePreset->schedule as $key => $time) {
            $i = $key + 1;
            $this->{"time_$i"} = $time['time'];
            $this->{"brightness_$i"} = $time['brightness'];
        }

        $this->editMode = true;
        $this->modalTitle = 'Edit Preset';
        $this->dispatch('open-modal', modalId: 'presetFormModal');
    }

    public function updateSchedulePreset()
    {
        $fields = ['name' => $this->name];
        $schedule = [];
        for ($i = 1; $i <= 6; $i++) {
            $fields["time_$i"] = $this->{"time_$i"};
            $fields["brightness_$i"] = $this->{"brightness_$i"};

            if (isset($this->{"time_" . ($i + 1)}) && strtotime($this->{"time_$i"}) >= strtotime($this->{"time_" . ($i + 1)})) {
                $this->timeAscendingError = 'Times must be in ascending order.';
                return;
            } else {
                $this->timeAscendingError = '';
            }

            $schedule[] = [
                'time' => $this->{"time_$i"},
                'brightness' => $this->{"brightness_$i"},
            ];
        }

        Validator::make($fields, $this->validationRules(), $this->validationMessages())->validate();
        $this->schedulePreset->update([
            'name' => $this->name,
            'schedule' => $schedule,
        ]);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'presetFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Preset has been updated.');
        $this->modalTitle = 'Add Preset';
    }

    #[On('delete-preset')]
    public function deleteSchedulePreset(string $id)
    {
        $schedulePreset = SchedulePresetModel::findOrFail($id);
        $schedulePreset->delete();

        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Preset has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $rules["time_$i"] = 'required|date_format:H:i';
            $rules["brightness_$i"] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!($value == 0 || ($value >= 12 && $value <= 100))) {
                        $fail("Brightness must be either 0 or between 12 and 100.");
                    }
                }
            ];
        }

        return $rules;
    }

    private function validationMessages()
    {
        $messages = [
            'name.required' => 'Please enter name.',
            'schedule.required' => 'Please enter times.',
            'schedule.array' => 'Invalid data.',
            'schedule.size' => 'Please provide exactly :size times.',
            'schedule.*.time.required' => 'Please enter time.',
            'schedule.*.time.time' => 'Please enter a valid time.',
            'schedule.*.brightness.required' => 'Please enter brightness.',
            'schedule.*.brightness.integer' => 'Brightness must be between 12 and 100.',
            'schedule.*.brightness.between' => 'Brightness must be between 12 and 100.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $messages["time_$i.required"] = 'Please enter time.';
            $messages["time_$i.time"] = 'Please enter a valid time.';
            $messages["brightness_$i.required"] = 'Please enter brightness.';
            $messages["brightness_$i.integer"] = 'Please enter a number.';
        }

        return $messages;
    }
}
