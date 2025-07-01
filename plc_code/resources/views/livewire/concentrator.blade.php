<div>
  @section('title', 'DCUs')
  @php
    $canCreate = auth()->user()->can('Create DCUs');
    $canEdit = auth()->user()->can('Edit DCUs');
    $canDelete = auth()->user()->can('Delete DCUs');
    $canDim = auth()->user()->can('Dim DCUs');
    $canSchedule = auth()->user()->can('Schedule DCUs');
  @endphp
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">DCUs</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
              data-bs-target="#concentratorFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endif
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table text-center mb-0">
          <thead>
            <tr>
              <th>Status</th>
              <th>DCU</th>
              <th>Zone</th>
              <th>Road</th>
              {{-- <th>SIM Card No.</th>
              <th>Longitude</th>
              <th>Latitude</th> --}}
              <th>Location</th>
              <th>Remarks</th>
              <th class="td-actions">Operations</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($concentrators as $dcu)
              <tr wire:key="{{ $dcu->id }}">
                <td>
                  @if (isDcuOnline($dcu->status_updated_at))
                    <span class="text-success">Online</span>
                  @else
                    <span class="text-danger">Offline</span>
                  @endif
                </td>
                <td>{{ $dcu->name }} ({{ $dcu->concentrator_no }})</td>
                <td>{{ $dcu->road?->zone?->name }}</td>
                <td>{{ $dcu->road?->name }}</td>
                {{-- <td>{{ $dcu->sim_no }}</td>
                <td>{{ $dcu->lon }}</td>
                <td>{{ $dcu->lat }}</td> --}}
                <td>{{ $dcu->location }}</td>
                <td>{{ $dcu->remarks }}</td>
                <td>
                  <div class="table-action-block">
                    <a href="#" class="btn btn-link link-info" data-bs-toggle="tooltip" data-bs-title="Details"
                      wire:click="showConcentrator({{ $dcu->id }})">
                      <i class="fas fa-spinner fa-pulse" wire:loading wire:target="showConcentrator"></i>
                      <i class="fas fa-list" wire:loading.remove wire:target="showConcentrator"></i>
                    </a>
                    @can('Create RTUs')
                      <a href="#" class="btn btn-link link-success" data-bs-toggle="tooltip"
                        data-bs-title="Sync RTUs"
                        onclick="syncRtu({{ $dcu->id }}, '{{ $dcu->concentrator_no }}', '{{ $dcu->name }}')">
                        <i class="fas fa-refresh"></i>
                      </a>
                    @endcan
                    @if ($canDim)
                      <a href="#" class="btn btn-link link-secondary" data-bs-toggle="tooltip"
                        data-bs-title="Dimming"
                        onclick="openDimModal({{ $dcu->id }}, 'Dimming DCU: {{ $dcu->name }}', 'dcu')">
                        <i class="fas fa-sliders"></i>
                      </a>
                    @endif
                    @if ($canSchedule)
                      <button type="button" class="btn btn-link" data-bs-toggle="tooltip" data-bs-title="Scheduling"
                        onclick="openSchedulingModal({{ $dcu->id }}, '{{ $dcu->name }}', '{{ $dcu->concentrator_no }}')">
                        <i class="far fa-clock"></i>
                      </button>
                    @endif
                    @if ($canEdit)
                      <x-buttons.tbl-edit wire-target="editConcentrator" model-id="{{ $dcu->id }}" />
                    @endif
                    @if ($canDelete)
                      <x-buttons.tbl-delete model-id="{{ $dcu->id }}" function-name="deleteConcentrator" />
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
      <div class="px-4 mt-2">{{ $concentrators->links() }}</div>
    </div>
  </div>

  @if ($canCreate || $canEdit)
    <div wire:ignore.self class="modal fade" id="concentratorFormModal" tabindex="-1"
      aria-labelledby="concentratorFormModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="concentratorFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateConcentrator' : 'storeConcentrator' }}"
              class="row gy-3">
              @csrf

              <div class="col-12 col-md-6">
                <label for="concentratorAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="concentratorAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter DCU name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="concentratorAddRoadId">Road</label>
                <select wire:model.live="road_id" id="concentratorAddRoadId"
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
                <label for="concentratorAddConcentratorNo" class="required">DCU No</label>
                <input type="text" wire:model.live="concentrator_no" id="concentratorAddConcentratorNo"
                  class="form-control @error('concentrator_no') is-invalid @enderror" placeholder="Enter DCU number"
                  required>
                @error('concentrator_no')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="concentratorAddSimNo">Sim Card No</label>
                <input type="text" wire:model.live="sim_no" id="concentratorAddSimNo"
                  class="form-control @error('sim_no') is-invalid @enderror" placeholder="Enter DCU sim card number">
                @error('sim_no')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
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
              </div>

              <div class="col-12 col-md-6">
                <label for="concentratorAddRemarks">Remarks</label>
                <input type="text" wire:model.live="remarks" id="concentratorAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter DCU remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateConcentrator' : 'storeConcentrator' }}" />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif

  <div wire:ignore.self class="modal fade" id="dcuDetailsModal" tabindex="-1"
    aria-labelledby="dcuDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="dcuDetailsModalLabel">DCU Details</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row gy-3">
            <div class="col-md-6">
              <p class="fw-medium mb-1">Name</p>
              <p class="mb-0">{{ $name }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">DCU Number</p>
              <p class="mb-0">{{ $concentrator_no }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Zone</p>
              <p class="mb-0">{{ $zone_name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Road</p>
              <p class="mb-0">{{ $road_name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Latitude</p>
              <p class="mb-0">{{ $lat ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Longitude</p>
              <p class="mb-0">{{ $lon ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Location</p>
              <p class="mb-0">{{ $location ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
              <p class="fw-medium mb-1">Remarks</p>
              <p class="mb-0">{{ $remarks ?? 'N/A' }}</p>
            </div>
            {{-- <div class="col-md-6">
              <p class="fw-medium mb-1">Last Schedule Set</p>
              <p class="mb-0">{{ $remarks ?? 'N/A' }}</p>
            </div> --}}
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($canSchedule)
    <div wire:ignore.self class="modal fade" id="schedulingModal" tabindex="-1"
      aria-labelledby="schedulingModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="schedulingModalLabel">Scheduling</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form class="row gy-3" action="#" id="scheduleForm">
              @csrf
              {{-- <small>* Enter times in ascending order.</small>

              @for ($i = 0; $i < 6; $i++)
                @php
                  $index = $i + 1;
                @endphp
                <div class="col-12 col-md-6">
                  <label for="time-{{ $i }}" class="required">Time {{ $index }}</label>
                  <input type="time" id="time-{{ $i }}" name="times[{{ $i }}][time]"
                    wire.model="times.time.{{ $i }}"
                    @if ($i == 0) readonly value="00:00" @endif
                    class="form-control @error("times.time.$i") is-invalid @enderror" placeholder="Enter time"
                    required>
                  @error("times.time.$i")
                    <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-12 col-md-6">
                  <label for="brightness-{{ $i }}" class="required">Brightness
                    {{ $index }}</label>
                  <input type="number" id="brightness-{{ $i }}"
                    name="times[{{ $i }}][brightness]" wire.model="times.brightness.{{ $i }}"
                    class="form-control @error("times.brightness.$i") is-invalid @enderror"
                    placeholder="Enter brightness" required>
                  @error("times.brightmess.$i")
                    <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
              @endfor --}}

              <div class="col-12">
                <label for="schedule_preset_id" class="required">Schedule Preset</label>
                <select name="preset_id" id="schedule_preset_id" class="form-select" required>
                  <option value="">---</option>
                  @foreach ($presets as $preset)
                    <option value="{{ $preset->id }}">{{ $preset->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-spinner fa-pulse" wire:loading wire:target="sendScheduleCommand()"></i>
                  <i class="fas fa-paper-plane" wire:loading.remove wire:target="sendScheduleCommand()"></i> Submit
                </button>
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
        window.deleteConcentrator = (concentratorId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this DCU?`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-concentrator', {
                id: concentratorId
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

  @can('Create RTUs')
    @script
      <script>
        window.syncRtu = (dcuId, dcuCode, dcuName) => {
          Notiflix.Confirm.show(
            "Confirm Sync",
            `Are you sure to sync DCU: ${dcuName} (${dcuCode})? Before syncing, please ensure all RTUs of this DCU are added under this DCU in the web panel.`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('sync-rtus', {
                id: dcuId
              });
            },
            () => {}, {
              titleColor: colors.warning,
            }
          );
        }
      </script>
    @endscript
  @endcan

  @if ($canCreate || $canEdit)
    @script
      <script>
        const concentratorModal = document.querySelector('#concentratorFormModal')
        concentratorModal.addEventListener('hidden.bs.modal', event => {
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

  @if ($canSchedule)
    @script
      <script>
        const scheduleModalEl = document.querySelector('#schedulingModal')
        const scheduleModal = new bootstrap.Modal(scheduleModalEl);
        const scheduleModalLabel = document.querySelector('#schedulingModalLabel');
        window.openSchedulingModal = (id, name, code) => {
          scheduleModal.show();
          scheduleModalLabel.innerText = `Scheduling - ${name} (${code})`;
          scheduleModalEl.querySelector('form').dataset.dcuId = id;
          // scheduleModalEl.querySelector('button').addEventListener('click', () => {
          //   $wire.dispatch('send-schedule-command', {
          //     id: id
          //   });
          // })
        }

        const scheduleForm = document.querySelector('#scheduleForm')
        scheduleForm.addEventListener('submit', (e) => {
          e.preventDefault();
          addLoader(document.body);

          let url = "{{ route('schedule-command', ['id' => ':id', 'project' => ':project']) }}";
          url = url.replace(':id', scheduleForm.dataset.dcuId).replace(':project', {{ config('project_id') }});
          console.log(url);
          axios({
            method: 'POST',
            url: url,
            data: new FormData(scheduleForm)
          }).then(res => {
            showToast(res.data.message ?? 'Schedule command sent.');
            removeLoader(document.body);
          }).catch(err => {
            removeLoader(document.body);
            showToast(err.response & err.response.data & err.response.data.message ? err.response.data.message :
              'Something went wrong!', 'danger');
          })
        })
      </script>
    @endscript
  @endif
</div>
