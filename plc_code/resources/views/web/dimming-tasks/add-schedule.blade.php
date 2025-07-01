<form action="{{ route('dimming-task.store-schedule', ['project' => config('project_id'), 'task' => $task]) }}"
  class="row gy-3" onsubmit="handleFormSubmit(event, this)" method="POST">
  @csrf

  <div class="col-12">
    <label for="time">Time</label>
    <input type="time" name="time" id="timeSchedule" class="form-control" placeholder="Enter time">
  </div>

  <div class="col-12">
    <label for="brightness">Brightness</label>
    <input type="number" name="brightness" id="brightnessSchedule" class="form-control" placeholder="Enter brightness">
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
  </div>
</form>
