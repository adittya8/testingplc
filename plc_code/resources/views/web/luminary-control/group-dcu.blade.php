<div class="modal fade" id="groupCommandModal" tabindex="-1" aria-labelledby="groupCommandModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="groupCommandModalLabel">{{ __('texts.group') }}</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12 col-md-5">
                <div class="row mb-3">
                  <label for="filter_luminary_no" class="col-4 col-form-label pe-0"
                    style="font-size: 13px">Group</label>
                  <div class="col-8">
                    <input type="text" class="form-control" placeholder="Group name" name="indivGroupName"
                      id="indivGroupName">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 dt-hide-search">
            <table class="table text-center" id="dimControlGroupTable">
              <thead>
                <tr>
                  <th>DCU Name.</th>
                  <th>DCU No.</th>
                  <th>Operation</th>
                </tr>
              </thead>
              <tbody>
                @if (config('dcus') && count(config('dcus')))
                  @foreach (config('dcus') as $dcu)
                    <tr id="indGrpRow-{{ $dcu->id }}">
                      <td>{{ $dcu->name }}</td>
                      <td>{{ $dcu->concentrator_no }}</td>
                      <td>
                        <a href="#" class="link-secondary"
                          onclick="openDim({{ $dcu->id }}, 'Dimming {{ $dcu->name }} ({{ $dcu->concentrator_no }})', 'group')">
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
