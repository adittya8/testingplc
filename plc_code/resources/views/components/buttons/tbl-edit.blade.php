<button type="button" {{ $attributes->merge(['class' => 'btn btn-link link-secondary text-decoration-none']) }}
  wire:click.prevent="{{ $wireTarget }}({{ $modelId }})" wire:loading.attr="disabled"
  wire:target="{{ $wireTarget }}({{ $modelId }})" data-bs-toggle="tooltip" data-bs-title="Edit" title="Edit">
  <i class="fas fa-spinner fa-pulse" wire:loading wire:target="{{ $wireTarget }}({{ $modelId }})"></i>
  <i class="fas fa-pencil" wire:loading.remove wire:target="{{ $wireTarget }}({{ $modelId }})"></i>
</button>
