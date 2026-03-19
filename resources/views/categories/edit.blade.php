@extends('layout')

@section('content')
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">
        <i class="fas fa-edit me-2"></i> Edit Category: {{ $category->name }}
      </h4>
      <a href="{{ url('categories/' . $category->slug) }}" class="btn btn-view">
        <i class="fas fa-eye me-2"></i> View
      </a>
    </div>
  </div>
  
  <div class="card-body">
    
    @if ($errors->any())
      <div class="alert alert-danger alert-school">
          <strong>Whoops!</strong> There were some problems with your input.
          <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <form action="{{ url('categories/' . $category->slug) }}" method="POST">
      @csrf
      @method("PATCH")

      <div class="row">
        <div class="col-md-8 mb-3">
          <label class="form-label">Category Name *</label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $category->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Slug *</label>
          <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                 value="{{ old('slug', $category->slug) }}" required>
          @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                  rows="4">{{ old('description', $category->description) }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Icon</label>
        <div class="row">
          <div class="col-md-6">
            <select name="icon" id="icon" class="form-select @error('icon') is-invalid @enderror">
              <option value="">None</option>
              <option value="fas fa-code" {{ old('icon', $category->icon) == 'fas fa-code' ? 'selected' : '' }}>💻 Programming</option>
              <option value="fas fa-chart-bar" {{ old('icon', $category->icon) == 'fas fa-chart-bar' ? 'selected' : '' }}>📊 Data</option>
              <option value="fas fa-paint-brush" {{ old('icon', $category->icon) == 'fas fa-paint-brush' ? 'selected' : '' }}>🎨 Design</option>
              <option value="fas fa-language" {{ old('icon', $category->icon) == 'fas fa-language' ? 'selected' : '' }}>🌐 Languages</option>
            </select>
            @error('icon')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <div class="icon-preview p-3 text-center" id="iconPreview">
              @if($category->icon)
                <i class="{{ $category->icon }} fa-2x" style="color: {{ $category->color ?? '#3498db' }};"></i>
                <p class="small mt-2">Icon Preview</p>
              @else
                <i class="fas fa-folder fa-2x text-muted"></i>
                <p class="small mt-2">Icon Preview</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Color</label>
        <div class="d-flex align-items-center">
          <input type="color" name="color" id="color" class="form-control form-control-color me-2"
                 value="{{ old('color', $category->color ?? '#3498db') }}">
          <input type="text" id="colorText" class="form-control" style="width: 100px;" 
                 value="{{ old('color', $category->color ?? '#3498db') }}" placeholder="#RRGGBB">
        </div>
        @error('color')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ url('categories') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Back
        </a>
        <button type="submit" class="btn btn-custom">
          <i class="fas fa-save me-2"></i> Update Category
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Courses --}}
@if($category->courses()->count())
<div class="card mt-4">
  <div class="card-header">
    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i> Courses in this category</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Course Title</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($category->courses()->latest()->take(5)->get() as $course)
          <tr>
            <td>
              <strong>{{ $course->title }}</strong>
              <br>
              <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
            </td>
            <td>
              @if($course->status === 'published')
                <span class="badge-published">Published</span>
              @else
                <span class="badge-draft">Draft</span>
              @endif
            </td>
            <td>
              <a href="{{ url('courses/' . $course->id) }}" class="btn btn-view btn-sm">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <a href="{{ url('courses?category=' . $category->slug) }}" class="btn btn-outline-primary btn-sm">
      <i class="fas fa-arrow-right me-2"></i> View All Courses
    </a>
  </div>
</div>
@endif
@endsection

@push('styles')
    @vite(['resources/css/pages/categories.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/categories.js'])
@endpush