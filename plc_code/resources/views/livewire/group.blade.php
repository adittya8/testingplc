<div>
  @php
    $canCreate = auth()->user()->can('Create Groups');
    $canEdit = auth()->user()->can('Edit Groups');
    $canDelete = auth()->user()->can('Delete Groups');
  @endphp
  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Groups</h2>
        </div>
        <div class="col-6 text-end">
          @if ($canCreate)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#groupFormModal">
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
            <th>Group Name</th>
            <th>Number of Sub-groups</th>
            <th>Number of RTUs</th>
            <th>Remarks</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($groups as $group)
            <tr wire:key="{{ $group->id }}">
              <td>{{ $group->name }}</td>
              <td>{{ $group->sub_groups_count }}</td>
              <td>{{ $group->rtus_count }}</td>
              <td>{{ $group->remarks }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <x-buttons.tbl-edit wire-target="editGroup" model-id="{{ $group->id }}" />
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $group->id }}" function-name="deleteGroup" />
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $groups->links() }}</div>
    </div>
  </div>

  @if ($canCreate || $canEdit)
    <div wire:ignore.self class="modal fade" id="groupFormModal" tabindex="-1" aria-labelledby="groupFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="groupFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateGroup' : 'storeGroup' }}" class="row gy-3">
              @csrf

              <div class="col-12">
                <label for="groupAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="groupAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter group name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="groupAddRemarks">Remarks</label>
                <input type="text" wire:model.live="remarks" id="groupAddRemarks"
                  class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">
                @error('remarks')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateGroup' : 'storeGroup' }}" />
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
        window.deleteGroup = (dirtrictId) => {
          Notiflix.Confirm.show(
            "Confirm Delete",
            `Are you sure to delete this group`,
            "Yes",
            "No",
            () => {
              addLoader(document.body);
              $wire.dispatch('delete-group', {
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
        const groupModal = document.querySelector('#groupFormModal')
        groupModal.addEventListener('hidden.bs.modal', event => {
          $wire.dispatch('reset-form');
        });
      </script>
    @endscript
  @endif
</div>
