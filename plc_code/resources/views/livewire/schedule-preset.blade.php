<div>
  @section('title', 'Schedule Presets')

  <div class="card mt-4">
    <div class="card-header py-3 px-4 border-bottom-0">
      <div class="row">
        <div class="col-6">
          <h2 class="fs-4 mb-0 card-title">Schedule Presets</h2>
        </div>
        <div class="col-6 text-end">
          <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#presetFormModal">
            <i class="fas fa-plus"></i> Add
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table text-center mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Preset</th>
            <th class="td-actions">Operations</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($presets as $preset)
            <tr wire:key="{{ $preset->id }}">
              <td>{{ $preset->name }}</td>
              <td>
                @php
                  $timesString = [];
                  foreach ($preset->schedule as $time) {
                      $timesString[] = "{$time['time']} ({$time['brightness']})";
                  }
                @endphp
                {{ implode(', ', $timesString) }}
              </td>
              <td>
                <div class="table-action-block">
                  <x-buttons.tbl-edit wire-target="editSchedulePreset" model-id="{{ $preset->id }}" />
                  <x-buttons.tbl-delete model-id="{{ $preset->id }}" function-name="deleteSchedulePreset" />
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">Nothing to show</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-4 mt-2">{{ $presets->links() }}</div>
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="presetFormModal" tabindex="-1" aria-labelledby="presetFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="presetFormModalLabel">{{ $modalTitle }}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateSchedulePreset' : 'storeSchedulePreset' }}"
            class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="presetAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="presetAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime1" class="required">Time 1</label>
              <input type="time" wire:model.live="time_1" id="presetAddTime1"
                class="form-control @error('time_1') is-invalid @enderror" placeholder="Enter time" readonly required>
              @error('time_1')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness1" class="required">Brightness 1</label>
              <input type="number" wire:model.live="brightness_1" id="presetAddBrightness1"
                class="form-control @error('brightness_1') is-invalid @enderror" placeholder="Enter time" required>
              @error('brightness_1')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime2" class="required">Time 2</label>
              <input type="time" wire:model.live="time_2" id="presetAddTime2"
                class="form-control @error('time_2') is-invalid @enderror" placeholder="Enter time" required>
              @error('time_2')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness2" class="required">Brightness 2</label>
              <input type="number" wire:model.live="brightness_2" id="presetAddBrightness2"
                class="form-control @error('brightness_2') is-invalid @enderror" placeholder="Enter brightness"
                required>
              @error('brightness_2')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime3" class="required">Time 3</label>
              <input type="time" wire:model.live="time_3" id="presetAddTime3"
                class="form-control @error('time_3') is-invalid @enderror" placeholder="Enter time" required>
              @error('time_3')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness3" class="required">Brightness 3</label>
              <input type="number" wire:model.live="brightness_3" id="presetAddBrightness3"
                class="form-control @error('brightness_3') is-invalid @enderror" placeholder="Enter brightness"
                required>
              @error('brightness_3')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime4" class="required">Time 4</label>
              <input type="time" wire:model.live="time_4" id="presetAddTime4"
                class="form-control @error('time_4') is-invalid @enderror" placeholder="Enter time" required>
              @error('time_4')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness4" class="required">Brightness 4</label>
              <input type="number" wire:model.live="brightness_4" id="presetAddBrightness4"
                class="form-control @error('brightness_4') is-invalid @enderror" placeholder="Enter brightness"
                required>
              @error('brightness_4')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime5" class="required">Time 5</label>
              <input type="time" wire:model.live="time_5" id="presetAddTime5"
                class="form-control @error('time_5') is-invalid @enderror" placeholder="Enter time" required>
              @error('time_5')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness5" class="required">Brightness 5</label>
              <input type="number" wire:model.live="brightness_5" id="presetAddBrightness5"
                class="form-control @error('brightness_5') is-invalid @enderror" placeholder="Enter brightness"
                required>
              @error('brightness_5')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddTime6" class="required">Time 6</label>
              <input type="time" wire:model.live="time_6" id="presetAddTime6"
                class="form-control @error('time_6') is-invalid @enderror" placeholder="Enter time" required>
              @error('time_6')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 col-md-6">
              <label for="presetAddBrightness6" class="required">Brightness 6</label>
              <input type="number" wire:model.live="brightness_6" id="presetAddBrightness6"
                class="form-control @error('brightness_6') is-invalid @enderror" placeholder="Enter brightness"
                required>
              @error('brightness_6')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            @if ($timeAscendingError)
              <div class="text-danger">{{ $timeAscendingError }}</div>
            @endif

            {{-- @for ($i = 2; $i <= 6; $i++)
              <div class="col-12 col-md-6">
                <label for="schedule-{{ $i }}" class="required">Time {{ $i }}</label>
                <input type="time" id="time-{{ $i }}" wire.model.live="time_{{ $i }}"
                  @if ($i == 1) readonly value="00:00" @endif
                  class="form-control @error("time_$i") is-invalid @enderror" placeholder="Enter time" required>
                @error("time_$i")
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 col-md-6">
                <label for="brightness-{{ $i }}" class="required">Brightness {{ $i }}</label>
                <input type="number" id="brightness-{{ $i }}"
                  wire.model="brightness_{{ $i }}"
                  class="form-control @error("brightness_$i") is-invalid @enderror" placeholder="Enter brightness"
                  required>
                @error("brightness_$i")
                  <div class="text-danger">{{ $message }}</div>
                @enderror
              </div>
            @endfor --}}

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateSchedulePreset' : 'storeSchedulePreset' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      window.deleteSchedulePreset = (presetId) => {
        Notiflix.Confirm.show(
          "Confirm Delete",
          `Are you sure to delete this Preset`,
          "Yes",
          "No",
          () => {
            addLoader(document.body);
            $wire.dispatch('delete-preset', {
              id: presetId
            });
          },
          () => {}, {
            titleColor: colors.danger,
          }
        );
      }

      const PresetModal = document.querySelector('#presetFormModal')
      PresetModal.addEventListener('hidden.bs.modal', event => {
        $wire.dispatch('reset-form');
      });

      window.addEventListener('close-modal', (event) => {
        var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
        modal.hide();
      });

      window.addEventListener('open-modal', (event) => {
        console.log(event.detail);
        var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
        modal.show();
      });
    </script>
  @endscript
</div>
