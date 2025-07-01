<div>
  @section('title', 'Roles')

  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Roles</h2>
        </div>
        <div class="col-6 text-end">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#roleFormModal">
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
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roles as $role)
            <tr wire:key="{{ $role->id }}">
              <td>{{ $role->name }}</td>
              <td>
                <div class="table-action-block">
                  @if ($role->name != 'Project Admin')
                    @if (Auth::user()->hasAnyRole(['Super Admin', 'Project Admin']))
                      <a class="btn btn-link link-success text-decoration-none" title="Permissions"
                        data-bs-toggle="tooltip" data-bs-title="Permissions"
                        href="{{ route('roles.permissions', ['project' => config('project_id'), 'role' => $role]) }}">
                        <i class="fas fa-check"></i>
                      </a>
                    @endif
                    <x-buttons.tbl-edit wire-target="editRole" model-id="{{ $role->id }}" />
                    <x-buttons.tbl-delete model-id="{{ $role->id }}" function-name="deleteRole" />
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
      <div class="px-4 mt-2">{{ $roles->links() }}</div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="roleFormModal" tabindex="-1" aria-labelledby="roleFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="roleFormModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateRole' : 'storeRole' }}" class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="roleAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="roleAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateRole' : 'storeRole' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteRole = (id) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this role`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-role', {
              id: id
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const projectModal = document.querySelector('#roleFormModal')
      projectModal.addEventListener('hidden.bs.modal', event => {
        $wire.dispatch('reset-form');
      });

      window.addEventListener('close-modal', (event) => {
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });

      const element = document.querySelector('#roleAddProject');
      const choices = new Choices(element);
    </script>
  @endscript
</div>
