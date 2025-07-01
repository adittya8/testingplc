<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>RTU</th>
      <th>DCU</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($rtus as $rtu)
      <tr>
        <td>
          <input type="checkbox" name="rtu_ids[]" id="rtu-check-{{ $rtu->id }}" value="{{ $rtu->id }}"
            class="form-check-input" @checked($rtu->checked)>
        </td>
        <td>
          <label for="rtu-check-{{ $rtu->id }}">{{ $rtu->name }} ({{ $rtu->code }})</label>
        </td>
        <td>
          @if ($rtu->concentrator)
            {{ $rtu->concentrator->name }} ({{ $rtu->concentrator->concentrator_no }})
          @endif
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
