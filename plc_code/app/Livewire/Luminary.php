<?php

namespace App\Livewire;

use App\Models\Concentrator;
use App\Models\ControlGearType;
use App\Models\LampType;
use App\Models\Luminary as LuminaryModel;
use App\Models\LuminaryType;
use App\Models\Pole;
use App\Models\RemoteTerminal;
use App\Models\SubGroup;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Luminary extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $lamp_type_id = '';
    public $node_id = '';
    public $pole_id = '';
    public $concentrator_id = '';
    public $rtu_id = '';
    public $luminary_type_id = '';
    public $sub_group_id = '';
    public $control_gear_type_id = '';
    public $installation_status = '';
    public $remarks = '';
    public $rated_power = '';

    public $luminary;
    public $editMode = false;
    public $modalTitle = 'Add Luminary';
    public function render()
    {
        return view('livewire.luminary')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'luminaries' => LuminaryModel::with('lampType', 'concentrator', 'subGroup', 'luminaryType', 'controlGearType')
                    ->paginate(50, ['*'], 'luminaries'),
                'concentrators' => Concentrator::whereHas('road', fn($q) => $q->where('roads.project_id', config('project_id')))->get(),
                'rtus' => RemoteTerminal::where('project_id', config('project_id'))->get(),
                'luminary_types' => LuminaryType::all(),
                'lamp_types' => LampType::all(),
                'sub_groups' => SubGroup::all(),
                'control_gear_types' => ControlGearType::all(),
                'poles' => Pole::all(),
            ]);
    }

    public function storeLuminary()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        LuminaryModel::create(array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $validated));
        $this->reset();
        $this->dispatch('close-modal', modalId: 'luminaryFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary has been added.');
    }

    public function editLuminary(string $id)
    {
        $this->luminary = LuminaryModel::findOrFail($id);
        $this->name = $this->luminary->name;
        $this->node_id = $this->luminary->node_id;
        $this->lamp_type_id = $this->luminary->lamp_type_id;
        $this->pole_id = $this->luminary->pole_id;
        $this->concentrator_id = $this->luminary->concentrator_id;
        $this->sub_group_id = $this->luminary->sub_group_id;
        $this->luminary_type_id = $this->luminary->luminary_type_id;
        $this->control_gear_type_id = $this->luminary->control_gear_type_id;
        $this->installation_status = $this->luminary->installation_status;
        $this->remarks = $this->luminary->remarks;
        $this->rated_power = $this->luminary->rated_power;
        $this->rtu_id = $this->luminary->rtu_id;

        $this->editMode = true;
        $this->modalTitle = 'Edit Luminary';
        $this->dispatch('open-modal', modalId: 'luminaryFormModal');
    }

    public function updateLuminary()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->luminary->update(array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $validated));

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'luminaryFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary has been updated.');
        $this->modalTitle = 'Add Luminary';
    }

    #[On('delete-luminary')]
    public function deleteLuminary(string $id)
    {
        $luminary = LuminaryModel::findOrFail($id);

        $luminary->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Luminary has been deleted.');
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
            'node_id' => 'nullable|string|max:255',
            'lamp_type_id' => 'nullable|exists:lamp_types,id',
            'pole_id' => 'nullable|exists:poles,id',
            'concentrator_id' => 'nullable|exists:concentrators,id',
            'rtu_id' => 'required|exists:remote_terminals,id',
            'sub_group_id' => 'nullable|exists:sub_groups,id',
            'luminary_type_id' => 'nullable|exists:luminary_types,id',
            'control_gear_type_id' => 'nullable|exists:control_gear_types,id',
            'installation_status' => 'required|in:0,1',
            'remarks' => 'nullable|string|max:255',
            'rated_power' => 'nullable|integer|gt:0'
        ];
    }

    private function validationMessages()
    {
        return [
            'node_id.required' => 'Please enter name.',
            'lamp_type_id.exists' => 'Selected lamp type could not be found..',
            'lamp_type_id.required' => 'Please select a lamp type.',
            'pole_id.required' => 'Please select a pole.',
            'concentrator_id.required' => 'Please select a DCU.',
            'concentrator_id.exists' => 'Selected DCU could not be found.',
            'sub_group_id.required' => 'Please select a sub group.',
            'sub_group_id.exists' => 'Selected sub group could not be found.',
            'luminary_type_id.required' => 'Please select a luminary.',
            'luminary_type_id.exists' => 'Selected Luminary type could not be found.',
            'control_gear_type_id.required' => 'Please select a control gear.',
            'control_gear_type_id.exists' => 'Selected congrol gear type could not be found.',
            'installation_status.required' => 'Please select a installation status.',
            'installation_status.exists' => 'Installation status could not be found.',
            'rated_power.integer' => 'Please enter a number.',
            'rated_power.gt' => 'Please enter a value greater than 0.',
        ];
    }
}
