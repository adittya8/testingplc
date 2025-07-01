<div>
  @php
    $property = $propertyName;
    if (isset($model)) {
        $property =
            isset($isPage) && $isPage && isset($model->data[$propertyName])
                ? $model->data[$propertyName]
                : $model->{$propertyName};
    }
    $id = isset($id) ? $id : $propertyName;
    $name = isset($name) ? $name : $propertyName;
  @endphp
  @if (isset($model) && $property && file_exists(public_path('storage/' . getStoragePath($model) . $property)))
    <input type="file" wire:model="{{ $name }}" id="{{ $id }}" class="form-control d-none"
      accept="{{ $acceptedFileTypes }}" onchange="updatePreview('{{ $id }}')"
      @if (isset($required) && $required) required @endif>
    <div class="form-preview">
      <img src="{{ asset(getFileAssetPath($model, $propertyName, isset($isPage) && $isPage)) }}" alt=""
        onclick="document.querySelector('#{{ $id }}').click()" data-preview="{{ $id }}">
      <div onclick="document.querySelector('#{{ $id }}').click()">Change Image</div>
    </div>
  @else
    <input type="file" wire:model="{{ $name }}" id="{{ $id }}" class="form-control"
      accept="{{ $acceptedFileTypes }}" @if (isset($required) && $required) required @endif>
  @endif
</div>
