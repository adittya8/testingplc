<div class="modal fade" id="{{ $id ?? 'formModal' }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
  aria-labelledby="{{ isset($id) ? $id . 'Title' : 'formModalTitle' }}" aria-hidden="true">
  <div class="modal-dialog @if (isset($modalSize)) {{ "modal-$modalSize" }} @endif"
    @if (isset($dialogWidth)) style="max-width: {{ $dialogWidth }}" @endif>
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="{{ isset($id) ? $id . 'Title' : 'formModalTitle' }}">
          {{ $title ?? 'Modal Title' }}
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
          @if (isset($resetOnClose) && $resetOnClose) wire:click="resetForm" @endif></button>
      </div>
      <div class="modal-body" id="{{ isset($id) ? $id . 'Body' : 'formModalBody' }}">{{ $body ?? '' }}</div>
    </div>
  </div>
</div>
