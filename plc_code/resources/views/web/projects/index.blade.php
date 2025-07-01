@extends('layouts.layout')
@section('title', 'Projects')
@section('content')
  <div class="container mt-4 px-3">
    <div class="row">
      <div class="col-6">
        <h1 class="fs-5 mb-4">{{ __('texts.project_name') }}:</h1>
      </div>
      <div class="col-6 text-end">
        <a href="#" class="btn btn-outline-success"><i class="fas fa-plus"></i> Add Project</a>
      </div>
    </div>

    <div class="row">
      @foreach ($projects as $project)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
          <a href="{{ route('dashboard', ['project' => $project]) }}" class="text-decoration-none">
            <div class="card company-card">
              <div class="card-header border-bottom-0 p-0">
                <img src="{{ asset($project->image ? getFileAssetPath($project, 'image') : 'images/city.jpg') }}"
                  alt="" class="company-card-img">
              </div>
              <div class="card-body">
                <p class="title">{{ $project->name }}</p>
                <p class="company-card-info">DCUs: {{ $project->concentrators_count }}</p>
                <p class="company-card-info mb-0">Light Sources: {{ $project->luminaries_count }}</p>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>

  <div wire:ignore.self class="modal fade" id="projectFormModal" tabindex="-1" aria-labelledby="projectFormModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="projectFormModalLabel">Add New Project</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form wire:submit="{{ isset($editMode) && $editMode ? 'updateProject' : 'storeProject' }}" class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="projectAddName" class="required">Name</label>
              <input type="text" wire:model.live="name" id="projectAddName"
                class="form-control @error('name') is-invalid @enderror" placeholder="Enter project name" required>
              @error('name')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="projectAddImage">Image</label>
              <input type="file" wire:model.live="image" id="projectAddImage"
                class="form-control @error('image') is-invalid @enderror">
              @error('image')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-end">
              <x-buttons.submit wire-target="{{ $editMode ? 'updateProject' : 'storeProject' }}" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
