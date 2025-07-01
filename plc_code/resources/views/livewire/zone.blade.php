<div>
  @php
    $canCreate = auth()->user()->can('Create Zones');
    $canEdit = auth()->user()->can('Edit Zones');
    $canDelete = auth()->user()->can('Delete Zones');
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Zones</h2>
        </div>
        @if ($canCreate)
          <div class="col-6 text-end">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#zoneFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          </div>
        @endif
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Zone Name</th>
            <th>Number of Roads</th>
            <th>Remarks</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($zones as $zone)
            <tr wire:key="{{ $zone->id }}">
              <td>{{ $zone->name }}</td>
              <td>{{ $zone->roads_count }}</td>
              <td>{{ $zone->remarks }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editZone" model-id="{{ $zone->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $zone->id }}" function-name="deleteZone" />
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $zones->links() }}</div>
    </div>
  </div>

  @if ($canCreate || $canEdit)
    <div wire:ignore.self class="modal fade" id="zoneFormModal" tabindex="-1" aria-labelledby="zoneFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="zoneFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateZone' : 'storeZone' }}" class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="zoneAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="zoneAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter zone name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="zoneAddRemarks">Remarks</label>
                <input type="text" wire:model.live="remarks" id="zoneAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateZone' : 'storeZone' }}" />
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
        window.deleteZone = (dirtrictId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this zone`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-zone', {
                id: dirtrictId
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
        const distModal = document.querySelector('#zoneFormModal')
        distModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
