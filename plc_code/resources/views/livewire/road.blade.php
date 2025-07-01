<div>
  @php
    $canCreate = auth()->user()->can('Create Roads');
    $canEdit = auth()->user()->can('Edit Roads');
    $canDelete = auth()->user()->can('Delete Roads');
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Roads</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#roadFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Road Name</th>
            <th>Zone</th>
            <th>Road Grade</th>
            <th>Road Length (KM)</th>
            <th>Poles Count</th>
            <th>Luminaries Count</th>
            <th>Remarks</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roads as $road)
            <tr wire:key="{{ $road->id }}">
              <td>{{ $road->name }}</td>
              <td>{{ $road->zone?->name }}</td>
              <td>{{ $road->grade }}</td>
              <td>{{ $road->length }}</td>
              <td>{{ $road->poles_count }}</td>
              <td>{{ $road->luminaries_count }}</td>
              <td>{{ $road->remarks }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editRoad" model-id="{{ $road->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $road->id }}" function-name="deleteRoad" />
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $roads->links() }}</div>
    </div>
  </div>

  @if ($canCreate || $canEdit)
    <div wire:ignore.self class="modal fade" id="roadFormModal" tabindex="-1" aria-labelledby="roadFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="roadFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateRoad' : 'storeRoad' }}" class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="roadAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="roadAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter road name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="roadAddZoneId">Zone</label>
                <select wire:model.live="zone_id" id="roadAddZoneId"
                  class="form-select @error('zone_id') is-invalid @enderror" required>
                  <option value="">Select zone</option>
                  @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                  @endforeach
                </select>
                @error('zone_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="roadAddGrade">Grade</label>
                <select class="form-select @error('grade') is-invalid @enderror" wire:model.live="grade"
                  id="roadAddGrade">
                  <option value="">--</option>
                  <option value="M1">M1</option>
                  <option value="M2">M2</option>
                  <option value="M3">M3</option>
                  <option value="M4">M4</option>
                </select>
                @error('grade')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="roadAddLength" class="required">Length</label>
                <input type="number" wire:model.live="length" id="roadAddLength" step=".01"
                  class="form-control @error('length') is-invalid @enderror" placeholder="Enter road length">
                @error('gralengthde')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="roadAddRemarks" class="required">Remarks</label>
                <input type="text" wire:model.live="remarks" id="roadAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateRoad' : 'storeRoad' }}" />
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
        window.deleteRoad = (roadId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this road`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-road', {
                id: roadId
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
        const roadModal = document.querySelector('#roadFormModal')
        roadModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
