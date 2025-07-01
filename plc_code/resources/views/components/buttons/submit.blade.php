<button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="{{ $wireTarget }}">
  <i class="fas fa-spinner fa-pulse" wire:loading wire:target="{{ $wireTarget }}"></i>
  <i class="fas fa-save" wire:loading.remove wire:target="{{ $wireTarget }}"></i> Save
</button>
