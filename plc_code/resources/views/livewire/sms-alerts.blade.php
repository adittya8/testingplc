<div>
  @section('title', 'SMS ALerts')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">SMS ALerts</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="mt-4 table text-center">
        <thead>
          <tr>
            <th>Generated At</th>
            <th>Sent At</th>
            <th>Sent To</th>
            <th>Alert Type</th>
            <th>Message</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($alerts as $alert)
            <tr>
              <td>{{ $alert->created_at }}</td>
              <td>{{ $alert->created_at }}</td>
              <td>{{ $alert->recipient }}</td>
              <td>{{ $alert->subject }}</td>
              <td>{{ $alarm->text }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $alerts->links() }}
    </div>
  </div>
</div>
