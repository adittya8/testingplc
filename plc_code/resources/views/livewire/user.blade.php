<div>
  @section('title', __('texts.users'))

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">{{ __('texts.users') }}</h2>
        </div>
        <div class="col-6 text-end">
          @can('Create Users')
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#userFormModal">
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
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Username</th>
            <th>Role</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @php
            $canEditUsers = auth()->user()->can('Edit Users');
            $canDeleteUsers = auth()->user()->can('Delete Users');
          @endphp
          @forelse ($users as $user)
            <tr wire:key="{{ $user->id }}">
              <td>{{ $user->name }}</td>
              <td>
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
              </td>
              <td>
                <a href="tel:{{ $user->mobile }}">{{ $user->mobile }}</a>
              </td>
              <td>{{ $user->username }}</td>
              <td>{{ $user->roles?->first()?->name }}</td>
              <td>
                <div class="table-action-block">
                  @if ($canEditUsers)
                    <x-buttons.tbl-edit wire-target="editUser" model-id="{{ $user->id }}" />
                  @endif
                  @if ($canDeleteUsers)
                    <x-buttons.tbl-delete model-id="{{ $user->id }}" function-name="deleteUser" />
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
      <div class="px-4 mt-2">{{ $users->links() }}</div>
    </div>
  </div>

  @canAny(['Create Users', 'Edit Users'])
    <div wire:ignore.self class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="userFormModalLabel">{{ $modalTitle }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form wire:submit="{{ isset($editMode) && $editMode ? 'updateUser' : 'storeUser' }}" class="row gy-3">
              @csrf

              <div class="col-12 col-md-6">
                <label for="userAddName" class="required">Name</label>
                <input type="text" wire:model.live="name" id="userAddName"
                  class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" required>
                @error('name')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="userAddEmail" class="required">Email Address</label>
                <input type="text" wire:model.live="email" id="userAddEmail"
                  class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" required>
                @error('email')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="userAddUsername" class="required">Username</label>
                <input type="text" wire:model.live="username" id="userAddUsername"
                  class="form-control @error('username') is-invalid @enderror" placeholder="Enter username">
                @error('username')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="userAddMobile" class="required">Mobile Number</label>
                <input type="text" wire:model.live="mobile" id="userAddMobile"
                  class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter mobile number">
                @error('mobile')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="userAddPassword" class="required">Password</label>
                <input type="password" wire:model.live="password" id="userAddPassword"
                  class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                @error('password')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="userAddConfirmPassword" class="required">Confirm Password</label>
                <input type="password" wire:model.live="confirm_password" id="userAddConfirmPassword"
                  class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm password">
                @error('password')
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12">
                <label for="userAddRoleId" class="required">Role</label>
                <div>
                  @foreach ($roles as $role)
                    <div class="form-check form-check-inline">
                      <input type="radio" wire:model.live="role_id" id="userAddRoleId_{{ $role->id }}"
                        value="{{ $role->id }}" class="form-check-input">
                      <label for="userAddRoleId_{{ $role->id }}"
                        class="form-check-label">{{ $role->name }}</label>
                    </div>
                  @endforeach
                  @error('role_id')
                    <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-12 text-end">
                <x-buttons.submit wire-target="{{ $editMode ? 'updateUser' : 'storeUser' }}" />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endcanany

  @script
    <script>
      window.deleteUser = (dirtrictId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this user`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-user', {
              id: dirtrictId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const projectModal = document.querySelector('#userFormModal')
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

      const element = document.querySelector('#userAddProject');
      const choices = new Choices(element);
    </script>
  @endscript
</div>
