<div>
  @section('title', 'Dimming Schedules')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Dimming Schedules</h2>
        </div>
        <div class="col-6 text-end">
          <a href="{{ route('dimming-schedule.create', ['project' => config('project_id')]) }}"
            class="btn btn-outline-success">
            <i class="fas fa-plus"></i> Add
          </a>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table text-center">
        <thead>
          <tr>
            <th>Dim Schedule Name</th>
            <th>Zone</th>
            <th>Road</th>
            <th>Created Date</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($schedules as $schedule)
            <tr>
              <td>{{ $schedule->name }}</td>
              <td>{{ $schedule->road?->zone?->name }}</td>
              <td>{{ $schedule->road?->name }}</td>
              <td>{{ $schedule->created_at }}</td>
              <td>
                <div class="table-action-block">
                  <a class="btn btn-link link-secondary text-decoration-none" data-bs-toggle="tooltip"
                    data-bs-title="Edit"
                    href="{{ route('dimming-schedule.edit', ['project' => config('project_id'), 'schedule' => $schedule]) }}">
                    <i class="fas fa-pencil"></i>
                  </a>
                  <x-buttons.tbl-delete model-id="{{ $schedule->id }}" function-name="deleteDimmingSchedule" />
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nothing to show.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @script
    <script>
      window.deleteDimmingSchedule = (id) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this schedule`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-schedule', {
              id: id
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }
    </script>
  @endscript
</div>
