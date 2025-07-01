<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>
    @hasSection('title')
      @yield('title') |
    @endif
    PLC
  </title>

  <link rel="shortcut icon" href="{{ asset('images/energy-logo.png') }}" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
  {{-- <link rel="stylesheet" href="{{ asset('plugins/nice-select/nice-select2.css') }}"> --}}
  <link rel="stylesheet" href="{{ asset('plugins/choices/choices.min.css') }}">

  @vite(['resources/scss/app.scss'])
  @yield('pageStyles')
  @livewireStyles

  <style>
    @font-face {
      font-family: kalpurush;
      src: url('{{ asset('kalpurush.ttf') }}');
    }

    body {
      font-family: system-ui, -apple-system, kalpurush, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }
  </style>
</head>
@php
  $noSidebar = in_array(request()->route()->getName(), ['projects', 'home']);
@endphp

<body @class(['no-sidebar' => $noSidebar])>
  @if (!$noSidebar)
    @include('layouts.sidebar')
  @endif

  <div class="body-main">
    @include('layouts.navbar')

    @if (request()->route()->getName() != 'projects.dashboard')
      <div class="main-content" id="mainContent">
        @yield('content')
      </div>
    @else
      @yield('content')
    @endif

    <x-modal id="passwordModal" title="Update Password">
      <x-slot:body>
        <form action="#" class="row gy-3" method="POST" onsubmit="handleFormSubmit(event, this)">
          @csrf
          @method('PUT')
          <div class="col-12">
            <label for="current_password" class="required">Current Password</label>
            <input type="password" id="current_password" name="current_password"
              class="form-control @error('current_password') is-invalid @enderror"
              placeholder="Enter your current password" autocomplete="off" required>
            @error('current_password')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="new_password" class="required">New Password</label>
            <input type="password" id="new_password" name="new_password" autocomplete="off"
              class="form-control @error('new_password') is-invalid @enderror" placeholder="Enter new password"
              required>
            @error('new_password')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="confirm_new_password" class="required">Confirm New Password</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" autocomplete="off"
              class="form-control @error('confirm_new_password') is-invalid @enderror"
              placeholder="Confirm new password" required>
            @error('confirm_new_password')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
          </div>
        </form>
      </x-slot:body>
    </x-modal>

    {{-- <x-modal id="commandModal" title="Run a Command">
      <x-slot:body>
        <form action="{{ route('run-command') }}" method="POST" class="row gy-3"
          onsubmit="handleCmdSubmit(event, this)">
          @csrf
          <div class="col-12">
            <label for="cmdModal_Device_id">Device ID</label>
            <input type="text" name="device_id" id="cmdModal_Device_id" class="form-control"
              placeholder="Enter device ID" required>
          </div>

          <div class="col-12">
            <label for="cmdModal_DateTo">Till Date</label>
            <input type="datetime-local" name="date_to" id="cmdModal_DateTo" class="form-control"
              placeholder="Enter date" required>
          </div>

          <div class="col-12">
            <label>Command</label>
            <div>
              <div class="form-check-inline form-check">
                <input type="radio" name="command" id="cmdModal_command-1" class="from-check-input" value="1">
                <label for="cmdModal_command-1" class="form-check-label">Turn On</label>
              </div>
              <div class="form-check-inline form-check">
                <input type="radio" name="command" id="cmdModal_command-2" class="from-check-input" value="2">
                <label for="cmdModal_command-2" class="form-check-label">Turn Off</label>
              </div>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Run Command</button>
          </div>
        </form>
      </x-slot:body>
    </x-modal> --}}

    @can('Dim RTUs')
      @include('web.luminary-control.individual')
    @endcan

    {{-- @can('Dim RTUs')
      @include('web.luminary-control.individual-schedule')
    @endcan --}}

    {{-- @include('web.luminary-control.group-dcu') --}}

    @can('Dim Groups')
      @include('web.luminary-control.group')
    @endcan

    @can('Dim Sub-Groups')
      @include('web.luminary-control.sub-group')
    @endcan

    {{-- <div class="d-none custom-modal" id="test">
      <div class="card custom-modal-dialog">
        <div class="card-header border-0">
          <h3 class="fs-5" id="customModalTitle">Dimming</h3>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center flex-wrap">
            <div class="flex-grow-1 w-100 w-md-50 pe-1 d-flex align-items-center justify-content-center mb-2 mb-md-0">
              <label for="lightStatus" class="me-2">Status</label>
              <div class="form-check form-check-inline form-switch me-2">
                <input class="form-check-input" type="checkbox" role="switch" id="lightStatus">
              </div>
              <i class="far fa-lightbulb text-secondary me-4"></i>
              <div class="me-5">Brightness</div>
            </div>
            <div class="flex-grow-1 w-100 w-md-50 ps-1">
              <div data-input-range class="input-range-group flex-grow-1 text-center text-md-start">
                <span class="value border px-4 py-1" contenteditable="true">100</span>
                <input type="text" class="value border px-4 py-1" value="100" />
                <input type="range" class="form-range" value="100" id="individualDimRange">
              </div>
            </div>
          </div>
          <div class="text-center mt-3">
            <button class="btn btn-outline-secondary" data-custom-dismiss="#test">Cancel</button>
            <button class="btn btn-primary" id="customModalConfirm">Confirm</button>
          </div>
        </div>
      </div>
    </div> --}}

    @canany(['Dim RTUs', 'Dim Groups', 'Dim Sub-Groups'])
      <div class="modal fade" id="dimModal" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true"
        aria-labelledby="dimModalLabel" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="dimModalLabel">Dimming</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" data-modal-toggle
                aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="brightness-tab" data-bs-toggle="tab"
                    data-bs-target="#brightness-tab-pane" type="button" role="tab"
                    onclick="setDimmingType('brightness')" aria-controls="brightness-tab-pane"
                    aria-selected="true">Brightness</button>
                </li>
                <li class="nav-item" role="presentation" id="dimModalPowerTab">
                  <button class="nav-link" id="power-tab" data-bs-toggle="tab" data-bs-target="#power-tab-pane"
                    onclick="setDimmingType('power')" type="button" role="tab" aria-controls="power-tab-pane"
                    aria-selected="false">Power</button>
                </li>
              </ul>

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="brightness-tab-pane" role="tabpanel"
                  aria-labelledby="brightness-tab" tabindex="0">
                  <div class="d-flex align-items-center flex-wrap mt-3">
                    <div
                      class="flex-grow-1 w-100 w-md-50 pe-1 d-flex align-items-center justify-content-center mb-2 mb-md-0">
                      <div class="me-5">Brightness</div>
                    </div>
                    <div class="flex-grow-1 w-100 w-md-50 ps-1">
                      <div data-input-range class="input-range-group flex-grow-1 text-center text-md-start">
                        <input type="number" class="value border px-3 py-1 form-control d-inline-block text-center"
                          style="width: 5rem" value="100" data-dim-input />
                        <input type="range" class="form-range" value="100" id="individualDimRange"
                          data-dim-range>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="power-tab-pane" role="tabpanel" aria-labelledby="power-tab"
                  tabindex="0">
                  <div class="d-flex align-items-center flex-wrap my-3">
                    <div
                      class="flex-grow-1 w-100 w-md-50 pe-1 d-flex align-items-center justify-content-center mb-2 mb-md-0">
                      <div class="me-5">Power</div>
                    </div>
                    <div class="flex-grow-1 w-100 w-md-50 ps-1">
                      <div data-input-range class="input-range-group flex-grow-1 text-center text-md-start">
                        <input type="number" class="value border px-3 py-1 form-control d-inline-block text-center"
                          style="width: 100%" value="100" data-dim-input-power />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center flex-wrap">
                <div
                  class="flex-grow-1 w-100 w-md-50 pe-1 d-flex align-items-center justify-content-center mb-2 mb-md-0">
                  <div class="me-5">Status</div>
                </div>
                <div class="flex-grow-1 w-100 w-md-50 ps-1 d-flex align-items-center">
                  <div class="form-check form-check-inline form-switch me-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="lightStatus" checked>
                  </div>
                  <i class="far fa-lightbulb text-secondary me-4 light-bulb glow" data-light-status-icon></i>
                </div>
              </div>

              <div class="text-center mt-3">
                <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal" data-modal-toggle>Cancel</button>
                <button class="btn btn-primary" id="customModalConfirm">Confirm</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endcanany
  </div>

  <script src="{{ asset('js/axios.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/notiflix-aio.min.js') }}"></script>
  <script src="{{ asset('plugins/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
  {{-- <script src="{{ asset('plugins/nice-select/nice-select2.js') }}"></script> --}}
  <script src="{{ asset('plugins/choices/choices.min.js') }}"></script>
  <script src="{{ asset('js/admin.js') }}"></script>

  @livewireScripts

  <script>
    window.addEventListener('show-toast', (event) => {
      if (!event.detail.message) return;
      showToast(event.detail.message, event.detail.type ?? 'success')
    });

    window.addEventListener('show-loader', (event) => {
      addLoader(document.body);
    });
    window.addEventListener('hide-loader', (event) => {
      removeLoader(document.body);
    });

    if (localStorage.getItem('session_success') && localStorage.getItem('session_success') != '') {
      showToast(localStorage.getItem('session_success'))
      localStorage.removeItem('session_success');
    }
    if (localStorage.getItem('session_warning') && localStorage.getItem('session_warning') != '') {
      showToast(localStorage.getItem('session_warning'), 'warning')
      localStorage.removeItem('session_warning');
    }
    if (localStorage.getItem('session_error') && localStorage.getItem('session_error') != '') {
      showToast(localStorage.getItem('session_error'), 'danger')
      localStorage.removeItem('session_error');
    }

    if ("{{ session('success') }}" != '') {
      showToast("{{ session('success') }}")
    }
    if ("{{ session('warning') }}" != '') {
      showToast("{{ session('warning') }}", 'warning')
    }
    if ("{{ session('error') }}" != '') {
      showToast("{{ session('error') }}", 'danger')
    }

    localStorage.setItem('locale', '{{ app()->getLocale() }}')

    // function handleCmdSubmit(event, form) {
    //   event.preventDefault();
    //   addLoader(document.body);

    //   let data = new FormData(form);
    //   axios({
    //     method: 'POST',
    //     url: form.action,
    //     data: data
    //   }).then(res => {
    //     removeLoader(document.body);
    //     showToast(res.data.message ?? 'Command Send.');
    //   }).catch(err => {
    //     console.error(err);
    //     removeLoader(document.body);
    //     showToast(err.response && err.response.data && err.response.data.message ? err.response.data.message :
    //       'Something went wrong!', 'danger');
    //   })
    // }
  </script>

  @canany(['Dim RTUs', 'Dim Groups', 'Dim Sub-Groups'])
    <script>
      const indLumNoFilter = document.querySelector('#indivLuminaryNo');
      const indivConNoFilter = document.querySelector('#indivConcentratorNo');
      let indivTable = new DataTable('#individualLumTable', {
        ordering: false,
        pageLength: 20,
        lengthChange: false,
      });
      indLumNoFilter.addEventListener('keyup', () => {
        indivTable.search(indLumNoFilter.value).draw();
      })
      indivConNoFilter.addEventListener('keyup', () => {
        indivTable.search(indivConNoFilter.value).draw();
      })

      const indivGroupName = document.querySelector('#indivGroupName');
      const indivSubGroupName = document.querySelector('#indivSubGroupName');
      let dimControlGroupTable = new DataTable('#dimControlGroupTable', {
        ordering: false,
        pageLength: 5,
        lengthChange: false,
      });

      window.luminaryId = null;
      window.indivDimUrl = null;

      function openCustomModal(selector) {
        const modalEl = document.querySelector(selector);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }

      const dimModalEl = document.querySelector("#dimModal");
      const dimModal = new bootstrap.Modal(dimModalEl);
      const dimModalLabel = document.querySelector("#dimModalLabel");

      function openDimModal(id, title, type, firstModalId = null) {
        dimModalLabel.innerHTML = title;
        const modalToggles = document.querySelectorAll('[data-modal-toggle]');
        if (firstModalId) {
          modalToggles.forEach((toggle) => {
            toggle.dataset.bsTarget = firstModalId;
            toggle.dataset.bsToggle = 'modal';
            delete toggle.dataset.bsDismiss;
          });
        } else {
          modalToggles.forEach((toggle) => {
            delete toggle.dataset.bsTarget;
            delete toggle.dataset.bsToggle;
            toggle.dataset.bsDismiss = 'modal'
          });
        }

        luminaryId = id;
        if (type == "group") {
          indivDimUrl = "{{ route('group-dimming', ['id' => ':id']) }}";
          indivDimUrl = indivDimUrl.replace(":id", id);
        } else if (type == "individual") {
          indivDimUrl = "{{ route('individual-dimming', ['id' => ':id']) }}";
          indivDimUrl = indivDimUrl.replace(":id", id);
        } else if (type == "sub-group") {
          indivDimUrl = "{{ route('sub-group-dimming', ['id' => ':id']) }}";
          indivDimUrl = indivDimUrl.replace(":id", id);
        } else if (type == "dcu") {
          indivDimUrl = "{{ route('dcu-dimming', ['id' => ':id']) }}";
          indivDimUrl = indivDimUrl.replace(":id", id);
        }

        if (firstModalId) {
          const firstModalEl = document.querySelector(firstModalId);
          const firstModal = bootstrap.Modal.getInstance(firstModalEl);
          firstModal.hide();
        }

        if (type == 'dcu') {
          document.querySelector('#dimModalPowerTab').classList.add('d-none');
        } else {
          document.querySelector('#dimModalPowerTab').classList.remove('d-none');
        }

        dimModal.show();
      }

      const dimInput = document.querySelector('[data-dim-input]');
      const dimRange = document.querySelector('[data-dim-range]');
      const dimInputPower = document.querySelector('[data-dim-input-power]');
      window.dimmingType = 'brightness';

      dimInput?.addEventListener('input', () => {
        if (dimInput.value < 0) {
          dimInput.value = 0;
        }
        if (dimInput.value > 100) {
          dimInput.value = 100;
        }
        dimRange.value = dimInput.value;
        dimInput.value > 0 ? lightIconOn(dimInput.value) : lightIconOff(dimInput.value);
      })
      dimRange?.addEventListener('input', () => {
        dimInput.value = dimRange.value;
        dimInput.value > 0 ? lightIconOn(dimInput.value) : lightIconOff(dimInput.value);
      })
      dimInputPower?.addEventListener('input', () => {
        dimInputPower.value > 0 ? lightIconOn(100, true) : lightIconOff(0);
      })

      document.querySelector('#customModalConfirm').addEventListener('click', () => {
        const data = new FormData();
        if (dimmingType == 'power') {
          data.append('power', dimInputPower.value);
        } else {
          data.append('dim', dimRange.value);
        }

        addLoader(document.body);

        axios({
          method: 'POST',
          url: indivDimUrl,
          data: data,
        }).then(res => {
          showToast(res.data.message ?? 'Dimming set successfully.');
          removeLoader(document.body);
        }).catch(err => {
          console.error(err);
          removeLoader(document.body);
          showToast(err.response.data.message ?? 'Something went wrong!', 'danger');
        });
      });

      const lightStatus = document.querySelector('#lightStatus');
      const lightStatusIcon = document.querySelector('[data-light-status-icon]');
      lightStatus.addEventListener('click', () => {
        lightStatus.checked ? lightIconOn() : lightIconOff();
      })

      function lightIconOn(brightness = 100, dontUpdatePower = false) {
        lightStatusIcon.classList.add('glow');
        dimInput.value = brightness;
        dimRange.value = brightness;
        if (!dontUpdatePower) {
          dimInputPower.value = brightness;
        }
        lightStatus.checked = true;
      }

      function lightIconOff(brightness = 0) {
        lightStatusIcon.classList.remove('glow');
        dimInput.value = brightness;
        dimRange.value = brightness;
        dimInputPower.value = brightness;
        lightStatus.checked = false;
      }

      function setDimmingType(value) {
        dimmingType = value;
      }
    </script>
  @endcanany

  @yield('pageScripts')
</body>

</html>
