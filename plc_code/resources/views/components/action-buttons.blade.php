<div class="btn-group action-btns">
  @isset($atstart)
    {{ $atstart }}
  @endisset

  @if (!isset($hide) || !in_array('edit', $hide))
    <a class="btn btn-sm btn-yellow" data-bs-toggle="tooltip" data-bs-title="Edit"
      @if (isset($editModal)) href="javascript:void(0)" onclick="openModal('{{ route($route . '.edit', $routeParams) }}', '{{ 'Edit ' . $modelName }}')"
        @else href="{{ route($route . '.edit', $routeParams) }}" @endif>
      <i class="fas fa-edit"></i>
      <span>Edit</span>
    </a>
  @endif

  @if (!isset($hide) || !in_array('status', $hide))
    @php
      $statusBtnText = 'Deactivate';
      $statusBtnClass = 'warning';
      $statusBtnIcon = 'fas fa-ban';
      $dataActive = 1;
      if ($item->deleted_at) {
          $statusBtnText = 'Activate';
          $statusBtnClass = 'success';
          $statusBtnIcon = 'fas fa-check';
          $dataActive = 0;
      }
    @endphp
    <a href="javascript:void(0)" class="btn btn-sm btn-{{ $statusBtnClass }}" data-bs-toggle="tooltip"
      data-bs-title="{{ $statusBtnText }}" data-active="{{ $dataActive }}"
      onclick="toggleItemStatus(this, '{{ $item->name ?? ($item->title ?? '') }}', '{{ route($route . '.status', $routeParams) }}')">
      <i class="{{ $statusBtnIcon }}"></i>
      <span>{{ $statusBtnText }}</span>
    </a>
  @endif

  @if (!isset($hide) || !in_array('delete', $hide))
    <a href="javascript:void(0)" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-title="Delete"
      onclick="deleteItem('{{ route($route . '.destroy', $routeParams) }}', '{{ $item->id }}', '{{ $modelName }}', false)">
      <i class="fas fa-trash-alt"></i>
      <span>Delete</span>
    </a>
  @endif

  @if (isset($slot))
    {{ $slot }}
  @endif
</div>
