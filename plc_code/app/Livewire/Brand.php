<?php

namespace App\Livewire;

use App\Models\Brand as BrandModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Brand extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $type = '';
    public $remarks = '';

    public $brand;
    public $editMode = false;
    public $modalTitle = 'Add Brand';

    public function render()
    {
        return view('livewire.brand')->with([
            'brands' => BrandModel::paginate(20, ['*'], 'brands'),
        ]);
    }

    public function storeBrand()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        BrandModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'brandFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Brand has been added.');
    }

    public function editBrand(string $id)
    {
        $this->brand = BrandModel::findOrFail($id);
        $this->name = $this->brand->name;
        $this->type = $this->brand->type;
        $this->editMode = true;
        $this->modalTitle = 'Edit Brand';
        $this->dispatch('open-modal', modalId: 'brandFormModal');
    }

    public function updateBrand()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->brand->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'brandFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Brand has been updated.');
        $this->modalTitle = 'Add Brand';
    }

    #[On('delete-brand')]
    public function deleteBrand(string $id)
    {
        $brand = BrandModel::findOrFail($id);
        if ($brand->luminaryTypes && count($brand->luminaryTypes)) {
            $this->dispatch('show-toast', type: 'success', message: 'Cannot delete. One or more luminary types depend on this brand.');
            return;
        }

        $brand->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Brand has been deleted.');
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
            'name.required' => 'Please enter brand name.',
        ];
    }
}
