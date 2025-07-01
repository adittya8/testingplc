<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\LightSourceType;
use App\Models\LuminaryType as LuminaryTypeModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class LuminaryType extends Component
{
    use WithPagination;

    public $model = '';
    public $brand_id = '';
    public $light_source_type_id = '';
    public $rated_power = '';
    public $avg_life = '';
    public $remarks = '';

    protected $paginationTheme = 'bootstrap';
    public $editMode = false;
    public $luminaryType = null;
    public $modalTitle = 'Add Luminary Type';

    public function render()
    {
        return view('livewire.luminary-type')->with([
            'luminaryTypes' => LuminaryTypeModel::paginate(20, ['*'], 'luminary_types'),
            'brands' => Brand::all(),
            'lightSourceTypes' => LightSourceType::all(),
        ]);
    }

    public function storeLuminaryType()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        LuminaryTypeModel::create($validated + [
            'project_id' => config('project_id'),
        ]);

        $this->reset();
        $this->dispatch('close-modal', modalId: 'ltFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary type has been added.');
    }

    public function editLuminaryType(string $id)
    {
        $this->luminaryType = LuminaryTypeModel::findOrFail($id);
        $this->model = $this->luminaryType->model;
        $this->brand_id = $this->luminaryType->brand_id;
        $this->light_source_type_id = $this->luminaryType->light_source_type_id;
        $this->rated_power = $this->luminaryType->rated_power;
        $this->avg_life = $this->luminaryType->avg_life;
        $this->remarks = $this->luminaryType->remarks;

        $this->editMode = true;
        $this->modalTitle = 'Edit Luminary Type';
        $this->dispatch('open-modal', modalId: 'ltFormModal');
    }

    public function updateLuminaryType()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->luminaryType->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'ltFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary type has been added.');
        $this->modalTitle = 'Add Luminary Type';
    }

    #[On('delete-luminary-type')]
    public function deleteLuminaryType(string $id)
    {
        $lt = LuminaryTypeModel::findOrFail($id);
        // if ($lt->luminaryTypes && count($lt->luminaryTypes)) {
        //     $this->dispatch('show-toast', type: 'success', message: 'Cannot delete. One or more luminary types depend on this brand.');
        //     return;
        // }

        $lt->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary type has been deleted.');
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules()
    {
        return [
            'model' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'light_source_type_id' => 'required|exists:light_source_types,id',
            'rated_power' => 'nullable|string|max:255',
            'avg_life' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'model.required' => 'Please enter model.',
            'brand_id.required' => 'Please select a brand.',
            'brand_id.exists' => 'Select brand could not be found.',
            'light_source_type_id.required' => 'Please select a light source type.',
            'light_source_type_id.exists' => 'Select light source type could not be found.',
        ];
    }
}
