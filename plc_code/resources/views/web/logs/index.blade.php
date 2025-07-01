@extends('layouts.layout')
@section('title', 'Activity Logs')
@section('content')
  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom">
      <div class="row">
        <div class="col-12">
          <h2 class="fs-4 mb-0 card-title">Activity Logs</h2>
        </div>
      </div>
    </div>

    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Log ID</th>
            <th>Event</th>
            <th>User</th>
            <th>Log Time</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($logs as $log)
            <tr>
              <td>{{ $log->id }}</td>
              <td>
                @php
                  $event = ucfirst($log->event);
                  if ($log->subject) {
                      $event .= ' ' . getMappedModelName($log->subject);
                  }
                @endphp
                {{ $event }}
              </td>
              <td>{{ $log->user }}</td>
              <td>{{ date('M d, Y h:i a', strtotime($log->created_at)) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="px-4">
        {{ $logs->links() }}
      </div>
    </div>
  </div>
@endsection
