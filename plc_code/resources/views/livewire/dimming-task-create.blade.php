<div>
  @section('title', $pageTitle)
  @section('pageStyles')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  @endsection

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom">
      <div class="row">
        <div class="col-12">
          <h2 class="fs-4 mb-0 card-title">{{ $pageTitle }}</h2>
        </div>
      </div>
    </div>

    <form wire:submit="{{ $task ? 'updateTask' : 'storeTask' }}">
      <div class="card-body">
        <div class="row gy-3">
          <div class="col-12 col-md-6 col-lg-5">
            <div class="row mb-3">
              <label for="name" class="col-3 col-form-label pe-0 required" style="font-size: 13px">Task Name</label>
              <div class="col-9">
                <input type="text" wire:model.live="name" id="name" placeholder="Enter task name"
                  class="form-control">
              </div>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-7">
            <div class="row mb-3">
              <label for="dates" class="col-1 col-form-label pe-0 required" style="font-size: 13px">Dates</label>
              <div class="col-11">
                <input type="text" wire:model.live="dates" id="dates" class="form-control">
              </div>
              @error('dates')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="col-12 text-center">
            @for ($i = 0; $i <= 6; $i++)
              <div class="form-check form-check-inline">
                <input type="checkbox" wire:model="weekdays" id="weekdays-{{ $i }}" class="form-check-input"
                  value="{{ $i }}">
                <label for="weekdays-{{ $i }}" style="font-size: 14px"
                  class="form-check-label text-uppercase">{{ getWeekdayName($i, true) }}</label>
              </div>
            @endfor
            @error('weekdays')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      {{-- <div class="card-header py-3 px-4 border-bottom">
        <div class="row">
          <div class="col-6">
            <h2 class="fs-4 mb-0 card-title">Group Task</h2>
          </div>
          <div class="col-6 text-end">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
              data-bs-target="#addGroupTaskModal">
              <i class="fas fa-plus"></i> Add
            </button>
          </div>
        </div>
      </div> --}}

      <div class="card-body">
        {{-- <table class="table text-center">
          <thead>
            <tr>
              <th>Sub Group</th>
              <th>Dimming Schedule</th>
              <th class="td-actions">Operations</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($groupTasks as $key => $gt)
              <tr wire:key="{{ $key }}">
                <td>{{ \App\Models\SubGroup::find($gt['sub_group_id'])->name }}</td>
                <td>{{ \App\Models\DimmingSchedule::find($gt['dimming_schedule_id'])->name }}</td>
                <td>
                  <button type="button" class="btn btn-link link-danger text-decoration-none"
                    wire:click.prevent="removeDimItem({{ $key }})" wire:loading.attr="disabled"
                    wire:target="removeDimItem({{ $key }})" title="Delete" data-bs-toggle="tooltip"
                    data-bs-title="Delete">
                    <i class="fas fa-spinner fa-pulse" wire:loading
                      wire:target="removeDimItem({{ $key }})"></i>
                    <i class="fas fa-trash-alt" wire:loading.remove
                      wire:target="removeDimItem({{ $key }})"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">No Data</td>
              </tr>
            @endforelse
          </tbody>
        </table> --}}

        <div class="text-center mt-4">
          <x-buttons.submit wire-target="{{ $task ? 'update ' : 'storeTask' }}" />
        </div>
      </div>
    </form>
  </div>

  <div wire:ignore.self class="modal fade" id="addGroupTaskModal" tabindex="-1"
    aria-labelledby="addGroupTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="addGroupTaskModalLabel">Add Luminaries</h1>
          <div class="mt-1 ms-5">
            <div class="form-check form-check-inline">
              <input type="radio" id="luminary-type-group" wire:model="luminaryType" value="1"
                name="luminary-type" class="form-check-input" checked onclick="showGroup(event, this)">
              <label for="luminary-type-group" style="font-size: 14px" class="form-check-label">Sub Group</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="luminary-type-individual" wire:model="luminaryType" value="2"
                name="luminary-type" class="form-check-input" onclick="showIndividual(event, this)">
              <label for="luminary-type-individual" style="font-size: 14px" class="form-check-label">Individual</label>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="setLuminary" class="row gy-3">
            @csrf

            <div class="col-12" data-sub-group>
              <label for="sub_group_id">Sub Group</label>
              <select wire:model.live="lumSubGroup" id="sub_group_id" class="form-select">
                <option value="">Select sub-group</option>
                @foreach ($subGroups as $sg)
                  <option value="{{ $sg->id }}">{{ $sg->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 d-none" data-individual>
              <label for="luminaries_no">Luminary No.</label>
              <input type="text" wire:model.live="lumLuminaryNo" id="luminaries_no" class="form-control"
                placeholder="Enter luminary no.">
            </div>

            <div class="col-12 d-none" data-individual>
              <label for="dcu_no">DCU No.</label>
              <input type="text" wire:model.live="lumDcuNo" id="dcu_no" class="form-control"
                placeholder="Enter DCU no.">
            </div>

            <div class="col-12">
              <label for="sub_group_id">Dimming Schedule</label>
              <select wire:model.live="lumDimSchedule" id="dimming_schedule_id" class="form-select">
                <option value="">Select dimming schedule</option>
                @foreach ($schedules as $schedule)
                  <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="setLuminary" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.addEventListener('close-modal', (event) => {
        console.log(event.detail);
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });
    </script>
  @endscript

  @section('pageScripts')
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
      $('#dates').daterangepicker({
        ranges: {
          'Today': [moment(), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
        },
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true,
        "startDate": moment().startOf('month'),
        "endDate": moment().endOf('month')
      }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') +
          ' (predefined range: ' + label + ')');
      });

      const groupElements = document.querySelectorAll('[data-sub-group]');
      const individualElements = document.querySelectorAll('[data-individual]');

      function showGroup(event, radio) {
        if (radio.checked) {
          groupElements.forEach(el => {
            el.classList.remove('d-none');
          });
          individualElements.forEach(el => {
            el.classList.add('d-none');
          });
        }
      }

      function showIndividual(event, radio) {
        if (radio.checked) {
          groupElements.forEach(el => {
            el.classList.add('d-none');
          });
          individualElements.forEach(el => {
            el.classList.remove('d-none');
          });
        }
      }
    </script>
  @endsection
</div>
