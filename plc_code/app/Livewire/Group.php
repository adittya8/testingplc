<?php

namespace App\Livewire;

use App\Models\Group as GroupModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Group extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $remarks = '';

    public $group;
    public $editMode = false;
    public $modalTitle = 'Add Group';

    public function render()
    {
        return view('livewire.group')->with([
            'groups' => GroupModel::withCount('subGroups', 'rtus')->paginate(20, ['*'], 'groups'),
        ]);
    }

    public function storeGroup()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        GroupModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'groupFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Group has been added.');
    }

    public function editGroup(string $id)
    {
        $this->group = GroupModel::findOrFail($id);
        $this->name = $this->group->name;
        $this->remarks = $this->group->remarks;
        $this->editMode = true;
        $this->modalTitle = 'Edit Group';
        $this->dispatch('open-modal', modalId: 'groupFormModal');
    }

    public function updateGroup()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->group->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'groupFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Group has been updated.');
        $this->modalTitle = 'Add Group';
    }

    #[On('delete-group')]
    public function deleteGroup(string $id)
    {
        $group = GroupModel::findOrFail($id);
        if ($group->subGroups && count($group->subGroups)) {
            $count = count($group->subGroups);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'sub-groups are' : 'sub-group is') . ' assigned to this group. Please delete or reassign the sub-groups first.');
            return;
        }

        $group->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Group has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter brand name.',
        ];
    }
}
