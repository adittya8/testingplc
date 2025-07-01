<div>
  @section('title', __('texts.alerts'))
  @section('pageStyles')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  @endsection

  {{-- <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Alerts</h2>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="mb-3 border-top p-3">
        <form action="#" class="row gy-3 align-items-end" wire:submit="filterChart">
          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_dates" style="font-size: 13px">Dates</label>
            <input type="text" wire:model.live="filterDates" id="filter_dates" class="form-control"
              placeholder="Search by date" value="{{ $filterDates }}">
          </div>

          <div class="col-12 col-md-4 col-lg-3">
            <label for="filter_road" style="font-size: 13px">Road</label>
            <select name="filterRoad" id="filter_road" class="form-select">
              <option value="">All</option>
              @foreach ($roads as $road)
                <option value="{{ $road->id }}" @selected($filterRoad == $road->id)>{{ $road->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-spinner fa-pulse" wire:loading wire:target="filterChart"></i>
              <i class="fas fa-search" wire:loading.remove wire:target="filterChart"></i>
              Search
            </button>
          </div>
        </form>
      </div>
    </div>
  </div> --}}

  <div class="row">
    <div class="col-12 col-lg-7 h-100">
      <div class="card mt-4">
        <div class="card-header py-3 px-4 border-bottom">
          <div class="row">
            <div class="col-12">
              <h2 class="fs-4 mb-0 card-title">RTU & DCU Alerts</h2>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div wire:ignore>
            <canvas id="lumDcuChart" style="height: 400px"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5 h-100">
      <div class="card mt-4">
        <div class="card-header py-3 px-4 border-bottom">
          <div class="row">
            <div class="col-12">
              <h2 class="fs-4 mb-0 card-title">RTU Alerts</h2>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div wire:ignore>
            <canvas id="donutChart" style="height: 400px"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  @section('pageScripts')
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/chart.umd.min.js') }}"></script>
    <script src="{{ asset('plugins/chartjs-plugin-annotation.min.js') }}"></script>

    <script>
      $('#filter_dates').daterangepicker({
        ranges: {
          'Today': [moment(), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
        },
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true,
        "startDate": moment().startOf('month'),
        "endDate": moment().endOf('month'),
        locale: {
          format: 'YYYY-MM-DD',
        }
      });

      const alarmCounts = @json($alarmCounts);
      const alarmsChart = document.querySelector('#lumDcuChart');
      new Chart(alarmsChart, {
        type: 'line',
        data: {
          labels: alarmCounts.map(i => i.label),
          datasets: [{
              label: 'RTU',
              data: alarmCounts.map(i => i.luminary_count),
              borderColor: 'rgb(112, 134, 253)',
              backgroundColor: 'rgba(112, 134, 253, .4)',
              fill: true,
            },
            {
              label: 'DCU',
              data: alarmCounts.map(i => i.concentrator_count),
              borderColor: 'rgb(111, 209, 149)',
              backgroundColor: 'rgba(111, 209, 149, .4)',
              fill: true,
            },
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              display: true,
              title: {
                display: true,
                text: 'Dates'
              },
              beginAtZero: true,
            },
            y: {
              display: true,
              title: {
                display: true,
                text: 'Count'
              },
              beginAtZero: true,
              ticks: {
                stepSize: 1,
              }
            }
          },
        },
      });

      const alarmTypesCounts = @json($alarmTypesCounts);
      const alarmTypes = @json($alarmTypes);
      const donutChart = document.querySelector('#donutChart');
      new Chart(donutChart, {
        type: 'doughnut',
        data: {
          labels: alarmTypes.map(i => i.text),
          datasets: [{
            label: '',
            data: [
              // alarmTypesCounts.over_current,
              // alarmTypesCounts.under_current,
              alarmTypesCounts.over_voltage,
              alarmTypesCounts.under_voltage
            ],
            backgroundColor: alarmTypes.map(i => i.color)
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: false,
              text: 'Luminaries Alerts'
            },
            annotation: {
              annotations: {
                dLabel: {
                  type: 'doughnutLabel',
                  content: ({
                    chart
                  }) => [
                    `Over Voltage: ${alarmTypesCounts.over_voltage}`,
                    `Under Voltage: ${alarmTypesCounts.under_voltage}`,
                  ],
                  font: [{
                    size: 20
                  }],
                  color: alarmTypes.map(i => i.color)
                }
              }
            }
          }
        },
      });


      Livewire.on('updateChart', data => {
        pcChart.data = data[0];
        pcChart.update();
      });
    </script>
  @endsection
</div>
