<?php

namespace App\Livewire;

use App\Models\DimmingSchedule as DimmingScheduleModel;
use Livewire\Attributes\On;
use Livewire\Component;

class DimmingSchedule extends Component
{
    public function render()
    {
        return view('livewire.dimming-schedule')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'schedules' => DimmingScheduleModel::with('road.zone')->paginate(10)
            ]);
    }

    #[On('delete-schedule')]
    public function deleteSchedule(string $id)
    {
        $schedule = DimmingScheduleModel::findOrFail($id);
        if ($schedule->dimmingTasks && count($schedule->dimmingTasks)) {
            $this->dispatch('show-toast', type: 'success', message: 'Cannot delete. One or more dimming tasks depend on this schedule.');
            return;
        }

        $schedule->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Dimming schedule has been deleted.');
    }
}
