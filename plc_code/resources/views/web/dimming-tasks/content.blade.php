<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>Sub-Group</th>
      <th>Group</th>
      <th>Number of RTUs</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($subGroups as $sg)
      <tr>
        <td>
          <input type="checkbox" name="sg_ids[]" id="sg-check-{{ $sg->id }}" value="{{ $sg->id }}"
            class="form-check-input" @checked($sg->checked)>
        </td>
        <td>
          <label for="sg-check-{{ $sg->id }}">{{ $sg->name }}</label>
        </td>
        <td>{{ $sg->group?->name }}</td>
        <td>{{ $sg->rtus_count }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
