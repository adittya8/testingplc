<?php

namespace App\Livewire;

use App\Models\DimmingTask as DimmingTaskModel;
use App\Models\DimmingTaskCategory;
use Livewire\Attributes\On;
use Livewire\Component;

class DimmingTask extends Component
{
    public function render()
    {
        hasPermissionTo('View Schedule Presets');

        return view('livewire.dimming-task')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'tasks' => DimmingTaskModel::with('weekdays')->paginate(20),
                'categories' => DimmingTaskCategory::all(),
            ]);
    }

    #[On('delete-task')]
    public function deleteTask(string $id)
    {
        hasPermissionTo('Delete Schedule Presets');

        $task = DimmingTaskModel::findOrFail($id);
        $task->delete();

        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Task has been deleted.');
    }
}
