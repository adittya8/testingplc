<div>
  @section('title', 'Role Permissions')

  <div class="card">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <h2 class="fs-4 mb-0 card-title col-12 col-md-6">Role Permissions - {{ $role->name }}</h2>
        <div class="col-12 col-md-6 text-end">
          <div class="form-check form-check-inline">
            <input type="checkbox" id="perm-check-all" class="form-check-input">
            {{-- <input type="checkbox" id="perm-check-all" wire:model.live="checkAll" class="form-check-input"> --}}
            <label for="perm-check-all" class="form-check-label">Check All</label>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form class="row gy-3" wire:submit="rolePermissions">
        @csrf

        @php
          $permissionsChunks = $allPermissions->groupBy('group_id')->chunk(4);
        @endphp
        @foreach ($permissionsChunks as $key => $permissionGroups)
          <div class="col-12 col-md-6 col-lg-3">
            @foreach ($permissionGroups as $permissionGroup)
              <div class="mb-3">
                @foreach ($permissionGroup as $permission)
                  <div class="form-check">
                    <input type="checkbox" id="perm-{{ $permission->id }}" wire:model.live="permissionIds"
                      class="form-check-input" value="{{ $permission->id }}" data-perm-check>
                    <label for="perm-{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>
        @endforeach

        <div class="col-12">
          {{-- <button wire:target="rolePermissions"></button> --}}
          <x-buttons.submit wire-target="rolePermissions" />
        </div>
      </form>
    </div>
  </div>

  @script
    <script>
      const checkAllInput = document.querySelector('#perm-check-all');
      checkAllInput.addEventListener('click', () => {
        if (checkAllInput.checked) {
          document.querySelectorAll('[data-perm-check]').forEach(check => {
            check.checked = true;
          });
          $wire.dispatch('check-all', {
            'check': 1
          });
        } else {
          document.querySelectorAll('[data-perm-check]').forEach(check => {
            check.checked = false;
          });
          $wire.dispatch('check-all', {
            'check': 0
          });
        }
      })
    </script>
  @endscript
</div>
