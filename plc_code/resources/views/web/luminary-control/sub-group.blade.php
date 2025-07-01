<div class="modal fade" id="subGroupCommandModal" tabindex="-1" aria-labelledby="subGroupCommandModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="subGroupCommandModalLabel">{{ __('texts.sub_group') }}</h1>
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
                    <input type="text" class="form-control" placeholder="Group name" name="indivSubGroupName"
                      id="indivSubGroupName">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 dt-hide-search">
            <table class="table text-center" id="dimControlGroupTable">
              <thead>
                <tr>
                  <th>Sub-Group</th>
                  <th>Group</th>
                  <th>RTU #</th>
                  <th>Operation</th>
                </tr>
              </thead>
              <tbody>
                @if (config('subGroups') && count(config('subGroups')))
                  @foreach (config('subGroups') as $sGroup)
                    <tr id="indGrpRow-{{ $sGroup->id }}">
                      <td>{{ $sGroup->name }}</td>
                      <td>{{ $sGroup->group?->name }}</td>
                      <td>{{ $sGroup->rtus_count }}</td>
                      <td>
                        {{-- <a href="#" class="link-secondary"
                          onclick="openDim({{ $sGroup->id }}, 'Dimming {{ $sGroup->name }}', 'sub-group')">
                          <i class="fas fa-sliders"></i>
                        </a> --}}

                        <a href="#" class="link-secondary"
                          onclick="openDimModal({{ $sGroup->id }}, 'Dimming Sub-group: {{ $sGroup->name }}', 'sub-group', '#subGroupCommandModal')">
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
