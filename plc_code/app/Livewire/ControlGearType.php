<?php

namespace App\Livewire;

use App\Models\ControlGearType as ControlGearTypeModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ControlGearType extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';

    public $cgType;
    public $editMode = false;
    public $modalTitle = 'Add Control Gear Type';

    public function render()
    {
        return view('livewire.control-gear-type')->with([
            'cgTypes' => ControlGearTypeModel::paginate(10, ['*'], 'control_gear_types'),
        ]);
    }

    public function storeControlGearType()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        ControlGearTypeModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'controlGearTypeFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Control gear type has been added.');
    }

    public function editControlGearType(string $id)
    {
        $this->cgType = ControlGearTypeModel::findOrFail($id);
        $this->name = $this->cgType->name;
        $this->editMode = true;
        $this->modalTitle = 'Edit Control Gear Type';
        $this->dispatch('open-modal', modalId: 'controlGearTypeFormModal');
    }

    public function updateControlGearType()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->cgType->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'controlGearTypeFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Control gear type has been updated.');
        $this->modalTitle = 'Add Control Gear Type';
    }

    #[On('delete-control-gear-type')]
    public function deleteControlGearType(string $id)
    {
        $cgType = ControlGearTypeModel::findOrFail($id);
        if ($cgType->luminaryTypes && count($cgType->luminaryTypes)) {
            $this->dispatch('show-toast', type: 'success', message: 'Cannot delete. One or more luminary types depend on this control gear type.');
            return;
        }

        $cgType->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Control gear type has been deleted.');
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
            'type' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter control gear type name.',
        ];
    }
}
