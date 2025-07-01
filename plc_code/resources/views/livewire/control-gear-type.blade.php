<div>
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Control Gear Types</h2>
        </div>
        <div class="col-6 text-end">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
            data-bs-target="#controlGearTypeFormModal">
            <i class="fas fa-plus"></i> Add
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Type</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($cgTypes as $cgType)
            <tr wire:key="{{ $cgType->id }}">
              <td>{{ $cgType->name }}</td>
              <td>{{ $cgType->type }}</td>
              <td>
                <div class="table-action-block">
                  <x-buttons.tbl-edit wire-target="editControlGearType" model-id="{{ $cgType->id }}" />
                  <x-buttons.tbl-delete model-id="{{ $cgType->id }}" function-name="deleteControlGearType" />
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $cgTypes->links() }}</div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="controlGearTypeFormModal" tabindex="-1"
    aria-labelledby="controlGearTypeFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="controlGearTypeFormModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateControlGearType' : 'storeControlGearType' }}"
            class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="controlGearTypeAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="controlGearTypeAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter control gear type name"
                required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateControlGearType' : 'storeControlGearType' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteControlGearType = (cgTypeId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this control gear type`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-control-gear-type', {
              id: cgTypeId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const controlGearTypeModal = document.querySelector('#controlGearTypeFormModal')
      controlGearTypeModal.addEventListener('hidden.bs.modal', event => {
        $wire.dispatch('reset-form');
      });
    </script>
  @endscript
</div>
