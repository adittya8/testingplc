<div>
  @php
    $canCreate = auth()->user()->can('Create Sub-Groups');
    $canEdit = auth()->user()->can('Edit Sub-Groups');
    $canDelete = auth()->user()->can('Delete Sub-Groups');
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Sub-Groups</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
              data-bs-target="#subGroupFormModal">
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
            <th>Group</th>
            <th>Sub-Group Name</th>
            <th>RTUs #</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($subGroups as $sg)
            <tr wire:key="{{ $sg->id }}">
              <td>{{ $sg->group?->name }}</td>
              <td>{{ $sg->name }}</td>
              <td>{{ $sg->rtus_count }}</td>
              <td>
                <div class="table-action-block">
                  {{-- <button type="button" class="btn btn-link link-success text-decoration-none"
                    wire:click.prevent="editSubGroupDevices({{ $sg->id }})" wire:loading.attr="disabled"
                    wire:target="editSubGroupDevices({{ $sg->id }})" data-bs-toggle="tooltip"
                    data-bs-title="Update Devices" title="Edit">
                    <i class="fas fa-spinner fa-pulse" wire:loading
                      wire:target="editSubGroupDevices({{ $sg->id }})"></i>
                    <i class="fas fa-plus" wire:loading.remove
                      wire:target="editSubGroupDevices({{ $sg->id }})"></i>
                  </button> --}}
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editSubGroup" model-id="{{ $sg->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $sg->id }}" function-name="deleteSubGroup" />
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
      <div class="px-4 mt-2">{{ $subGroups->links() }}</div>
    </div>
  </div>

  @if ($canEdit || $canCreate)
    <div wire:ignore.self class="modal fade" id="subGroupFormModal" tabindex="-1"
      aria-labelledby="subGroupFormModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="subGroupFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateSubGroup' : 'storeSubGroup' }}"
              class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="subGroupAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="subGroupAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter sub-group name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="subGroupAddGroupId" class="required">Group</label>
                <select wire:model.live="group_id" id="subGroupAddGroupId"
                  class="form-select @error('group_id') is-invalid @enderror" required>
                  <option value="">Select group</option>
                  @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                  @endforeach
                </select>
                @error('group_id')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="subGroupAddRemarks">Remarks</label>
                <textarea wire:model.live="remarks" id="subGroupAddRemarks" class="form-control @error('remarks') is-invalid @enderror"
                  placeholder="Enter remarks" rows="3"></textarea>
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateSubGroup' : 'storeSubGroup' }}" />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- <div wire:ignore.self class="modal fade" id="addToSubGroupModal" tabindex="-1"
    aria-labelledby="addToSubGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="addToSubGroupModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="updateSubGroupDevices" class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="subGroupAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="subGroupAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter sub-group name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="subGroupAddGroupId" class="required">Group</label>
              <select wire:model.live="group_id" id="subGroupAddGroupId"
                class="form-select @error('group_id') is-invalid @enderror" required>
                <option value="">Select group</option>
                @foreach ($groups as $group)
                  <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
              </select>
              @error('group_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="subGroupAddRemarks">Remarks</label>
              <textarea wire:model.live="remarks" id="subGroupAddRemarks"
                class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks" rows="3"></textarea>
              @error('remarks')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateSubGroup' : 'storeSubGroup' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> --}}

  @if ($canDelete)
    @script
      <script>
        window.deleteSubGroup = (subGroupId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this sub-group`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-sub-group', {
                id: subGroupId
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
        const subGroupModal = document.querySelector('#subGroupFormModal')
        subGroupModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
