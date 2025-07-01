<div>
  @section('title', 'Dimming Tasks')
  @php
    $canCreate = auth()->user()->can('Create Schedule Tasks');
    $canEdit = auth()->user()->can('Edit Schedule Tasks');
    $canDelete = auth()->user()->can('Delete Schedule Tasks');
  @endphp
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Dimming Tasks</h2>
        </div>
        <div class="col-6 text-end">
          {{-- <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="fas fa-plus"></i> Add
          </button> --}}
          @if ($canCreate)
            <a href="{{ route('dimming-task.create', config('project_id')) }}" class="btn btn-outline-success">
              <i class="fas fa-plus"></i> Add
            </a>
          @endif
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Task Name</th>
            <th>Dates</th>
            <th>Week Days</th>
            <th>Status</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($tasks as $task)
            <tr wire:key="{{ $task->id }}">
              <td>{{ $task->name }}</td>
              <td>{{ date('Y-m-d', strtotime($task->date_from)) }} - {{ date('Y-m-d', strtotime($task->date_to)) }}</td>
              <td>
                @php
                  $weekdays = $task->weekdays->map(fn($i) => getWeekdayName($i['weekday'], true));
                @endphp
                {{ $weekdays->implode(', ') }}
              </td>
              <td class="text-center">
                <x-active-badge :active="$task->is_active" />

                {{-- <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="status" wire:model="dimStatus"
                    value="1" aria-label="Status" data-bs-toggle="tooltip"
                    data-bs-title="{{ $task->is_active == 1 ? 'Active' : 'Inactive' }}" @checked($task->is_active == 1)>
                </div> --}}
              </td>
              <td>
                <div class="table-action-block">
                  @if ($canEdit)
                    <a href="{{ route('dimming-task.add-rtus', ['project' => config('project_id'), 'task' => $task]) }}"
                      class="btn btn-link link-success text-decoration-none" data-bs-toggle="tooltip"
                      data-bs-title="Details">
                      <i class="fas fa-plus"></i>
                    </a>
                    <a href="{{ route('dimming-task.edit', ['project' => config('project_id'), 'task' => $task]) }}"
                      class="btn btn-link link-secondary text-decoration-none" data-bs-toggle="tooltip"
                      data-bs-title="Edit">
                      <i class="fas fa-pencil"></i>
                    </a>
                  @endif
                  @if ($canDelete)
                    <x-buttons.tbl-delete model-id="{{ $task->id }}" function-name="deleteDimmingTask" />
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $tasks->links() }}</div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="margin-top: 120px">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="categoryModalLabel">Task Category</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="text-center py-3">
            @foreach ($categories as $cat)
              <a href="{{ route('dimming-task.create', ['project' => config('project_id'), 'category' => $cat->id]) }}"
                class="btn btn-outline-secondary btn-hover-outline-success @if (!$loop->last) me-4 @endif">{{ $cat->name }}</a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteDimmingTask = (taskId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this Task?`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-task', {
              id: taskId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

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
