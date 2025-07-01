<button type="button" {{ $attributes->merge(['class' => 'btn btn-link link-danger text-decoration-none']) }}
  onclick="window['{{ $functionName }}']({{ $modelId }})" title="Delete" data-bs-toggle="tooltip"
  data-bs-title="Delete">
  <i class="fas fa-trash-alt"></i>
</button>
