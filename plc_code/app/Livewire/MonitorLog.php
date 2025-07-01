<?php

namespace App\Livewire;

use App\Models\Alert;
use App\Models\Concentrator;
use App\Models\Remote\DeviceData;
use App\Models\ReportData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MonitorLog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $filterDeviceId = '';

    public function render()
    {
        // $page = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = 10;
        // $dataCount = DeviceData::count();
        // $data = DeviceData::orderByDesc('created_at')
        //     ->when($this->filterDeviceId, function ($q) {
        //         $devId = implode("", array_reverse(str_split($this->filterDeviceId)));
        //         $q->where('device_id', 'like', "%$devId%");
        //     })
        //     ->with('device')
        //     ->skip(($page - 1) * $perPage)
        //     ->take(10)
        //     ->get();
        // $devIds = $data->pluck('device_id')->map(fn($i) => implode("", array_reverse(str_split($i, 2))));
        // $dcus = Concentrator::whereIn('concentrator_no', $devIds)->with('road.zone')->get();

        // $events = collect();
        // foreach ($data as $d) {
        //     $dcu = $dcus->where('concentrator_no', implode("", array_reverse(str_split($d->device_id, 2))))->first();
        //     $events->push((object) [
        //         'id' => $d->id,
        //         'event' => $d->event_type,
        //         'zone' => $dcu?->road?->zone?->name,
        //         'road' => $dcu?->road?->name,
        //         'device_id' => $dcu?->concentrator_no,
        //         'event_time' => $d->created_at,
        //     ]);
        // }

        // $paginatedData = new LengthAwarePaginator(
        //     $events,
        //     $dataCount,
        //     $perPage,
        //     $page,
        //     ['path' => LengthAwarePaginator::resolveCurrentPath()]
        // );

        // $data = ReportData::orderByDesc('created_at')->paginate(50);
        // $alerts = Alert::orderByDesc('created_at')->paginate(50);
        $data = DB::table('device_data as DD')
            ->join('concentrators as C', 'C.concentrator_no', '=', 'DD.device_code')
            ->leftJoin('roads as RD', 'C.road_id', '=', 'RD.id')
            ->leftJoin('zones as Z', 'RD.zone_id', '=', 'Z.id')
            ->when($this->filterDeviceId, fn($q) => $q->where('DD.device_code', $this->filterDeviceId))
            ->orderByDesc('DD.id')
            ->select('DD.id', 'DD.created_at', 'DD.device_code', 'C.name', 'C.concentrator_no', 'RD.name as road', 'Z.name as zone')
            ->paginate(50);

        return view('livewire.monitor-log')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'data' => $data
            ]);
    }
}
