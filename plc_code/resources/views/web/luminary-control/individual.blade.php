<div class="modal fade" id="individualCommandModal" tabindex="-1" aria-labelledby="individualCommandModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="individualCommandModalLabel">{{ __('texts.individual') }}</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-5">
                <div class="row mb-3">
                  <label for="filter_luminary_no" class="col-4 col-form-label pe-0" style="font-size: 13px">Luminary
                    No</label>
                  <div class="col-8">
                    <input type="text" class="form-control" placeholder="Lunimary number" name="indivLuminaryNo"
                      id="indivLuminaryNo">
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-5">
                <div class="row mb-3">
                  <label for="filter_concentrator_no" class="col-4 col-form-label pe-0" style="font-size: 13px">DCU
                    No</label>
                  <div class="col-8">
                    <input type="text" class="form-control" placeholder="DCU number" name="indivConcentratorNo"
                      id="indivConcentratorNo">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 dt-hide-search">
            <table class="table text-center" id="individualLumTable">
              <thead>
                <tr>
                  <th>RTU No.</th>
                  <th>DCU No.</th>
                  <th>Operation</th>
                </tr>
              </thead>
              <tbody>
                @if (config('rtus') && count(config('rtus')))
                  @foreach (config('rtus') as $rtu)
                    <tr wire:key="{{ $rtu->id }}">
                      <td>{{ $rtu->name }} ({{ $rtu->code }})</td>
                      <td>{{ $rtu->concentrator->name }} ({{ $rtu->concentrator->concentrator_no }})</td>
                      <td>
                        {{-- <a href="#" class="link-secondary" 
                          onclick="openDim({{ $rtu->id }}, 'Dimming {{ $rtu->name }} ({{ $rtu->code }})', 'individual')">
                          <i class="fas fa-sliders"></i>
                        </a> --}}
                        <a href="#" class="link-secondary"
                          onclick="openDimModal({{ $rtu->id }}, 'Dimming RTU: {{ $rtu->name }} ({{ $rtu->code }})', 'individual', '#individualCommandModal')">
                          <i class="fas fa-sliders"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
