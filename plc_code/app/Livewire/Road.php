<?php

namespace App\Livewire;

use App\Models\Zone;
use App\Models\Road as RoadModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Road extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $zone_id = '';
    public $grade = '';
    public $length = '';
    public $remarks = '';

    public $road;
    public $editMode = false;
    public $modalTitle = 'Add Road';

    public function render()
    {
        return view('livewire.road')->with([
            'roads' => RoadModel::withCount('poles', 'luminaries')->paginate(20, ['*'], 'roads'),
            'zones' => Zone::all(),
        ]);
    }

    public function storeRoad()
    {
        $validated = $this->validate($this->validationRules(), $this->validationRules());
        RoadModel::create($validated + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'roadFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Road has been added.');
    }

    public function editRoad(string $id)
    {
        $this->road = RoadModel::findOrFail($id);
        $this->name = $this->road->name;
        $this->zone_id = $this->road->zone_id;
        $this->grade = $this->road->grade;
        $this->length = $this->road->length;
        $this->remarks = $this->road->remarks;

        $this->editMode = true;
        $this->modalTitle = 'Edit Road';
        $this->dispatch('open-modal', modalId: 'roadFormModal');
    }

    public function updateRoad()
    {
        $validated = $this->validate($this->validationRules(), $this->validationRules());
        $this->road->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'roadFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Road has been updated.');
        $this->modalTitle = 'Add Road';
    }

    #[On('delete-road')]
    public function deleteRoad(string $id)
    {
        $road = RoadModel::findOrFail($id);
        if ($road->poles && count($road->poles)) {
            $count = count($road->poles);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'poles are' : 'pole is') . ' assigned to this road. Please delete or reassign the poles first.');
            return;
        }

        $road->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Road has been deleted.');
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
            'zone_id' => 'required|exists:zones,id',
            'grade' => 'nullable|string|max:255',
            'length' => 'nullable|numeric|max:99999999',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter concentrator name.',
            'zone_id.required' => 'Please select a zone.',
            'zone_id.exists' => 'Selected zone could not be found.',
            'length.required' => 'Length must be a number.',
        ];
    }
}
