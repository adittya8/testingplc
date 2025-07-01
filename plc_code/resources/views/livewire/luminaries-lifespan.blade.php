<div>
  @section('title', 'Luminaries Lifespan')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Luminaries Lifespan</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="mt-4 table text-center mb-0">
        <thead>
          <tr>
            <th>Duration</th>
            <th>RTU</th>
            <th>Pole</th>
            <th>Road</th>
            <th>Group</th>
            <th>Sub-Group</th>
            <th>DCU</th>
            <th>Luminary Model</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rtus as $rtu)
            <tr>
              <td>
                @php
                  $totalMinutes = $rtu->runningTimes?->sum('running_time');
                  $totalHours = floor($totalMinutes / 60);
                  $remainingMins = $totalMinutes % 60;
                @endphp
                {{ $totalHours < 10 ? "0$totalHours" : $totalHours }}:{{ $remainingMins }}
              </td>
              <td>{{ $rtu->name }} ({{ $rtu->code }})</td>
              <td>{{ $rtu->pole?->code }}</td>
              <td>{{ $rtu->concentrator?->road?->name }}</td>
              <td>{{ $rtu->subGroup?->group?->name }}</td>
              <td>{{ $rtu->subGroup?->name }}</td>
              <td>{{ $rtu->concentrator?->name }} ({{ $rtu->concentrator?->concentrator_no }})</td>
              <td>{{ $rtu->model?->name }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="px-4">
        {{ $rtus->links() }}
      </div>
    </div>
  </div>
</div>
