<div>
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
                      <input type="text" class="form-control" placeholder="Lunimary number" wire:model="luminaryNo">
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-5">
                  <div class="row mb-3">
                    <label for="filter_concentrator_no" class="col-4 col-form-label pe-0" style="font-size: 13px">DCU
                      No</label>
                    <div class="col-8">
                      <input type="text" class="form-control" placeholder="DCU number" wire:model="concentratorNo">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-21">
              <table class="table text-center">
                <thead>
                  <tr>
                    <th>Luminary No.</th>
                    <th>DCU No.</th>
                    <th>Operation</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($luminaries as $lum)
                    <tr wire:key="{{ $lum->id }}">
                      <td>{{ $lum->node_id }}</td>
                      <td>{{ $lum->concentrator->concentrator_no }}</td>
                      <td>
                        <a href="#" class="link-secondary"
                          onclick="openDim({{ $lum->id }}, '{{ $lum->node_id }}')">
                          <i class="fas fa-sliders"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <div>
                {{ $luminaries->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-none custom-modal" id="test">
    <div class="card custom-modal-dialog">
      <div class="card-header border-0">
        <h3 class="fs-5" id="customModalTitle">Dimming</h3>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center">
          <label for="lightStatus" class="me-2">Status</label>
          <div class="form-check form-check-inline form-switch me-2">
            <input class="form-check-input" type="checkbox" role="switch" id="lightStatus">
          </div>
          <i class="far fa-lightbulb text-secondary me-4"></i>
          <div class="me-5">Brightness</div>
          <div data-input-range class="input-range-group flex-grow-1">
            <span class="value border px-4 py-1">100</span>
            <input type="range" class="form-range" value="100" id="individualDimRange">
          </div>
        </div>

        <div class="text-center mt-3">
          <button class="btn btn-outline-secondary" data-custom-dismiss="#test">Cancel</button>
          <button class="btn btn-primary" id="customModalConfirm">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      //   const test = document.querySelector('#test');
      //   window.luminaryId = null;

      //   window.openDim = (id, nodeId) => {
      //     test.classList.remove("d-none");
      //     document.querySelector('#customModalTitle').innerText = `Dimming ${nodeId}`;
      //     luminaryId = id;
      //   }

      //   document.querySelector('#customModalConfirm').addEventListener('click', () => {
      //     let url = "{{ route('dimming-individual.luminary', ['id' => ':id']) }}";
      //     url = url.replace(':id', luminaryId);
      //     const data = new FormData();
      //     data.append('dim', document.querySelector('#individualDimRange').value);

      //     addLoader(document.body)

      //     axios({
      //       method: 'POST',
      //       url: url,
      //       data: data,
      //     }).then(res => {
      //       test.classList.add('d-none');
      //       showToast(res.data.message ?? 'Dimming set successfully.');
      //       removeLoader(document.body)
      //     }).catch(err => {
      //       removeLoader(document.body)
      //       showToast(err.response & err.response.data & err.response.data.message ? err.response.data.message :
      //         'Something went wrong!', 'danger');
      //     })
      //   });

      //   const dismiss = document.querySelector('[data-custom-dismiss]');
      //   dismiss.addEventListener('click', () => {
      //     test.classList.add('d-none');
      //   })
    </script>
  @endscript
</div>
