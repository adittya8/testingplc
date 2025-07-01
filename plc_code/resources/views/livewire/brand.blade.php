<div>
  @php
    $canEdit = auth()->user()->can('Edit Brand');
    $canDelete = auth()->user()->can('Delete Brand');
    $canCreateOrEdit = auth()
        ->user()
        ->canany(['Create Brand', 'Edit Brand']);
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Brands</h2>
        </div>
        <div class="col-6 text-end">
          @can('Create Brand')
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#brandFormModal">
              <i class="fas fa-plus"></i> Add
            </button>
          @endcan
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Brand Name</th>
            <th>Type</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($brands as $brand)
            <tr wire:key="{{ $brand->id }}">
              <td>{{ $brand->name }}</td>
              <td>{{ $brand->type }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editBrand" model-id="{{ $brand->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $brand->id }}" function-name="deleteBrand" />
                  @endif
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
      <div class="px-4 mt-2">{{ $brands->links() }}</div>
    </div>
  </div>

  @if ($canCreateOrEdit)
    <div wire:ignore.self class="modal fade" id="brandFormModal" tabindex="-1" aria-labelledby="brandFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="brandFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateBrand' : 'storeBrand' }}" class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="brandAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="brandAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter brand name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="brandAddType">Type</label>
                <input type="text" wire:model.live="type" id="brandAddType"
                  class="form-control @error('type') is-invalid @enderror" placeholder="Enter brand type">
                @error('type')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="brandAddRemarks">Remarks</label>
                <input type="text" wire:model.live="remarks" id="brandAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateBrand' : 'storeBrand' }}" />
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
        window.deleteBrand = (brandId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this brand`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-brand', {
                id: brandId
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

  @if ($canCreateOrEdit)
    @script
      <script>
        const brandModal = document.querySelector('#brandFormModal')
        brandModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
