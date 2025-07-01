<div>
  @section('title', 'Poles')
  @php
    $canCreate = auth()->user()->can('Create Poles');
    $canEdit = auth()->user()->can('Edit Poles');
    $canDelete = auth()->user()->can('Delete Poles');
    $canDim = auth()->user()->can('Dim Poles');
  @endphp
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Poles</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#poleFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <div class="row align-items-end gy-3">
          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_pole_code" style="font-size: 13px">Name</label>
            <input type="text" wire:model.live="filter_pole_code" id="filter_pole_code" class="form-control"
              placeholder="Search by pole name" value="{{ $filter_pole_code }}">
          </div>

          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_road" style="font-size: 13px">Road</label>
            <select wire:model.live="filter_road" id="filter_road" class="form-select">
              <option value="">All</option>
              @foreach ($roads as $road)
                <option value="{{ $road->id }}" @selected($road->id == $filter_road)>{{ $road->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_pole_code" style="font-size: 13px">DCU No.</label>
            <input type="text" wire:model.live="filter_dcu_no" id="filter_dcu_no" class="form-control"
              value="{{ $filter_dcu_no }}" placeholder="Search by DCU number">
          </div>

          <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
              wire:target="filter_pole_code, filter_road, filter_dcu_no">
              <i class="fas fa-spinner fa-pulse" wire:loading
                wire:target="filter_pole_code, filter_road, filter_dcu_no"></i>
              <i class="fas fa-search" wire:loading.remove
                wire:target="filter_pole_code, filter_road, filter_dcu_no"></i>
              Search
            </button>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table text-center mb-0">
          <thead>
            <tr>
              <th>Pole Name</th>
              <th>Pole Type</th>
              <th>Zone</th>
              <th>Road</th>
              <th>DCU</th>
              {{-- <th>Longitude</th>
              <th>Latitude</th> --}}
              <th>Location</th>
              <th class="td-actions">Operations</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($poles as $pole)
              <tr wire:key="{{ $pole->id }}">
                <td>{{ $pole->code }}</td>
                <td>{{ $pole->poleType?->name }}</td>
                <td>{{ $pole->road?->zone?->name }}</td>
                <td>{{ $pole->road?->name }}</td>
                <td>
                  {{ $pole->concentrator ? "{$pole->concentrator->name} ({$pole->concentrator->concentrator_no})" : '' }}
                </td>
                {{-- <td>{{ $pole->lon }}</td>
                <td>{{ $pole->lat }}</td> --}}
                <td>{{ $pole->location }}</td>
                <td>
                  <div class="table-action-block">
                    @if ($canEdit)
                      <x-buttons.tbl-edit wire-target="editPole" model-id="{{ $pole->id }}" />
                    @endif
                    @if ($canDelete)
                      <x-buttons.tbl-delete model-id="{{ $pole->id }}" function-name="deletePole" />
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center">Nothing to show</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-4 mt-2">{{ $poles->links() }}</div>
    </div>
  </div>

  @if ($canCreate || $canEdit)
    <div wire:ignore.self class="modal fade" id="poleFormModal" tabindex="-1" aria-labelledby="poleFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="poleFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updatePole' : 'storePole' }}" class="row gy-3">
              @csrf

              <div class="col-12 col-md-6">
                <label for="poleAddCode" class="required">Name</label>
                <input type="text" wire:model.live="code" id="poleAddCode"
                  class="form-control @error('code') is-invalid @enderror" placeholder="Enter pole name" required>
                @error('code')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="poleAddPoleTypeId" class="required">Type</label>
                <select wire:model.live="pole_type_id" id="poleAddPoleTypeId"
                  class="form-select @error('pole_type_id') is-invalid @enderror">
                  <option value="">Select pole type</option>
                  @foreach ($poleTypes as $pt)
                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                  @endforeach
                </select>
                @error('pole_type_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="poleAddRoadId" class="required">Road</label>
                <select wire:model.live="road_id" id="poleAddRoadId"
                  class="form-select @error('road_id') is-invalid @enderror">
                  <option value="">Select road</option>
                  @foreach ($roads as $road)
                    <option value="{{ $road->id }}">{{ $road->name }}</option>
                  @endforeach
                </select>
                @error('road_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="poleAddConcentratorId" class="required">DCU</label>
                <select wire:model.live="concentrator_id" id="poleAddConcentratorId"
                  class="form-select @error('concentrator_id') is-invalid @enderror">
                  <option value="">Select DCU</option>
                  @foreach ($concentrators as $concentrator)
                    <option value="{{ $concentrator->id }}">
                      {{ $concentrator->name }} ({{ $concentrator->concentrator_no }})
                    </option>
                  @endforeach
                </select>
                @error('concentrator_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              {{-- <div class="col-12 col-md-6">
                <label for="poleAddLatitude">Latitude</label>
                <input type="number" wire:model.live="lat" id="poleAddLatitude" step=".0001"
                  class="form-control @error('lat') is-invalid @enderror" placeholder="Enter pole latitude">
                @error('lat')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="poleAddLongitude">Longitude</label>
                <input type="number" wire:model.live="lon" id="poleAddLongitude" step=".0001"
                  class="form-control @error('lon') is-invalid @enderror" placeholder="Enter pole longitude">
                @error('lon')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div> --}}

              <div class="col-12">
                <label for="poleAddLocation">Location</label>
                <input type="text" wire:model.live="location" id="poleAddLocation"
                  class="form-control @error('location') is-invalid @enderror" placeholder="Enter pole location">
                @error('Location')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updatePole' : 'storePole' }}" />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif

  @if ($canDelete)
    @script
      <script>
        window.deletePole = (poleId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this pole`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-pole', {
                id: poleId
              });
            },
            () => {}, {
              titleColor: colors.danger,
            }
          );
        }
      </script>
    @endscript
  @endif

  @if ($canCreate || $canEdit)
    @script
      <script>
        const poleModal = document.querySelector('#poleFormModal')
        poleModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif

  @script
    <script>
      window.addEventListener('close-modal', (event) => {
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });
    </script>
  @endscript
</div>
