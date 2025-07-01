@extends('layouts.layout')
@section('title', 'Add Sub-Groups/RTUs to Task')
@section('content')
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom">
      <div class="row">
        <div class="col-lg-6 d-flex align-items-center">
          <h2 class="fs-4 mb-0 card-title">Dimming Schedule Task: {{ $task->name }}</h2>
        </div>
        <div class="col-lg-6 text-end">
          <a href="{{ route('dimming-task.edit', ['project' => config('project_id'), 'task' => $task]) }}"
            class="btn btn-outline-secondary">
            <i class="fas fa-edit"></i> Edit
          </a>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-lg-6">
          <p class="fw-bold mb-1">Weekdays</p>
          <p class="mb-0">
            @php
              $wdNames = $task->weekdays->map(fn($i) => getWeekdayName($i['weekday']));
            @endphp
            {{ $wdNames->implode(', ') }}
          </p>
        </div>

        <div class="col-lg-6">
          <p class="fw-bold mb-1">Dates</p>
          <p class="mb-0">
            {{ date('Y-m-d', strtotime($task->date_from)) }} -
            {{ date('Y-m-d', strtotime($task->date_to)) }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom">
      <div class="row">
        <div class="col-lg-6 d-flex align-items-center">
          <h2 class="fs-4 mb-0 card-title">Schedules</h2>
        </div>
        <div class="col-6 text-end">
          <button type="button" class="btn btn-outline-success"
            onclick="openModal('{{ route('dimming-task.add-schedule', ['project' => config('project_id'), 'task' => $task]) }}', 'Add Schedule')">
            <i class="fas fa-plus"></i> Add Schedule
          </button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Brightness</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($task->schedules as $schedule)
            <tr>
              <td>{{ $schedule->time }}</td>
              <td>{{ $schedule->brightness }}</td>
              <td>
                <a href="#" class="link-danger"
                  onclick="deleteItem('{{ route('dimming-task.delete-schedule', ['project' => config('project_id'), 'schedule' => $schedule, 'task' => $task]) }}', {{ $schedule->id }}, 'Task schedule')"
                  data-bs-toggle="tooltip" data-bs-title="Delete">
                  <i class="fas fa-trash-alt"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">Nothing to show.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom">
      <div class="row">
        <div class="col-12">
          <h2 class="fs-4 mb-0 card-title">Add Luminaries</h2>
        </div>
      </div>
    </div>

    <div class="card-body">
      <form action="{{ route('dimming-task.store-rtus', ['project' => config('project_id'), 'task' => $task]) }}">
        @csrf

        @if ($task->groups && count($task->groups))
          @php
            if ($task->groups->first()?->sub_group_id) {
                $groupType = 'sg';
                $subGroups = $task->groups->map(fn($i) => $i->subGroup);
                $allSubGroups = $allSubGroups->map(function ($i) use ($subGroups) {
                    $exists = $subGroups->where('id', $i->id)->first();
                    $i->checked = $exists ? true : false;
                    return $i;
                });
            } elseif ($task->groups->first()?->rtu_id) {
                $groupType = 'ind';
                $rtus = $task->groups->map(fn($i) => $i->rtu);
                $allRtus = $allRtus->map(function ($i) use ($rtus) {
                    $exists = $rtus->where('id', $i->id)->first();
                    $i->checked = $exists ? true : false;
                    return $i;
                });
            }
          @endphp
          <input type="hidden" name="type" value="{{ $groupType }}">
          <div id="contents">
            @if ($groupType == 'sg')
              <p class="mb-1 fw-medium">Type: Sug-Group</p>
              @include('web.dimming-tasks.content', [
                  'subGroups' => $allSubGroups,
              ])
            @else
              <p class="mb-1 fw-medium">Type: RTU</p>
              @include('web.dimming-tasks.content-rtus', [
                  'rtus' => $allRtus,
              ])
            @endif
          </div>
        @else
          <div>
            <p class="fw-medium mb-1">Type</p>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="type" id="inlineRadio1" value="sg"
                onclick="loadContent(1)" @checked(old('type') == 'sg')>
              <label class="form-check-label" for="inlineRadio1">Sub-Group</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="type" id="inlineRadio2" value="ind"
                onclick="loadContent(2)" @checked(old('type') == 'ind')>
              <label class="form-check-label" for="inlineRadio2">Individual</label>
            </div>
            @error('type')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>

          <div id="contents" class="mt-3"></div>
        @endif

        @error('rtu_ids')
          <div class="text-danger">{{ $message }}</div>
        @enderror
        @error('sg_ids')
          <div class="text-danger">{{ $message }}</div>
        @enderror

        <div class="mt-2">
          @error('submit_error')
            <div class="text-danger mb-2">{{ $message }}</div>
          @enderror
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="scheduleModalLabel">Add Schedule</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>

  <x-modal />
@endsection
@section('pageScripts')
  <script>
    function loadContent(type) {
      let url = '{{ route('dimming-task.rtu-list', config('project_id')) }}';
      if (type == 1) {
        url = '{{ route('dimming-task.sub-group-list', config('project_id')) }}';
      }

      const contentEl = document.querySelector('#contents');
      addLoader(document.body)
      axios.get(url).then(res => {
        contentEl.innerHTML = res.data;
        removeLoader(document.body)
      })
    }
  </script>

  @if (old('type'))
    <script>
      loadContent("{{ old('type') }}" == 'sg' ? 1 : 2);
    </script>
  @endif
@endsection
