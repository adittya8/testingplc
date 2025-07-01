@if (isset($layout) && !$layout)
  <div class="row justify-content-center mt-5">
    <div class="col-12 col-md-6 col-lg-4">
      <p class="fs-3 fw-bold mb-2">403</p>
      <p>{{ $message ?? 'You are not permitted to perform this action!' }}</p>
    </div>
  </div>
@else
  @extends('layouts.layout')
  @section('title', 'Forbidden')
  @section('content')
    <div class="row justify-content-center mt-5">
      <div class="col-12 col-md-6 col-lg-4">
        <p class="fs-3 fw-bold mb-2">403</p>
        <p>{{ $message ?? 'You are not permitted to perform this action!' }}</p>
      </div>
    </div>
  @endsection
@endif
