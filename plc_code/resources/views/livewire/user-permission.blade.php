<div>
  @section('title', 'Role Permissions')

  <div class="card">
    <div class="card-header">
      <h2 class="fs-4 mb-0 card-title">Role Permissions - {{ $role->name }}</h2>
    </div>
    <div class="card-body">
      <div class="row gy-3">
        @foreach ($permissions as $key => $permission)
          @php
            $project = $projects->where('id', $key)->first();
          @endphp
          <div class="col-12 @if (!$loop->last) border-bottom-1 @endif">
            <div class="row gy-2">
              <div class="col-md-4 col-lg-3 d-flex align-items-baseline">
                <button type="button" class="btn btn-link link-danger me-2" data-bs-toggle="tooltip"
                  data-bs-title="Remove all permissions from this project">
                  <i class="fas fa-trash-alt"></i>
                </button>
                {{ $project->name }}
              </div>
              <div class="col-md-8 col-lg-9">
                @foreach ($permission as $perm)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="{{ $perm['permission_id'] }}"
                      id="permission-{{ "$key-{$perm['permission_id']}" }}" @checked($perm['has_permission'])>
                    <label class="form-check-label"
                      for="permission-{{ "$key-{$perm['permission_id']}" }}">{{ $perm['name'] }}</label>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
        <div class="col-12 d-flex justify-content-between">
          <button type="button" class="btn btn-primary" wire:click="addProject"><i class="fas fa-plus"></i> Add
            Project</button>
          <button type="button" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
        </div>
      </div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="projectFormModal" tabindex="-1" aria-labelledby="projectFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="projectFormModalLabel">E</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form action="" class="row">
            @csrf
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script></script>
  @endscript
</div>
