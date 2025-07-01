@if (isset($table) && $table)
  <div class="table-image-container">
    <img src="{{ asset($path) }}" alt="{{ $alt ?? '' }}" class="table-image">
    @if (isset($showDelete) &&
            $showDelete &&
            auth()->user()->can($permission))
      <a class="btn-image-delete" href="javascript:;" data-bs-toggle="tooltip" data-bs-title="Delete this image"
        onclick="deleteItem('{{ $route }}', '', 'Image')">
        <i class="fa-solid fa-trash-alt"></i>
      </a>
    @endif
  </div>
@else
  <div class="image-container">
    <img src="{{ asset($path) }}" alt="{{ $alt ?? '' }}" class="image">
    @if (isset($showDelete) &&
            $showDelete &&
            auth()->user()->can($permission))
      <a class="btn btn-danger btn-sm" href="javascript:;" data-bs-toggle="tooltip" data-bs-title="Delete image"
        onclick="deleteItem('{{ $route }}', '', 'Image')">
        <i class="fa-solid fa-trash-alt"></i> Delete Image
      </a>
    @endif
  </div>
@endif
