<div>
  @section('title', 'Equipment Alarms')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Equipment Alarms</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="row">
        <div class="col-12 col-lg-6 px-5">
          @php
            $dcuTotal = $alarms->whereNull('rtu_code')->count();
            $dcuAlarm = $alarms->whereNull('rtu_code')->whereNotNull('alert_type')->count();
            $dcuNormal = $dcuTotal - $dcuAlarm;

            $rtuTotal = $alarms->count() - $dcuTotal;
            $rtuAlarm = $alarms->whereNotNull('rtu_code')->whereNotNull('alert_type')->count();
            $rtuNormal = $rtuTotal - $rtuAlarm;
          @endphp
          <h3>DCU</h3>
          <div>
            <div class="d-inline-block bg-blue-200 text-white rounded-4 px-4 py-2 text-center me-3" style="width: 29%">
              <p class="m-0 fs-5">{{ $dcuCount = $dcus->count() }}</p>
              <p class="m-0 fs-5">Total</p>
            </div>
            <div class="d-inline-block bg-green-200 text-white rounded-4 px-4 py-2 text-center me-3" style="width: 29%">
              <p class="m-0 fs-5">{{ $dcuCount - $dcuAlarmCount }}</p>
              <p class="m-0 fs-5">Normal</p>
            </div>
            <div class="d-inline-block bg-red-200 text-white rounded-4 px-4 py-2 text-center" style="width: 29%">
              <p class="m-0 fs-5">{{ $dcuAlarmCount }}</p>
              <p class="m-0 fs-5">Alarm</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6 px-5">
          <h3>Lamp</h3>
          <div class="d-inline-block bg-blue-200 text-white rounded-4 px-4 py-2 text-center me-3" style="width: 29%">
            <p class="m-0 fs-5">{{ $rtuCount = $rtus->count() }}</p>
            <p class="m-0 fs-5">Total</p>
          </div>
          <div class="d-inline-block bg-green-200 text-white rounded-4 px-4 py-2 text-center me-3" style="width: 29%">
            <p class="m-0 fs-5">{{ $rtuCount - $rtuAlarmCount }}</p>
            <p class="m-0 fs-5">Normal</p>
          </div>
          <div class="d-inline-block bg-red-200 text-white rounded-4 px-4 py-2 text-center" style="width: 29%">
            <p class="m-0 fs-5">{{ $rtuAlarmCount }}</p>
            <p class="m-0 fs-5">Alarm</p>
          </div>
        </div>
      </div>

      <table class="mt-4 table text-center">
        <thead>
          <tr>
            <th>Device Code</th>
            <th>Device Type</th>
            <th>Alarm Type</th>
            <th>Road</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($alarms as $alarm)
            <tr>
              {{-- <td>{{ $alarm->rtu_code ?? $alarm->dcu_code }}</td>
              <td>{{ $alarm->rtu_code ? 'RTU' : 'DCU' }}</td>
              <td>{{ $alarm->alert_type?->getText() }}</td>
              <td>{{ $alarm->dcu?->road?->name ?? $alarm->rtu?->dcu?->road?->name }}</td>
              <td>{{ $alarm->created_at }}</td> --}}
              <td>{{ $alarm['device_code'] }}</td>
              <td>{{ $alarm['device_type'] }}</td>
              <td>{{ $alarm['alarm_type'] }}</td>
              <td>{{ $alarm['road'] }}</td>
              <td>{{ $alarm['time'] }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="px-4">
        {{-- {{ $alarms->links() }} --}}
      </div>
    </div>
  </div>
</div>
