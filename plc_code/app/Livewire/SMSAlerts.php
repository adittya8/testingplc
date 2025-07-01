<?php

namespace App\Livewire;

use App\Models\RemoteTerminal;
use App\Models\ReportData;
use App\Models\Road;
use App\Models\SmsAlert;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SMSAlerts extends Component
{
    public $filterMonth = '';
    public $filterRoad = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';

    public function mount()
    {
        $this->filterDateStart = date("Y-{$this->filterMonth}-01");
        $this->filterDateEnd = date("Y-m-t", strtotime($this->filterDateStart));
    }

    public function render()
    {
        return view('livewire.sms-alerts')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'alerts' => SmsAlert::orderByDesc('created_at')->paginate(25),
            ]);
    }
}
