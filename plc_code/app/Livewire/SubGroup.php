<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\SubGroup as SubGroupModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SubGroup extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $group_id = '';
    public $remarks = '';

    public $subGroup;
    public $editMode = false;
    public $modalTitle = 'Add Sub-Group';

    public function render()
    {
        return view('livewire.sub-group')->with([
            'subGroups' => SubGroupModel::withCount('rtus')->paginate(20, ['*'], 'sub_groups'),
            'groups' => Group::all(),
        ]);
    }

    public function storeSubGroup()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        SubGroupModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'subGroupFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Sub-group has been added.');
    }

    public function editSubGroup(string $id)
    {
        $this->subGroup = SubGroupModel::findOrFail($id);
        $this->name = $this->subGroup->name;
        $this->group_id = $this->subGroup->group_id;
        $this->remarks = $this->subGroup->remarks;
        $this->editMode = true;
        $this->modalTitle = 'Edit Sub-Group';
        $this->dispatch('open-modal', modalId: 'subGroupFormModal');
    }

    public function updateSubGroup()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->subGroup->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'subGroupFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Sub-group has been updated.');
        $this->modalTitle = 'Add Sub-Group';
    }

    #[On('delete-sub-group')]
    public function deleteSubGroup(string $id)
    {
        $subGroup = SubGroupModel::findOrFail($id);
        if ($subGroup->luminaries && count($subGroup->luminaries)) {
            $count = count($subGroup->luminaries);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'luminaries are' : 'luminary is') . ' assigned to this sub-group. Please delete or reassign the luminaries first.');
            return;
        }

        if ($subGroup->rtus && count($subGroup->rtus)) {
            $count = count($subGroup->rtus);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'RTUs are' : 'RTU is') . ' assigned to this sub-group. Please delete or reassign the RTUs first.');
            return;
        }

        $subGroup->delete();

        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Sub-group has been deleted.');
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    // public function editSubGroupDevices(string $id)
    // {
    //     $subGroup = SubGroupModel::findOrFail($id);
    //     $subGroup
    // }

    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter sub-group name.',
            'group_id.required' => 'Please select a group.',
            'group_id.exists' => 'Selected group could not be found.',
        ];
    }
}
