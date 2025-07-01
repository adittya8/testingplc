<div>
  @section('title', $pageTitle)

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">{{ $pageTitle }}</h2>
        </div>
        <div class="col-6 text-end">
        </div>
      </div>
    </div>

    <div class="card-body">
      <form wire:submit="{{ $schedule ? 'updateSchedule' : 'storeSchedule' }}" class="row gy-3">
        @csrf

        <div class="col-12 col-md-4">
          <label for="name">Dim Schedule Name</label>
          <input type="text" wire:model.live="name" id="name"
            class="form-control @error('name') is-invalid @enderror" placeholder="Enter schedule name">
          @error('name')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 col-md-4">
          <label for="zone_id">Zone</label>
          <select id="zone_id" wire:model="zone_id" class="form-select" wire:change="updateRoadList" required>
            <option value="">Select zone</option>
            @foreach ($zones as $zone)
              <option value="{{ $zone->id }}">{{ $zone->name }}</option>
            @endforeach
          </select>
          @error('zone_id')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 col-md-4">
          <label for="road_id">Road</label>
          <select wire:model.live="road_id" id="road_id" class="form-select" required>
            <option value="">Select road</option>
            @foreach ($roads as $road)
              <option value="{{ $road->id }}" @selected($road->id == $road_id)>{{ $road->name }}</option>
            @endforeach
          </select>
          @error('road_id')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end">
          @if (!count($dims))
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addDimModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endif
        </div>

        <div class="col-12">
          <table class="table">
            <thead>
              <tr>
                <th>Time</th>
                <th>Dimming Type</th>
                <th>Status</th>
                <th>Operations</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($dims as $key => $dim)
                <tr wire:key="{{ $key }}">
                  <td>{{ $dim['start'] }} {{ isset($dim['end']) ? "- {$dim['end']}" : '' }}</td>
                  <td>{{ \App\Enums\DimTypes::getTextFromValue($dim['type']) }}</td>
                  <td>{{ isset($dim['status']) && $dim['status'] ? 'On' : 'Off' }}</td>
                  <td>
                    <button type="button" class="btn btn-link link-danger text-decoration-none"
                      wire:click.prevent="removeDimItem({{ $key }})" wire:loading.attr="disabled"
                      wire:target="removeDimItem({{ $key }})" title="Delete" data-bs-toggle="tooltip"
                      data-bs-title="Delete">
                      <i class="fas fa-spinner fa-pulse" wire:loading
                        wire:target="removeDimItem({{ $key }})"></i>
                      <i class="fas fa-trash-alt" wire:loading.remove
                        wire:target="removeDimItem({{ $key }})"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="col-12">
          <x-buttons.submit wire-target="{{ $schedule ? 'updateSchedule' : 'storeSchedule' }}" />
        </div>
      </form>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="addDimModal" tabindex="-1" aria-labelledby="addDimModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="addDimModalLabel">Add Dim</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="setDim" class="row gy-3">
            @csrf

            <div class="col-12">
              <div>
                <div class="form-check form-check-inline">
                  <input type="radio" class="form-check-input" value="1" id="timeType-1" checked>
                  <label for="timeType-1" class="form-check-label">Fixed Time</label>
                </div>
              </div>
              @error('time_type')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-6">
              <label for="start_time" class="required">Start Time</label>
              <input type="time" name="start_time" id="start_time"
                class="form-control @error('dimStart') is-invalid @enderror" wire:model="dimStart" required>
              @error('dimStart')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-6">
              <label for="end_time">End Time</label>
              <input type="time" name="end_time" id="end_time"
                class="form-control @error('dimEnd') is-invalid @enderror" wire:model="dimEnd">
              @error('dimEnd')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="end_time">Dim Type</label>
              <select wire:model="dimType" id="dim_type" class="form-control @error('dimType') is-invalid @enderror"
                required>
                <option value="">Select dim type</option>
                @foreach (\App\Enums\DimTypes::cases() as $type)
                  <option value="{{ $type->value }}">{{ $type->getText() }}</option>
                @endforeach
              </select>
              @error('dimType')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="status"
                  wire:model="dimStatus" value="1">
                <label class="form-check-label" for="flexSwitchCheckDefault">Status</label>
              </div>
              @error('dimStatus')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="setDim" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.addEventListener('close-modal', (event) => {
        console.log(event.detail);
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });
    </script>
  @endscript

  <script>
    const roadSelector = document.querySelector('#road_id');
    const zoneSelector = document.querySelector('#zone_id');
    const roads = @json($roads);

    function updateRoadSelect(d = null) {
      roadSelector.innerHTML = '<option value="">Select road</option>'
      const selectedZone = d ?? zoneSelector.value;
      console.log(selectedZone);
      console.log(roads);
      if (!selectedZone) return;
      const roadsToShow = roads.filter(r => r.zone_id == selectedZone);

      roadsToShow.forEach(rd => {
        const opt = document.createElement('option');
        opt.value = rd.id;
        opt.innerHTML = rd.name;
        roadSelector.append(opt);
      });
    }

    zoneSelector.addEventListener('change', () => updateRoadSelect());
  </script>

  @if ($schedule)
    <script>
      updateRoadSelect({{ $zone_id }});
    </script>
  @endif
</div>
