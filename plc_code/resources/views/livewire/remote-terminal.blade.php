<div>
  @section('title', 'RTUs')
  @php
    $canCreate = auth()->user()->can('Create RTUs');
    $canEdit = auth()->user()->can('Edit RTUs');
    $canDelete = auth()->user()->can('Delete RTUs');
    $canDim = auth()->user()->can('Dim RTUs');
  @endphp
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">RTUs</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
              data-bs-target="#terminalFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endif
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      {{-- <form action="#" class="row align-items-end gy-3 px-4 mb-4" wire:submit="filterRtu">
        <div class="col-12 col-md-4 col-lg-3">
          <label for="filterDcuNo" class="form-label mb-1" style="font-size: 13px">Month</label>
          <input type="text" wire:model.live="filterDcuNo" id="filterDcuNo" class="form-control"
            placeholder="Search by DCU number">
        </div>

        <div class="col-12 col-md-4 col-lg-3">
          <label for="filterSubGroup" class="form-label mb-1" style="font-size: 13px">Sub-Group</label>
          <select wire:model.live="filterSubGroup" id="filterSubGroup" class="form-select">
            <option value="">All</option>
            @foreach ($subGroups as $sg)
              <option value="{{ $sg->id }}" @selected($filterSubGroup == $sg->id)>{{ $sg->name }}</option>
            @endforeach
          </select>
        </div>
      </form> --}}

      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>DCU</th>
            {{-- <th>Latitude</th>
            <th>Longitude</th>
            <th>Location</th> --}}
            <th>Pole</th>
            <th>Sub-group</th>
            <th>Brand</th>
            <th>Rated Power</th>
            <th>Remarks</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($terminals as $terminal)
            <tr wire:key="{{ $terminal->id }}">
              <td>{{ $terminal->name }} ({{ $terminal->code }})</td>
              <td>
                {{ $terminal->concentrator->name ? "{$terminal->concentrator->name} ({$terminal->concentrator->concentrator_no})" : $terminal->concentrator->concentrator_no }}
              </td>
              {{-- <td>{{ $terminal->lat }}</td>
              <td>{{ $terminal->lon }}</td>
              <td>{{ $terminal->location }}</td> --}}
              <td>{{ $terminal->pole?->code }}</td>
              <td>{{ $terminal->subGroup?->name }}
                {{ $terminal->subGroup?->group?->name ? "({$terminal->subGroup?->group?->name})" : '' }}</td>
              <td>{{ $terminal->brand?->name }}</td>
              <td>{{ $terminal->rated_power }}</td>
              <td>{{ $terminal->remarks }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editTerminal" model-id="{{ $terminal->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $terminal->id }}" function-name="deleteTerminal" />
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
      <div class="px-4 mt-2">{{ $terminals->links() }}</div>
    </div>
  </div>

  @if ($canEdit || $canCreate)
    <div wire:ignore.self class="modal fade" id="terminalFormModal" tabindex="-1"
      aria-labelledby="terminalFormModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="terminalFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateTerminal' : 'storeTerminal' }}"
              class="row gy-3">
              @csrf

              <div class="col-12 col-md-6">
                <label for="terminalAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="terminalAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddCode" class="required">Device Code</label>
                <input type="text" wire:model.live="code" id="terminalAddCode"
                  class="form-control @error('code') is-invalid @enderror" placeholder="Enter code" required>
                @error('code')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddConcentratorId" class="required">DCU</label>
                <select wire:model.live="concentrator_id" id="terminalAddConcentratorId"
                  class="form-select @error('concentrator_id') is-invalid @enderror">
                  <option value="">Select DCU</option>
                  @foreach ($concentrators as $concentrator)
                    <option value="{{ $concentrator->id }}">{{ $concentrator->name }}</option>
                  @endforeach
                </select>
                @error('concentrator_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddPoleId">Pole</label>
                <select wire:model.live="pole_id" id="terminalAddPoleId"
                  class="form-select @error('pole_id') is-invalid @enderror">
                  <option value="">Select Pole</option>
                  @foreach ($poles as $pole)
                    <option value="{{ $pole->id }}">{{ $pole->code }}</option>
                  @endforeach
                </select>
                @error('pole_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddSubGroupId">Sub Group</label>
                <select wire:model.live="sub_group_id" id="terminalAddSubGroupId"
                  class="form-select @error('sub_group_id') is-invalid @enderror">
                  <option value="">Select Sub Group</option>
                  @foreach ($subGroups as $sg)
                    <option value="{{ $sg->id }}">
                      {{ $sg->name }} {{ $sg->group?->name ? "({$sg->group?->name})" : '' }}
                    </option>
                  @endforeach
                </select>
                @error('sub_group_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddRatedPower">Rated Power</label>
                <input type="text" wire:model.live="rated_power" id="terminalAddRatedPower"
                  class="form-control @error('rated_power') is-invalid @enderror" placeholder="Enter rated power">
                @error('rated_power')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddBrand">Brand</label>
                <select type="text" wire:model.live="brand_id" id="terminalAddBrand"
                  class="form-select @error('brand_id') is-invalid @enderror">
                  <option value="">---</option>
                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
                @error('brand_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="terminalAddRemarks">Remarks</label>
                <input type="text" wire:model.live="remarks" id="terminalAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              {{-- <div class="col-12 col-md-6">
              <label for="concentratorAddLatitude">Latitude</label>
              <input type="number" wire:model.live="lat" id="concentratorAddLatitude" step=".0001"
                class="form-control @error('lat') is-invalid @enderror" placeholder="Enter DCU latitude">
              @error('lat')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="concentratorAddLongitude">Longitude</label>
              <input type="number" wire:model.live="lon" id="concentratorAddLongitude" step=".0001"
                class="form-control @error('lon') is-invalid @enderror" placeholder="Enter DCU longitude">
              @error('lon')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="concentratorAddLocation">Location</label>
              <input type="text" wire:model.live="location" id="concentratorAddLocation"
                class="form-control @error('location') is-invalid @enderror" placeholder="Enter DCU location">
              @error('Location')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div> --}}

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateTerminal' : 'storeTerminal' }}" />
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
        window.deleteTerminal = (terminalId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this RTU`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-terminal', {
                id: terminalId
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
        const TerminalModal = document.querySelector('#terminalFormModal')
        TerminalModal.addEventListener('hidden.bs.modal', event => {
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
