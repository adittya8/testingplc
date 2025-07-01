<div>
  @section('title', 'Luminaries')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Luminaries</h2>
        </div>
        <div class="col-6 text-end">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#luminaryFormModal">
            <i class="fas fa-plus"></i> Add
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Status</th>
            <th>Node ID</th>
            {{-- <th>Lamp Type</th> --}}
            <th>RTU</th>
            <th>Sub Group</th>
            {{-- <th>Luminary Type</th>
            <th>Control Gear Type</th> --}}
            <th>Brightness</th>
            <th>Voltage</th>
            <th>Current</th>
            <th>Rated Power</th>
            <th>Power</th>
            <th>Date</th>
            <th>Installation Status</th>
            <th>Remarks</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($luminaries as $luminary)
            <tr wire:key="{{ $luminary->id }}">
              <td>
                @if ($luminary->rtu?->lastReportData?->created_at < date('Y-m-d H:i:s', strtotime('-5 minutes')))
                  <span class="text-danger">Offline</span>
                @else
                  <span class="text-success">Online</span>
                @endif
              </td>
              <td>{{ $luminary->node_id }}</td>
              {{-- <td>{{ $luminary->lampType?->name }}</td> --}}
              <td>{{ $luminary->rtu?->name }} {{ $luminary->rtu?->code ? "({$luminary->rtu->code})" : '' }}</td>
              <td>{{ $luminary->rtu?->subGroup?->name }}</td>
              {{-- <td>{{ $luminary->luminaryType->model }}</td>
              <td>{{ $luminary->controlGearType->name }}</td> --}}
              <td>
                {{ $luminary->rtu?->lastReportData?->main_light_color_temp ? number_format($luminary->rtu?->lastReportData?->main_light_color_temp) . '%' : '' }}
              </td>
              <td>
                {{ $luminary->rtu?->lastReportData?->voltage ? number_format($luminary->rtu?->lastReportData?->voltage) . 'V' : '' }}
              </td>
              <td>
                {{ $luminary->rtu?->lastReportData?->main_light_current ? $luminary->rtu?->lastReportData?->main_light_current . 'A' : '' }}
              </td>
              <td>{{ $luminary->rated_power ? "{$luminary->rated_power}W" : '' }}</td>
              <td>
                {{ $luminary->rtu?->lastReportData?->main_light_power ? number_format($luminary->rtu?->lastReportData?->main_light_power) . 'W' : '' }}
              </td>
              <td>
                @if ($luminary->rtu?->lastReportData?->created_at)
                  {{ date('Y-m-d', strtotime($luminary->rtu?->lastReportData?->created_at)) }} <br>
                  {{ date('H:i:s', strtotime($luminary->rtu?->lastReportData?->created_at)) }}
                @endif
              </td>
              <td>{{ $luminary->installation_status == 1 ? 'Installed' : 'Not Installed' }}</td>
              <td>{{ $luminary->remarks }}</td>
              <td>
                <div class="table-action-block">
                  <x-buttons.tbl-edit wire-target="editLuminary" model-id="{{ $luminary->id }}" />
                  <x-buttons.tbl-delete model-id="{{ $luminary->id }}" function-name="deleteLuminary" />
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
      <div class="px-4 mt-2">{{ $luminaries->links() }}</div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="luminaryFormModal" tabindex="-1"
    aria-labelledby="luminaryFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="luminaryFormModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateLuminary' : 'storeLuminary' }}"
            class="row gy-3">
            @csrf

            <div class="col-12 col-md-6">
              <label for="luminaryAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="luminaryAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryAddLampTypeId">Lamp Type</label>
              <select wire:model.live="lamp_type_id" id="lumnaryAddLampTypeId"
                class="form-select @error('lamp_type_id') is-invalid @enderror">
                <option value="">Select lamp type</option>
                @foreach ($lamp_types as $lt)
                  <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                @endforeach
              </select>
              @error('lamp_type_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryAddRtuId" class="required">RTU</label>
              <select wire:model.live="rtu_id" id="luminaryAddRtuId"
                class="form-select @error('rtu_id') is-invalid @enderror">
                <option value="">Select RTU</option>
                @foreach ($rtus as $rtu)
                  <option value="{{ $rtu->id }}">{{ $rtu->name }}</option>
                @endforeach
              </select>
              @error('rtu_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            {{-- <div class="col-12 col-md-6">
              <label for="terminalAddLatitude">Latitude</label>
              <input type="number" wire:model.live="lat" id="terminalAddLatitude" step=".0001"
                class="form-control @error('lat') is-invalid @enderror" placeholder="Enter latitude">
              @error('lat')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="terminalAddLongitude">Longitude</label>
              <input type="number" wire:model.live="lon" id="terminalAddLongitude" step=".0001"
                class="form-control @error('lon') is-invalid @enderror" placeholder="Enter longitude">
              @error('lon')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="terminalAddLocation">Location</label>
              <input type="text" wire:model.live="location" id="terminalAddLocation"
                class="form-control @error('location') is-invalid @enderror" placeholder="Enter location">
              @error('location')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div> --}}

            {{-- <div class="col-12 col-md-6">
              <label for="luminaryAddSubGroupId" class="required">Sub Group</label>
              <select wire:model.live="sub_group_id" id="luminaryAddSubGroupId"
                class="form-select @error('sub_group_id') is-invalid @enderror">
                <option value="">Select Sub Group</option>
                @foreach ($sub_groups as $sub_group)
                  <option value="{{ $sub_group->id }}">{{ $sub_group->name }}</option>
                @endforeach
              </select>
              @error('sub_group_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div> --}}

            <div class="col-12 col-md-6">
              <label for="luminaryLuminaryTypeId">Luminary type</label>
              <select wire:model.live="luminary_type_id" id="luminaryLuminaryTypeId"
                class="form-select @error('luminary_type_id') is-invalid @enderror">
                <option value="">Select Luminary type</option>
                @foreach ($luminary_types as $luminary_type)
                  <option value="{{ $luminary_type->id }}">{{ $luminary_type->model }}</option>
                @endforeach
              </select>
              @error('luminary_type_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryControlGearTypeId">Control Gear type</label>
              <select wire:model.live="control_gear_type_id" id="luminaryControlGearTypeId"
                class="form-select @error('control_gear_type_id') is-invalid @enderror">
                <option value="">Select Control Gear type</option>
                @foreach ($control_gear_types as $control_gear_type)
                  <option value="{{ $control_gear_type->id }}">{{ $control_gear_type->name }}</option>
                @endforeach
              </select>
              @error('control_gear_type_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryAddPoleId">Pole</label>
              <select wire:model.live="pole_id" id="luminaryAddPoleId"
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
              <label for="luminaryAddRatedPower">Rated Power</label>
              <input type="number" wire:model.live="rated_power" id="luminaryAddRatedPower"
                class="form-control @error('rated_power') is-invalid @enderror" placeholder="Enter rated power">
              @error('rated_power')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryAddRemarks">Remarks</label>
              <input type="text" wire:model.live="remarks" id="luminaryAddRemarks"
                class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter Luminary remarks">
              @error('remarks')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="luminaryInstallationStatus" class="required">Installation Status</label>
              <div>
                <input type="radio" wire:model.live="installation_status" id="installed" value="1"
                  class="form-check-input @error('installation_status') is-invalid @enderror">
                <label for="installed" class="form-check-label">Installed</label>
              </div>
              <div>
                <input type="radio" wire:model.live="installation_status" id="uninstalled" value="0"
                  class="form-check-input @error('installation_status') is-invalid @enderror">
                <label for="uninstalled" class="form-check-label">Uninstalled</label>
              </div>
              @error('installation_status')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateLuminary' : 'storeLuminary' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteLuminary = (luminaryId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this luminary`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-luminary', {
              id: luminaryId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const LuminaryModal = document.querySelector('#luminaryFormModal')
      LuminaryModal.addEventListener('hidden.bs.modal', event => {
        $wire.dispatch('reset-form');
      });

      window.addEventListener('close-modal', (event) => {
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        console.log(event.detail);
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });
    </script>
  @endscript
</div>
