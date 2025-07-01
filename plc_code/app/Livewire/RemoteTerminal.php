<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Concentrator;
use App\Models\Pole;
use App\Models\RemoteTerminal as RemoteTerminalModel;
use App\Models\Road;
use App\Models\SubGroup;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RemoteTerminal extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $code = '';
    public $concentrator_id = '';
    public $pole_id = '';
    public $sub_group_id = '';
    public $lat = '';
    public $lon = '';
    public $location = '';
    public $remarks = '';
    public $rated_power = '';
    public $brand_id = '';

    public $filterDcuNo = '';
    public $filterSubGroup = '';
    public $filterRoad = '';

    public $terminal;
    public $editMode = false;
    public $modalTitle = 'Add RTU';

    public function render()
    {
        return view('livewire.remote-terminal')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'terminals' => RemoteTerminalModel::with('concentrator', 'brand', 'subGroup.group')
                    ->where('project_id', config('project_id'))
                    ->paginate(25, ['*'], 'terminals'),
                'concentrators' => Concentrator::where('project_id', config('project_id'))->get(),
                'poles' => Pole::where('project_id', config('project_id'))->get(),
                'subGroups' => SubGroup::where('project_id', config('project_id'))->get(),
                'roads' => Road::where('project_id', config('project_id'))->get(),
                'brands' => Brand::where('project_id', config('project_id'))->orderBy('name')->get()
            ]);
    }

    public function storeTerminal()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        RemoteTerminalModel::create(array_map(fn($value) => $value === '' ? null : $value, $validated) + [
            'project_id' => config('project_id')
        ]);

        $this->reset();
        $this->dispatch('close-modal', modalId: 'terminalFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'RTU has been added.');
    }

    public function editTerminal(string $id)
    {
        $this->terminal = RemoteTerminalModel::findOrFail($id);
        $this->name = $this->terminal->name;
        $this->code = $this->terminal->code;
        $this->concentrator_id = $this->terminal->concentrator_id;
        $this->pole_id = $this->terminal->pole_id;
        $this->sub_group_id = $this->terminal->sub_group_id;
        $this->lat = $this->terminal->lat;
        $this->lon = $this->terminal->lon;
        $this->location = $this->terminal->location;
        $this->remarks = $this->terminal->remarks;
        $this->rated_power = $this->terminal->rated_power;
        $this->brand_id = $this->terminal->brand_id;

        $this->editMode = true;
        $this->modalTitle = 'Edit RTU';
        $this->dispatch('open-modal', modalId: 'terminalFormModal');
    }

    public function updateTerminal()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->terminal->update(array_map(fn($value) => $value === '' ? null : $value, $validated));

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'terminalFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'RTU has been updated.');
        $this->modalTitle = 'Add RTU';
    }

    #[On('delete-terminal')]
    public function deleteTerminal(string $id)
    {
        $terminal = RemoteTerminalModel::findOrFail($id);

        $terminal->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'RTU has been deleted.');
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
            'code' => 'required|string|max:255',
            'concentrator_id' => 'required|exists:concentrators,id',
            'pole_id' => 'nullable|exists:poles,id',
            'sub_group_id' => 'nullable|exists:sub_groups,id',
            'brand_id' => 'nullable|exists:brands,id',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'rated_power' => 'nullable|integer|gt:0',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter name.',
            'code.required' => 'Please enter code.',
            'concentrator_id.required' => 'Please select a DCU.',
            'concentrator_id.exists' => 'Selected DCU could not be found.',
            'pole_id.exists' => 'Selected Pole could not be found.',
            'sub_group_id.exists' => 'Selected Sub Group could not be found.',
            'brand_id.exists' => 'Selected Brand could not be found.',
            'lat.numeric' => 'Please enter a valid latitude.',
            'lon.numeric' => 'Please enter a valid longitude.',
            'rated_power.integer' => 'Please enter a number.',
            'rated_power.gt' => 'Please enter a number greater than 0.',
        ];
    }
}
