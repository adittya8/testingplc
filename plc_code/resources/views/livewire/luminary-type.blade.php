<div>
  @php
    $canCreate = auth()->user()->can('Create Luminary-Types');
    $canEdit = auth()->user()->can('Edit Luminary-Types');
    $canDelete = auth()->user()->can('Delete Luminary-Types');
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Luminary Types</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#ltFormModal">
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
            <th>Model</th>
            <th>Light Source Type</th>
            <th>Brand</th>
            <th>Rated Power</th>
            <th>Avg. Life</th>
            <th>Remakrs</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($luminaryTypes as $type)
            <tr wire:key="{{ $type->id }}">
              <td>{{ $type->model }}</td>
              <td>{{ $type->lightSourceType?->name }}</td>
              <td>{{ $type->brand?->name }}</td>
              <td>{{ $type->rated_power }}W</td>
              <td>{{ $type->avg_life }}</td>
              <td>{{ $type->remarks }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editLuminaryType" model-id="{{ $type->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $type->id }}" function-name="deleteLT" />
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $luminaryTypes->links() }}</div>
    </div>
  </div>

  @if ($canEdit || $canCreate)
    <div wire:ignore.self class="modal fade" id="ltFormModal" tabindex="-1" aria-labelledby="ltFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="ltFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ $editMode ? 'updateLuminaryType' : 'storeLuminaryType' }}" class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="ltFormModel" class="required">Model</label>
                <input type="text" wire:model="model" id="ltFormModel"
                  class="form-control @error('model') is-invalid @enderror" placeholder="Enter model" required>
                @error('model')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="ltFormLSTypeId" class="required">Light Source Type</label>
                <select wire:model="light_source_type_id" id="ltFormLSTypeId"
                  class="form-select @error('light_source_type_id') is-invalid @enderror" required>
                  <option value="">Select light source type</option>
                  @foreach ($lightSourceTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                  @endforeach
                </select>
                @error('light_source_type_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="ltFormBrandId" class="required">Brand</label>
                <select wire:model="brand_id" id="ltFormBrandId"
                  class="form-select @error('brand_id') is-invalid @enderror" required>
                  <option value="">Select brand</option>
                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
                @error('brand_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="ltFormRatedPower">Rated Power (in watts)</label>
                <input type="text" wire:model="rated_power" id="ltFormRatedPower"
                  class="form-control @error('rated_power') is-invalid @enderror" placeholder="Enter rated power"
                  required>
                @error('rated_power')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="ltFormAvgLife">Avg. Life</label>
                <input type="text" wire:model="avg_life" id="ltFormAvgLife"
                  class="form-control @error('avg_life') is-invalid @enderror" placeholder="Enter average life"
                  required>
                @error('avg_life')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="ltFormRemarks">Remarks</label>
                <input type="text" wire:model="remarks" id="ltFormRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateLuminaryType' : 'storeLuminaryType' }}" />
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
        window.deleteLT = (ltId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this luminary type`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-luminary-type', {
                id: ltId
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

  @if ($canEdit || $canCreate)
    @script
      <script>
        const ltModal = document.querySelector('#ltFormModal')
        ltModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
