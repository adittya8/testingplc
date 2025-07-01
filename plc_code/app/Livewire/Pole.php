<?php

namespace App\Livewire;

use App\Models\Concentrator;
use App\Models\Pole as PoleModel;
use App\Models\PoleType;
use App\Models\Road;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Pole extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $code = '';
    public $road_id = '';
    public $concentrator_id = '';
    public $pole_type_id = '';
    public $lon = '';
    public $lat = '';
    public $location = '';

    public $pole;
    public $editMode = false;
    public $modalTitle = 'Add Pole';

    public $filter_pole_code;
    public $filter_road;
    public $filter_dcu_no;


    public function render()
    {
        return view('livewire.pole')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'poles' => PoleModel::when($this->filter_pole_code, fn($q) => $q->whereLike('code', "%{$this->filter_pole_code}%"))
                    ->when($this->filter_road, fn($q) => $q->where('poles.road_id', $this->filter_road))
                    ->when($this->filter_dcu_no, function ($q) {
                        $q->join('concentrators', 'poles.concentrator_id', '=', 'concentrators.id')
                            ->whereLike('concentrators.concentrator_no', "%{$this->filter_dcu_no}%");
                    })
                    ->select('poles.*')
                    ->paginate(10, ['*'], 'poles'),
                'concentrators' => Concentrator::all(),
                'roads' => Road::all(),
                'poleTypes' => PoleType::all(),
            ]);
    }

    public function storePole()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        PoleModel::create(array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $validated) + [
            'serial' => PoleModel::max('serial') + 1,
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'poleFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Pole has been added.');
    }

    public function editPole(string $id)
    {
        $this->pole = PoleModel::findOrFail($id);
        $this->code = $this->pole->code;
        $this->pole_type_id = $this->pole->pole_type_id;
        $this->road_id = $this->pole->road_id;
        $this->concentrator_id = $this->pole->concentrator_id;
        $this->lat = $this->pole->lat;
        $this->lon = $this->pole->lon;
        $this->location = $this->pole->location;

        $this->editMode = true;
        $this->modalTitle = 'Edit Pole';
        $this->dispatch('open-modal', modalId: 'poleFormModal');
    }

    public function updatePole()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->pole->update(array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $validated));

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'poleFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Pole has been updated.');
        $this->modalTitle = 'Add Pole';
    }

    #[On('delete-pole')]
    public function deletePole(string $id)
    {
        $pole = PoleModel::findOrFail($id);

        if ($pole->luminaries && count($pole->luminaries)) {
            $count = count($pole->luminaries);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'luminaries are' : 'luminary is') . ' assigned to this pole. Please delete or reassign the luminaries first.');
            return;
        }

        if ($pole->rtus && count($pole->rtus)) {
            $count = count($pole->rtus);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: "Cannot delete. $count " . ($count > 1 ? 'RTUs are' : 'RTU is') . ' assigned to this pole. Please delete or reassign the RTUs first.');
            return;
        }

        $pole->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Pole has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules()
    {
        return [
            'code' => 'required|string|max:255',
            'road_id' => 'required|exists:roads,id',
            'concentrator_id' => 'required|exists:concentrators,id',
            'pole_type_id' => 'required|exists:pole_types,id',
            'lat' => 'nullable|numeric|min:-90|max:90',
            'lon' => 'nullable|numeric|min:-180|max:180',
            'location' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'code.required' => 'Please enter pole code.',
            'road_id.required' => 'Please select a road.',
            'road_id.exists' => 'Selected road could not be found.',
            'concentrator_id.required' => 'Please select a DCU.',
            'concentrator_id.exists' => 'Selected DCU could not be found.',
            'pole_type_id.required' => 'Please select a pole type.',
            'pole_type_id.exists' => 'Selected pole type could not be found.',
        ];
    }
}
