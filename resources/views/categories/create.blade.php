@extends('layout')

@section('content')
<div class="category-card card">
  <div class="card-header">
    <h4 class="card-title">
      <i class="fas fa-folder-plus"></i> Create New Category
    </h4>
  </div>

  <div class="card-body">
    {{-- Validation Errors --}}
    @if ($errors->any())
      <div class="alert alert-danger alert-category">
        <strong>Whoops!</strong> There were some problems with your input.
        <ul class="mt-2 mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ url('categories') }}" method="POST" id="categoryForm">
      @csrf

      <div class="row">
        <div class="col-md-8 mb-3">
          <label for="name" class="form-label">Category Name *</label>
          <input type="text" 
                 name="name" 
                 id="name" 
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" 
                 required
                 placeholder="Enter category name (e.g., Web Development, Data Science)">
          <div class="form-text">Choose a clear and descriptive name for the category</div>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label for="slug" class="form-label">Slug</label>
          <input type="text" 
                 name="slug" 
                 id="slug" 
                 class="form-control @error('slug') is-invalid @enderror"
                 value="{{ old('slug') }}" 
                 placeholder="Auto-generated slug">
          <div class="form-text">URL-friendly version (auto-generated)</div>
          @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Category Description</label>
        <textarea name="description" 
                  id="description" 
                  class="form-control @error('description') is-invalid @enderror"
                  rows="4"
                  placeholder="Briefly describe what this category includes">{{ old('description') }}</textarea>
        <div class="form-text">Optional: Add a description to help organize courses</div>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Category Icon</label>
        <div class="row">
          <div class="col-md-6">
            <select name="icon" id="icon" class="form-select @error('icon') is-invalid @enderror">
              <option value="">Select an icon</option>
              <option value="fas fa-code" {{ old('icon') == 'fas fa-code' ? 'selected' : '' }}>💻 Programming</option>
              <option value="fas fa-chart-bar" {{ old('icon') == 'fas fa-chart-bar' ? 'selected' : '' }}>📊 Data Science</option>
              <option value="fas fa-paint-brush" {{ old('icon') == 'fas fa-paint-brush' ? 'selected' : '' }}>🎨 Design</option>
              <option value="fas fa-briefcase" {{ old('icon') == 'fas fa-briefcase' ? 'selected' : '' }}>💼 Business</option>
              <option value="fas fa-music" {{ old('icon') == 'fas fa-music' ? 'selected' : '' }}>🎵 Music</option>
              <option value="fas fa-language" {{ old('icon') == 'fas fa-language' ? 'selected' : '' }}>🌐 Languages</option>
              <option value="fas fa-heartbeat" {{ old('icon') == 'fas fa-heartbeat' ? 'selected' : '' }}>❤️ Health</option>
              <option value="fas fa-camera" {{ old('icon') == 'fas fa-camera' ? 'selected' : '' }}>📸 Photography</option>
            </select>
            @error('icon')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <div class="icon-preview p-3 text-center" id="iconPreview">
              <i class="fas {{ old('icon') ?: 'fa-folder' }} {{ old('icon') ? '' : 'text-muted' }}"></i>
              <p class="small mt-2">Icon Preview</p>
            </div>
          </div>
        </div>
        <div class="form-text">Choose an icon to represent this category visually</div>
      </div>

      <div class="mb-3">
        <label for="color" class="form-label">Category Color</label>
        <input type="color" 
               name="color" 
               id="color"
               class="form-control form-control-color @error('color') is-invalid @enderror"
               value="{{ old('color', '#3498db') }}">
        <div class="form-text">Select a color to distinguish this category</div>
        @error('color')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ url('categories') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Back
        </a>
        <button type="submit" class="btn btn-category">
          <i class="fas fa-plus-circle"></i> Create Category
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
  @vite(['resources/css/pages/categories.css'])
@endpush

@push('scripts')
  @vite(['resources/js/pages/categories.js'])
@endpush