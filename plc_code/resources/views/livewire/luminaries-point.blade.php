<div>
  @section('title', 'Luminaries Point')
  @section('pageStyles')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  @endsection

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Luminaries Point Reports</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <form action="#" class="row gy-3">
          <div class="col-12 col-md-5">
            <div class="row mb-3">
              <label for="filter_month" class="col-2 col-form-label pe-0" style="font-size: 13px">Month</label>
              <div class="col-10">
                <select wire:model.live="filterMonth" id="filter_month" class="form-select">
                  <option value="">Any</option>
                  @for ($i = 1; $i < 13; $i++)
                    <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="row mb-3">
              <label for="filter_road" class="col-2 col-form-label pe-0" style="font-size: 13px">Road</label>
              <div class="col-10">
                <select name="filterRoad" id="filter_road" class="form-select">
                  <option value="">All</option>
                  @foreach ($roads as $road)
                    <option value="{{ $road->id }}" @selected($filterRoad == $road->id)>{{ $road->name }}</option>
                  @endforeach
                </select>
              </div>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-success">Search</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card mt-4">
        <div class="card-header py-3 px-4 border-bottom">
          <div class="row">
            <div class="col-12">
              <h2 class="fs-4 mb-0 card-title">Luminaries Points</h2>
            </div>
          </div>
        </div>
        <div class="card-body" style="min-height: 400px">
          <p class="fs-4">Luminaries Points: {{ $luminaryPointsSum }}</p>
          <div style="min-height: 420px">
            <canvas id="lumDcuChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  @section('pageScripts')
    <script src="{{ asset('plugins/chart.umd.min.js') }}"></script>

    <script>
      const lumData = @json($luminaryPointsCount);
      const lumDcuChart = document.getElementById('lumDcuChart');
      new Chart(lumDcuChart, {
        type: 'line',
        data: {
          labels: @json($labels),
          datasets: [{
            label: 'Luminary Points',
            data: lumData,
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
              }
            }
          },
        }
      });
    </script>
  @endsection
</div>
