<?php

namespace App\Livewire;

use App\Models\Concentrator as ConcentratorModel;
use App\Models\Road;
use App\Models\SchedulePreset;
use App\Services\RTUService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Concentrator extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $road_id = '';
    public $road_name = '';
    public $zone_name = '';
    public $concentrator_no = '';
    public $sim_no = '';
    public $lat = '';
    public $lon = '';
    public $location = '';
    public $remarks = '';
    public $preset_id = '';

    public $concentrator;
    public $editMode = false;
    public $modalTitle = 'Add DCU';

    public $time = [];

    public function render()
    {
        hasPermissionTo('View DCUs');

        return view('livewire.concentrator')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'concentrators' => ConcentratorModel::paginate(25, ['*'], 'dcus'),
                'roads' => Road::all(),
                'presets' => SchedulePreset::where('project_id', config('project_id'))->get(),
            ]);
    }

    public function storeConcentrator()
    {
        hasPermissionTo('Create DCUs');

        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        ConcentratorModel::create(array_map(fn($v) => $v === '' ? null : $v, $validated) + [
            'project_id' => config('project_id'),
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'concentratorFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'DCU has been added.');
    }

    public function editConcentrator(string $id)
    {
        $this->setFields($id);
        $this->editMode = true;
        $this->modalTitle = 'Edit DCU';
        $this->dispatch('open-modal', modalId: 'concentratorFormModal');
    }

    public function showConcentrator(string $id)
    {
        $this->setFields($id);
        $this->dispatch('open-modal', modalId: 'dcuDetailsModal');
    }

    private function setFields(string $id)
    {
        $this->concentrator = ConcentratorModel::findOrFail($id);
        $this->name = $this->concentrator->name;
        $this->road_id = $this->concentrator->road_id;
        $this->road_name = $this->concentrator->road?->name;
        $this->zone_name = $this->concentrator->road?->zone?->name;
        $this->concentrator_no = $this->concentrator->concentrator_no;
        $this->sim_no = $this->concentrator->sim_no;
        $this->lat = $this->concentrator->lat;
        $this->lon = $this->concentrator->lon;
        $this->location = $this->concentrator->location;
        $this->remarks = $this->concentrator->remarks;
    }

    public function updateConcentrator()
    {
        hasPermissionTo('Edit DCUs');

        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->concentrator->update(array_map(fn($v) => $v === '' ? null : $v, $validated));

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'concentratorFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'DCU has been updated.');
        $this->modalTitle = 'Add DCU';
    }

    #[On('delete-concentrator')]
    public function deleteConcentrator(string $id)
    {
        hasPermissionTo('Delete DCUs');

        $concentrator = ConcentratorModel::findOrFail($id);
        if ($concentrator->poles && count($concentrator->poles)) {
            $this->dispatch('show-toast', type: 'success', message: 'Cannot delete. One or more poles depend on this DCU.');
            return;
        }

        $concentrator->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'DCU has been deleted.');
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
            'road_id' => 'nullable|exists:roads,id',
            'concentrator_no' => 'required|string|max:255',
            'sim_no' => 'nullable|string|max:25',
            'lat' => 'nullable|numeric|min:-90|max:90',
            'lon' => 'nullable|numeric|min:-180|max:180',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter DCU name.',
            'road_id.required' => 'Please select a road.',
            'road_id.exists' => 'Selected road could not be found.',
            'concentrator_no.required' => 'Please enter DCU number.',
        ];
    }

    #[On('sync-rtus')]
    public function syncRtus(string $id)
    {
        $dcu = ConcentratorModel::find($id);
        if (!$dcu) {
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'danger', message: 'DCU not found!');
            return;
        }

        $response = RTUService::syncRtuToDcu($id);
        if ($response['code'] == 200) {
            $dcu->update([
                'synced_at' => date('Y-m-d H:i:s'),
                'synced_by' => Auth::id()
            ]);
            $this->dispatch('hide-loader');
            $this->dispatch('show-toast', type: 'success', message: $response['message']);
            return;
        }

        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'danger', message: $response['message']);
    }
}
