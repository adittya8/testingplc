@extends('layouts.layout')
@section('title', $pageTitle)
@section('content')
  @foreach ($components as $comp)
    @can($comp['permission'])
      <div class="{{ $loop->first ? 'mt-4' : 'mt-5' }}">
        <livewire:is :component="$comp['name']" wire:key="{{ getSlug($comp['name']) }}" />
      </div>
    @endcan
  @endforeach
@endsection

@section('pageScripts')
  <script>
    window.addEventListener('close-modal', (event) => {
      var modal = bootstrap.Modal.getInstance(document.querySelector(`#${event.detail.modalId}`));
      modal.hide();
    });

    window.addEventListener('open-modal', (event) => {
      var modal = new bootstrap.Modal(document.querySelector(`#${event.detail.modalId}`));
      modal.show();
    });
  </script>
@endsection
