<div>
  @section('title', 'Lamp Data')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Lamp Data</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <div class="row">
          {{-- <div class="col-md-6 col-lg-3">
            <div class="row mb-3">
              <label for="filter_node_id" class="col-2 col-form-label pe-0" style="font-size: 13px">Node ID:</label>
              <div class="col-10">
                <input type="text" wire:model.live="filterNodeId" id="filter_node_id" class="form-control"
                  placeholder="Search by node ID" value="{{ $filterNodeId }}">
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="row mb-3">
              <label for="filter_road" class="col-2 col-form-label pe-0" style="font-size: 13px">Road:</label>
              <div class="col-10">
                <select wire:model.live="filterRoad" id="filter_road" class="form-select">
                  <option value="">All</option>
                  @foreach ($roads as $road)
                    <option value="{{ $road->id }}" @selected($road->id == $filterRoad)>{{ $road->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3">
            <div class="row mb-3">
              <label for="filter_sub_group" class="col-2 col-md-3 col-form-label pe-0" style="font-size: 13px">Sub
                Group:</label>
              <div class="col-10 col-md-9">
                <select wire:model.live="filterSubGroup" id="filter_sub_group" class="form-select">
                  <option value="">All</option>
                  @foreach ($subGroups as $sg)
                    <option value="{{ $sg->id }}" @selected($sg->id == $filterSubGroup)>{{ $sg->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div> --}}

          <div class="col-md-4 col-lg-3">
            <label for="filter_dcu_no" style="font-size: 13px">DCU No:</label>
            <input type="text" wire:model.live="filterDcuNo" id="filter_dcu_no" class="form-control"
              value="{{ $filterDcuNo }}" placeholder="Search by DCU number">
          </div>
        </div>

        {{-- <div class="col-12 col-md-3">
          <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
            wire:target="filter_pole_code, filter_road, filter_dcu_no">
            <i class="fas fa-spinner fa-spin-pulse" wire:loading wire:loading.attr="disabled"
              wire:target="filter_pole_code, filter_road, filter_dcu_no"></i> Search
          </button>
        </div> --}}
      </div>
    </div>

    <table class="table text-center mb-0">
      <thead>
        <tr>
          <th>Status</th>
          <th>DCU No.</th>
          {{-- <th>Pole Code</th> --}}
          <th>RTU No.</th>
          {{-- <th>Point</th> --}}
          <th>Brightness</th>
          <th>Voltage</th>
          <th>Current</th>
          <th>Power</th>
          <th>Work Time</th>
          <th>Power Consumption</th>
          {{-- <th>PF</th> --}}
          <th>Updated At</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rtus as $key => $rtu)
          <tr wire:key="{{ $rtu->id }}">
            <td>
              @if (isRtuOnline($rtu->lastReportData?->created_at))
                <span class="text-success">Online</span>
              @else
                <span class="text-danger">Offline</span>
              @endif
            </td>
            <td>
              {{ $rtu->concentrator ? "{$rtu->concentrator?->name} ({$rtu->concentrator?->concentrator_no})" : '' }}
            </td>
            {{-- <td>{{ $rtu->pole->code }}</td> --}}
            <td>{{ $rtu->name }} ({{ $rtu->code }})</td>
            {{-- <td>{{ $rtu->lampData?->point }}</td> --}}
            <td>
              {{ $rtu->lastReportData?->main_light_brightness ? number_format($rtu->lastReportData?->main_light_brightness) . '%' : '' }}
            </td>
            <td>{{ $rtu->lastReportData?->voltage ? number_format($rtu->lastReportData?->voltage) . 'V' : '' }}
            </td>
            <td>
              {{ $rtu->lastReportData?->main_light_current ? number_format($rtu->lastReportData?->main_light_current, 2) . 'A' : '' }}
            </td>
            <td>
              {{ $rtu->lastReportData?->main_light_power ? number_format($rtu->lastReportData?->main_light_power) . 'W' : '' }}
            </td>
            <td>
              {{ $rtu->lastReportData?->running_time ? "{$rtu->lastReportData?->running_time} min" : '' }}
            </td>
            <td>
              @if ($rtu->lastReportData?->running_time && $rtu->lastReportData?->main_light_power)
                {{ number_format(($rtu->lastReportData->running_time / 60) * $rtu->lastReportData->main_light_power, 2) }}W
              @endif
            </td>
            {{-- <td>{{ $ld->lampData?->pf }}</td> --}}
            <td>
              {{ $rtu->lastReportData?->created_at ? date('M d, Y H:i:s', strtotime($rtu->lastReportData?->created_at)) : '' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="text-center">Nothing to show</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-4 mt-2">{{ $rtus->links() }}</div>
  </div>
</div>
</div>
