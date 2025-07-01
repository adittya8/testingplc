<div>
  <div class="container mt-4 px-3">
    <div class="row mb-2">
      <div class="col-6 d-flex align-items-center">
        <h1 class="fs-5 m-0">{{ __('texts.project_name') }}:</h1>
      </div>
      <div class="col-6 d-flex align-items-center justify-content-end">
        <a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#projectFormModal">
          <i class="fas fa-plus"></i> Add Project
        </a>
      </div>
    </div>

    <div class="row">
      @foreach ($projects as $project)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
          <div class="card company-card">
            <div class="card-header border-bottom-0 p-0">
              <a href="{{ route('projects.dashboard', ['project' => $project]) }}" class="text-decoration-none">
                <img src="{{ asset($project->image ? getFileAssetPath($project, 'image') : 'images/city.jpg') }}"
                  alt="" class="company-card-img">
              </a>
            </div>
            <div class="card-body">
              <p class="title">
                <a href="{{ route('projects.dashboard', ['project' => $project]) }}"
                  class="text-dark text-decoration-none">
                  {{ $project->name }}
                </a>
              </p>
              <p class="company-card-info">DCUs: {{ $project->concentrators_count }}</p>
              <p class="company-card-info mb-0">Light Sources: {{ $project->luminaries_count }}</p>

              <div class="project-actions">
                <x-buttons.tbl-edit class="me-2" wire-target="editProject" model-id="{{ $project->id }}" />
                <x-buttons.tbl-delete function-name="deleteProject" model-id="{{ $project->id }}" />
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="projectFormModal" tabindex="-1" aria-labelledby="projectFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="projectFormModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateProject' : 'storeProject' }}" class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="projectAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="projectAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter project name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="projectAddImage">Image</label>
              <x-image-upload-with-preview propertyName="image" :model="$project"
                acceptedFileTypes=".jpg, .jpeg, .png, .webp" />
              @error('image')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            @error('submit_error')
              <div class="col-12 text-danger">{{ $message }}</div>
            @enderror
            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateProject' : 'storeProject' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteProject = (roadId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this project`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-project', {
              id: roadId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const projectModal = document.querySelector('#projectFormModal')
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
    </script>
  @endscript
</div>
