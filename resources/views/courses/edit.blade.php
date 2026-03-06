@extends('layout')
@section('content')

<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <i class="fas fa-edit"></i> Edit Course: {{ $course->title }}
    </h4>
    <p class="mb-0 mt-2 text-white-50">
      <i class="fas fa-info-circle"></i> Last updated: {{ $course->updated_at->format('F d, Y') }}
    </p>
  </div>
  <div class="card-body">
    
    <!-- Display validation errors -->
    @if ($errors->any())
      <div class="alert alert-danger alert-school">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
      <div class="alert alert-success alert-school">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('courses.update', $course->id) }}" method="post">
      @csrf
      @method("PUT")
      
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="title" class="form-label">Course Title *</label>
          <input type="text" 
                 name="title" 
                 id="title" 
                 class="form-control @error('title') is-invalid @enderror" 
                 value="{{ old('title', $course->title) }}" 
                 required 
                 placeholder="Enter course title"
                 autofocus>
          @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">Make it descriptive and engaging</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="category_id" class="form-label">Category *</label>
          <select name="category_id" 
                  id="category_id" 
                  class="form-select @error('category_id') is-invalid @enderror" 
                  required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="instructor_id" class="form-label">Instructor *</label>
          <select name="instructor_id" 
                  id="instructor_id" 
                  class="form-select @error('instructor_id') is-invalid @enderror" 
                  required>
            <option value="">Select Instructor</option>
            @foreach($instructors as $instructor)
              <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                {{ $instructor->name }} ({{ $instructor->email }})
              </option>
            @endforeach
          </select>
          @error('instructor_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="level" class="form-label">Difficulty Level *</label>
          <select name="level" 
                  id="level" 
                  class="form-select @error('level') is-invalid @enderror" 
                  required>
            <option value="">Select Level</option>
            @foreach($levels as $level)
              <option value="{{ $level }}" {{ old('level', $course->level) == $level ? 'selected' : '' }}>
                {{ ucfirst($level) }}
              </option>
            @endforeach
          </select>
          @error('level')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="price" class="form-label">Price ($) *</label>
          <input type="number" 
                 name="price" 
                 id="price" 
                 class="form-control @error('price') is-invalid @enderror" 
                 value="{{ old('price', number_format($course->price, 2)) }}" 
                 required 
                 min="0" 
                 step="0.01" 
                 placeholder="0.00">
          @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">Enter 0 for free course</div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Course Status</label>
          <div class="form-check mt-2">
            <input class="form-check-input @error('published') is-invalid @enderror" 
                   type="checkbox" 
                   name="published" 
                   id="published" 
                   value="1" 
                   {{ old('published', $course->published) ? 'checked' : '' }}>
            <label class="form-check-label" for="published">
              Publish this course
            </label>
            @error('published')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Leave unchecked to save as draft</div>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Course Description *</label>
        <textarea name="description" 
                  id="description" 
                  class="form-control @error('description') is-invalid @enderror" 
                  rows="5" 
                  required 
                  placeholder="Describe what students will learn in this course">{{ old('description', $course->description) }}</textarea>
        @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Be detailed about course content and learning outcomes</div>
      </div>

      <!-- Live Preview Section -->
      <div class="card mt-4 mb-4 bg-light" id="previewSection">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <h4 id="previewTitle">{{ $course->title }}</h4>
              <p id="previewDescription">{{ Str::limit($course->description, 200) }}</p>
            </div>
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h6>Details:</h6>
                  <ul class="list-unstyled">
                    <li><strong>Level:</strong> <span id="previewLevel">{{ ucfirst($course->level) }}</span></li>
                    <li><strong>Price:</strong> $<span id="previewPrice">{{ number_format($course->price, 2) }}</span></li>
                    <li><strong>Status:</strong> 
                      <span id="previewStatus" class="badge {{ $course->published ? 'badge-published' : 'badge-draft' }}">
                        {{ $course->published ? 'Published' : 'Draft' }}
                      </span>
                    </li>
                    <li><strong>Slug:</strong> <small>{{ $course->slug }}</small></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
        <div>
          <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info me-2">
            <i class="fas fa-eye"></i> View Course
          </a>
          <button type="submit" class="btn btn-custom">
            <i class="fas fa-save"></i> Update Course
          </button>
        </div>
      </div>
    </form>
   
  </div>
</div>

<!-- Enhanced Course Statistics Card -->
<div class="card mt-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-chart-bar"></i> Course Analytics
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="stat-card p-3 rounded text-center">
          <div class="stat-icon mb-2">
            <i class="fas fa-users fa-2x text-primary"></i>
          </div>
          <h6 class="text-muted">Enrollments</h6>
          <h3 class="text-primary">{{ $course->enrollments_count ?? 0 }}</h3>
          <small>Total Students</small>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card p-3 rounded text-center">
          <div class="stat-icon mb-2">
            <i class="fas fa-book-open fa-2x text-success"></i>
          </div>
          <h6 class="text-muted">Lessons</h6>
          <h3 class="text-success">{{ $course->lessons_count ?? 0 }}</h3>
          <small>Total Lessons</small>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card p-3 rounded text-center">
          <div class="stat-icon mb-2">
            <i class="fas fa-star fa-2x text-warning"></i>
          </div>
          <h6 class="text-muted">Average Rating</h6>
          <h3 class="text-warning">{{ number_format($course->average_rating ?? 0, 1) }}</h3>
          <small>Out of 5</small>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card p-3 rounded text-center">
          <div class="stat-icon mb-2">
            <i class="fas fa-clock fa-2x text-info"></i>
          </div>
          <h6 class="text-muted">Created</h6>
          <h5 class="text-info">{{ $course->created_at->format('M d, Y') }}</h5>
          <small>{{ $course->created_at->diffForHumans() }}</small>
        </div>
      </div>
    </div>

    <!-- Additional Course Info -->
    <div class="row mt-3">
      <div class="col-md-12">
        <div class="alert alert-info">
          <div class="row">
            <div class="col-md-3">
              <strong><i class="fas fa-tag"></i> Course ID:</strong> #{{ $course->id }}
            </div>
            <div class="col-md-3">
              <strong><i class="fas fa-link"></i> Slug:</strong> {{ $course->slug }}
            </div>
            <div class="col-md-3">
              <strong><i class="fas fa-calendar"></i> Last Updated:</strong> {{ $course->updated_at->diffForHumans() }}
            </div>
            <div class="col-md-3">
              <strong><i class="fas fa-toggle-{{ $course->published ? 'on text-success' : 'off text-danger' }}"></i> Status:</strong>
              @if($course->published)
                <span class="badge badge-published">Published</span>
              @else
                <span class="badge badge-draft">Draft</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Danger Zone - Delete Course -->
<div class="card mt-4 border-danger">
  <div class="card-header bg-danger text-white">
    <h5 class="card-title mb-0">
      <i class="fas fa-exclamation-triangle"></i> Danger Zone
    </h5>
  </div>
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h6>Delete this course</h6>
        <p class="text-muted">Once you delete a course, there is no going back. Please be certain.</p>
      </div>
      <div class="col-md-4 text-end">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
          <i class="fas fa-trash"></i> Delete Course
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the course <strong>"{{ $course->title }}"</strong>?</p>
        <p class="text-danger"><small>This action cannot be undone. All associated data (lessons, enrollments, etc.) will also be deleted.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Yes, Delete Course
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
  }
  
  .card-header {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
    padding: 20px;
    border-bottom: none;
  }
  
  .card-header.bg-danger {
    background: linear-gradient(145deg, #e74c3c, #c0392b) !important;
  }
  
  .card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1.5rem;
  }
  
  .card-body {
    padding: 30px;
  }
  
  .form-control:focus, .form-select:focus {
    border-color: #2ecc71;
    box-shadow: 0 0 0 0.25rem rgba(46, 204, 113, 0.25);
  }
  
  .form-check-input:checked {
    background-color: #2ecc71;
    border-color: #2ecc71;
  }
  
  .stat-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
  }
  
  .stat-card:hover {
    background: #e9ecef;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  
  .badge-published {
    background: #2ecc71;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
  }
  
  .badge-draft {
    background: #95a5a6;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
  }
  
  .btn-custom {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s;
  }
  
  .btn-custom:hover {
    background: linear-gradient(145deg, #27ae60, #229954);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
  }
  
  .btn-info {
    background: linear-gradient(145deg, #3498db, #2980b9);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
  }
  
  .btn-info:hover {
    background: linear-gradient(145deg, #2980b9, #2471a3);
    color: white;
  }
  
  .btn-danger {
    background: linear-gradient(145deg, #e74c3c, #c0392b);
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
  }
  
  .btn-danger:hover {
    background: linear-gradient(145deg, #c0392b, #a93226);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
  }
  
  .invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
  }
  
  .is-invalid {
    border-color: #dc3545;
  }
  
  #previewSection {
    border: 2px dashed #2ecc71;
    transition: all 0.3s ease;
  }
  
  #previewSection .card-header {
    background: linear-gradient(145deg, #3498db, #2980b9);
    padding: 10px 20px;
  }
  
  .text-white-50 {
    color: rgba(255,255,255,0.7) !important;
  }
  
  .stat-icon {
    color: #2ecc71;
  }
  
  .alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const levelSelect = document.getElementById('level');
    const priceInput = document.getElementById('price');
    const publishedCheckbox = document.getElementById('published');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    const previewLevel = document.getElementById('previewLevel');
    const previewPrice = document.getElementById('previewPrice');
    const previewStatus = document.getElementById('previewStatus');
    
    // Update preview on input changes
    function updatePreview() {
      previewTitle.textContent = titleInput.value || '{{ $course->title }}';
      
      let desc = descriptionInput.value || '{{ $course->description }}';
      previewDescription.textContent = desc.length > 200 ? desc.substring(0, 200) + '...' : desc;
      
      const selectedLevel = levelSelect.options[levelSelect.selectedIndex];
      previewLevel.textContent = selectedLevel ? selectedLevel.text : '{{ ucfirst($course->level) }}';
      
      let price = parseFloat(priceInput.value) || {{ $course->price }};
      previewPrice.textContent = price.toFixed(2);
      
      const isPublished = publishedCheckbox.checked;
      previewStatus.textContent = isPublished ? 'Published' : 'Draft';
      previewStatus.className = 'badge ' + (isPublished ? 'badge-published' : 'badge-draft');
    }
    
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    levelSelect.addEventListener('change', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    publishedCheckbox.addEventListener('change', updatePreview);
    
    // Format price input on blur
    priceInput.addEventListener('blur', function() {
      let value = parseFloat(this.value);
      if (!isNaN(value)) {
        this.value = value.toFixed(2);
        updatePreview();
      }
    });
    
    // Character counter for description
    const descriptionCounter = document.createElement('small');
    descriptionCounter.className = 'text-muted float-end';
    descriptionCounter.innerHTML = '{{ strlen($course->description) }}/500 characters';
    descriptionInput.parentNode.insertBefore(descriptionCounter, descriptionInput.nextSibling);
    
    descriptionInput.addEventListener('input', function() {
      const length = this.value.length;
      descriptionCounter.innerHTML = length + '/500 characters';
      descriptionCounter.style.color = length > 500 ? '#dc3545' : '#6c757d';
    });
    
    // FIXED: Unsaved changes warning
    let formChanged = false;
    const form = document.querySelector('form');
    const formInputs = form.querySelectorAll('input, select, textarea');
    const originalValues = {};
    
    // Store original values
    formInputs.forEach(input => {
      if (input.type === 'checkbox') {
        originalValues[input.name] = input.checked;
      } else if (input.type === 'select-one') {
        originalValues[input.name] = input.value;
      } else {
        originalValues[input.name] = input.value;
      }
    });
    
    // Mark form as changed when inputs change
    formInputs.forEach(input => {
      input.addEventListener('input', () => {
        formChanged = true;
      });
      input.addEventListener('change', () => {
        formChanged = true;
      });
    });
    
    // FIXED: Reset formChanged flag on successful form submission
    form.addEventListener('submit', function(e) {
      // Don't show warning when submitting
      formChanged = false;
    });
    
    // FIXED: Check if there are actual changes before showing warning
    window.addEventListener('beforeunload', function(e) {
      // Check if form has any actual changes
      let hasChanges = false;
      
      formInputs.forEach(input => {
        if (input.type === 'checkbox') {
          if (input.checked !== originalValues[input.name]) {
            hasChanges = true;
          }
        } else if (input.type === 'select-one') {
          if (input.value !== originalValues[input.name]) {
            hasChanges = true;
          }
        } else {
          if (input.value !== originalValues[input.name]) {
            hasChanges = true;
          }
        }
      });
      
      // Only show warning if there are actual changes AND form hasn't been submitted
      if (hasChanges && formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
      }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert:not(.alert-info)');
      alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      });
    }, 5000);
  });
</script>

@stop