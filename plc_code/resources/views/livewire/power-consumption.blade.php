<div>
  @section('title', 'Power Consumption')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-12">
          <h2 class="fs-4 mb-0 card-title">Power Consumption</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <form action="#" class="row align-items-end gy-3" wire:submit="filterChart">
          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_month" class="form-label mb-1" style="font-size: 13px">Month</label>
            <select id="filter_month" class="form-select" wire:model="filterMonth">
              <option value="">Any</option>
              @for ($i = 1; $i < 13; $i++)
                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
              @endfor
            </select>
          </div>

          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_road" class="form-label mb-1" style="font-size: 13px">Road</label>
            <select wire:model="filterRoad" id="filter_road" class="form-select">
              <option value="">All</option>
              @foreach ($roads as $road)
                <option value="{{ $road->id }}" @selected($filterRoad == $road->id)>{{ $road->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3 col-lg-2">
            <button type="submit" class="btn btn-success w-100">
              <i class="fas fa-spinner fa-pulse" wire:loading wire:target="filterChart"></i>
              <i class="fas fa-search" wire:loading.remove wire:target="filterChart"></i>
              Search
            </button>
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
              <h2 class="fs-4 mb-0 card-title">Total Power Consumed</h2>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p class="fs-4">Total Consumed: {{ $totalPowerConsumption }}W</p>
          <div wire:ignore>
            <canvas id="pcChart" style="height: 400px"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  @section('pageScripts')
    <script src="{{ asset('plugins/chart.umd.min.js') }}"></script>

    <script>
      const pcChartEl = document.querySelector('#pcChart');
      console.log(@json($chartData));

      const pcChart = new Chart(pcChartEl, {
        type: 'line',
        data: @json($chartData),
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

      Livewire.on('updateChart', data => {
        pcChart.data = data[0];
        pcChart.update();
      });
    </script>
  @endsection
</div>
