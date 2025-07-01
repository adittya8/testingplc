<?php

namespace App\Livewire;

use App\Models\Alert;
use App\Models\RemoteTerminal;
use Livewire\Component;
use Livewire\WithPagination;

class LuminariesLifespan extends Component
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
        return view('livewire.luminaries-lifespan')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'rtus' => RemoteTerminal::where('project_id', config('project_id'))
                    ->with('lastReportData', 'pole', 'concentrator', 'subGroup.group')
                    ->paginate(50)
            ]);
    }
}
