<?php

namespace App\Livewire;

use App\Models\Zone as ZoneModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Zone extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $remarks = '';

    public $zone;
    public $editMode = false;
    public $modalTitle = 'Add Zone';

    public function render()
    {
        return view('livewire.zone')->with([
            'zones' => ZoneModel::withCount('roads')->paginate(20, ['*'], 'zones'),
        ]);
    }

    public function storeZone()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        ZoneModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'zoneFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Zone has been added.');
    }

    public function editZone(string $id)
    {
        $this->zone = ZoneModel::findOrFail($id);
        $this->name = $this->zone->name;
        $this->remarks = $this->zone->remarks;
        $this->editMode = true;
        $this->modalTitle = 'Edit Zone';
        $this->dispatch('open-modal', modalId: 'zoneFormModal');
    }

    public function updateZone()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->zone->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'zoneFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Zone has been updated.');
        $this->modalTitle = 'Add Zone';
    }

    #[On('delete-zone')]
    public function deleteZone(string $id)
    {
        $zone = ZoneModel::findOrFail($id);
        if ($zone->roads && count($zone->roads)) {
            $count = count($zone->roads);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'roads are' : 'road is') . ' assigned to this zone. Please delete or reassign the roads first.');
            return;
        }
        if ($zone->concentrators && count($zone->concentrators)) {
            $count = count($zone->concentrators);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'DCUs are' : 'DCU is') . ' assigned to this zone. Please delete or reassign the DCUs first.');
            return;
        }

        $zone->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Zone has been deleted.');
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
            'name.required' => 'Please enter zone name.',
        ];
    }
}
