<div>
  @section('title', 'Monitor Log')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Monitor Log</h2>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <div class="row">
          <div class="col-md-4 col-lg-3">
            <label for="filterDeviceId" style="font-size: 13px">Device ID:</label>
            <input type="text" wire:model.live="filterDeviceId" id="filterDeviceId" class="form-control"
              placeholder="Search by device ID" value="{{ $filterDeviceId }}">
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

    <div class="position-relative">
      <x-loader wire-target="filterDeviceId" />
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Event ID</th>
            <th>Event</th>
            <th>Zone</th>
            <th>Road</th>
            <th>Device ID</th>
            <th>Event Time</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $d)
            <tr wire:key="{{ $d->id }}">
              <td>{{ $d->id }}</td>
              <td>Heartbeat</td>
              <td>{{ $d->zone }}</td>
              <td>{{ $d->road }}</td>
              <td>{{ $d->device_code }}</td>
              <td>{{ $d->created_at }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-4 mt-2">{{ $data->links() }}</div>
  </div>
</div>
</div>
