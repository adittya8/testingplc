@extends('layouts.layout')
@section('title', 'Dashboard')
@section('content')
  <div class="map position-relative">
    <iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
      src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=23.804,90.3980&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>

    <div class="row gy-2 gx-3 me-0 map-page-charts mb-3">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card map-card">
          <div class="card-body">
            <div class="d-flex mb-2 align-items-center justify-content-between">
              <h2 class="fs-6 m-0">Lighting Monitoring</h2>
              <a class="fs-7 link-secondary td-none" href="{{ route('rtus', ['project' => config('project_id')]) }}">
                More <i class="fas fa-angle-right"></i>
              </a>
            </div>
            <div class="canvas">
              <canvas id="lightMtrChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card map-card">
          <div class="card-body">
            <div class="d-flex mb-2 align-items-center justify-content-between">
              <h2 class="fs-6 m-0">DCU Monitoring</h2>
              <a class="fs-7 link-secondary td-none"
                href="{{ route('concentrators', ['project' => config('project_id')]) }}">
                More <i class="fas fa-angle-right"></i>
              </a>
            </div>
            <div class="canvas">
              <canvas id="dcuMtrChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card map-card">
          <div class="card-body position-relative">
            <div class="d-flex mb-2 align-items-center justify-content-between">
              <h2 class="fs-6 m-0">{{ __('texts.alerts') }}</h2>
              <a class="fs-7 link-secondary td-none" href="{{ route('alerts', ['project' => config('project_id')]) }}">
                More <i class="fas fa-angle-right"></i>
              </a>
            </div>
            <div class="canvas">
              <canvas id="alarmsChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card map-card">
          <div class="card-body position-relative">
            <div class="d-flex mb-2 align-items-center justify-content-between">
              <h2 class="fs-6 m-0">Power Consumption</h2>
              <a class="fs-7 link-secondary td-none"
                href="{{ route('power-consumption', ['project' => config('project_id')]) }}">
                More <i class="fas fa-angle-right"></i>
              </a>
            </div>
            <div class="canvas">
              <canvas id="pcChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('pageScripts')
  <script src="{{ asset('plugins/chart.umd.min.js') }}"></script>
  <script src="{{ asset('plugins/chartjs-plugin-annotation.min.js') }}"></script>
  <script>
    const datalabelOptions = {
      color: '#FFF',
      formatter: function(value, context) {
        return value > 0 ? value : '';
      }
    };

    const labels = ['Online', 'Offline', 'Alarm', 'Other'];
    const bgColors = [
      '{{ \App\Enums\LuminaryStatuses::getColorFromValue(\App\Enums\LuminaryStatuses::ONLINE->value) }}',
      '{{ \App\Enums\LuminaryStatuses::getColorFromValue(\App\Enums\LuminaryStatuses::OFFLINE->value) }}',
      '{{ \App\Enums\LuminaryStatuses::getColorFromValue(\App\Enums\LuminaryStatuses::ALARM->value) }}',
      '{{ \App\Enums\LuminaryStatuses::getColorFromValue(-1) }}',
    ];

    const lightStatuses = @json($luminaryStatus);
    const lightMtrChart = document.querySelector('#lightMtrChart');
    new Chart(lightMtrChart, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          label: '',
          data: [
            lightStatuses.online_count,
            lightStatuses.offline_count,
            lightStatuses.alarm_count,
            lightStatuses.other_count
          ],
          backgroundColor: bgColors,
          datalabels: datalabelOptions
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
            text: 'Luminary Status'
          },
          annotation: {
            annotations: {
              dLabel: {
                type: 'doughnutLabel',
                content: ({
                  chart
                }) => [
                  `Online: ${lightStatuses.online_count}`,
                  `Offline: ${lightStatuses.offline_count}`,
                  `Alarm: ${lightStatuses.alarm_count}`,
                  `Other: ${lightStatuses.other_count}`,
                ],
                font: [{
                  size: 20
                }],
                color: bgColors
              }
            }
          }
        }
      },
    });

    const dcuStatuses = @json($concentratorStatus);
    const dcuMtrChart = document.querySelector('#dcuMtrChart');
    new Chart(dcuMtrChart, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          label: '',
          data: [
            dcuStatuses.online_count,
            dcuStatuses.offline_count,
            dcuStatuses.alarm_count,
            dcuStatuses.other_count
          ],
          backgroundColor: bgColors,
          datalabels: datalabelOptions
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
            text: 'DCU Status'
          },
          annotation: {
            annotations: {
              dLabel: {
                type: 'doughnutLabel',
                content: ({
                  chart
                }) => [
                  `Online: ${dcuStatuses.online_count}`,
                  `Offline: ${dcuStatuses.offline_count}`,
                  `Alarm: ${dcuStatuses.alarm_count}`,
                  `Other: ${dcuStatuses.other_count}`,
                ],
                font: [{
                  size: 20
                }],
                color: bgColors
              }
            }
          }
        }
      },
    });

    const alarmCounts = @json($alarmCounts);
    const alarmsChart = document.querySelector('#alarmsChart');
    new Chart(alarmsChart, {
      type: 'line',
      data: {
        labels: alarmCounts.map(i => i.label),
        datasets: [{
            label: 'Luminaries',
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

    const powerConsumptions = @json($powerConsumptions);
    const pcChart = document.querySelector('#pcChart');
    new Chart(pcChart, {
      type: 'line',
      data: {
        labels: powerConsumptions.map(i => i.label),
        datasets: [{
          label: 'Power Consumption',
          data: powerConsumptions.map(i => i.total_consumption),
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
