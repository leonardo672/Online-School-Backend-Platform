@extends('layout')
@section('content')

<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <i class="fas fa-graduation-cap"></i> Create New Course
    </h4>
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

    <!-- Success Message (if any) -->
    @if(session('success'))
      <div class="alert alert-success alert-school">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('courses.store') }}" method="post">
        @csrf
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="title" class="form-label">Course Title *</label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   class="form-control @error('title') is-invalid @enderror" 
                   value="{{ old('title') }}" 
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
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>
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
                   value="{{ old('price', '0.00') }}" 
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
                     {{ old('published') ? 'checked' : '' }}>
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
                    placeholder="Describe what students will learn in this course">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">Be detailed about course content and learning outcomes</div>
        </div>

        <!-- Live Preview Section (Optional) -->
        <div class="card mt-4 mb-4 bg-light" id="previewSection" style="display: none;">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <h4 id="previewTitle">Course Title</h4>
                <p id="previewDescription">Course description will appear here...</p>
              </div>
              <div class="col-md-4">
                <div class="card">
                  <div class="card-body">
                    <h6>Details:</h6>
                    <ul class="list-unstyled">
                      <li><strong>Level:</strong> <span id="previewLevel">-</span></li>
                      <li><strong>Price:</strong> $<span id="previewPrice">0.00</span></li>
                      <li><strong>Status:</strong> <span id="previewStatus">Draft</span></li>
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
            <button type="button" class="btn btn-info me-2" id="previewBtn">
              <i class="fas fa-eye"></i> Preview
            </button>
            <button type="submit" class="btn btn-custom">
              <i class="fas fa-save"></i> Create Course
            </button>
          </div>
        </div>
    </form>
   
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
    background: linear-gradient(145deg, #3498db, #2980b9);
    color: white;
    padding: 20px;
    border-bottom: none;
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
    border-color: #3498db;
    box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
  }
  
  .form-check-input:checked {
    background-color: #3498db;
    border-color: #3498db;
  }
  
  .btn-custom {
    background: linear-gradient(145deg, #3498db, #2980b9);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s;
  }
  
  .btn-custom:hover {
    background: linear-gradient(145deg, #2980b9, #2471a3);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
  }
  
  .btn-info {
    background-color: #17a2b8;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
  }
  
  .btn-info:hover {
    background-color: #138496;
    color: white;
  }
  
  .alert-school {
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
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
  
  .form-text {
    color: #6c757d;
    font-size: 0.875em;
    margin-top: 0.25rem;
  }
  
  #previewSection {
    transition: all 0.3s ease;
    border: 2px dashed #3498db;
  }
  
  #previewSection .card-header {
    background: linear-gradient(145deg, #17a2b8, #138496);
    padding: 10px 20px;
  }
</style>

<script>
  // Live preview functionality
  document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const levelSelect = document.getElementById('level');
    const priceInput = document.getElementById('price');
    const publishedCheckbox = document.getElementById('published');
    const previewBtn = document.getElementById('previewBtn');
    const previewSection = document.getElementById('previewSection');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    const previewLevel = document.getElementById('previewLevel');
    const previewPrice = document.getElementById('previewPrice');
    const previewStatus = document.getElementById('previewStatus');
    
    // Toggle preview section
    previewBtn.addEventListener('click', function() {
      if (previewSection.style.display === 'none') {
        previewSection.style.display = 'block';
        updatePreview();
        this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Preview';
      } else {
        previewSection.style.display = 'none';
        this.innerHTML = '<i class="fas fa-eye"></i> Preview';
      }
    });
    
    // Update preview on input changes
    function updatePreview() {
      previewTitle.textContent = titleInput.value || 'Course Title';
      previewDescription.textContent = descriptionInput.value || 'Course description will appear here...';
      
      // Update level
      const selectedLevel = levelSelect.options[levelSelect.selectedIndex];
      previewLevel.textContent = selectedLevel ? selectedLevel.text : '-';
      
      // Update price
      let price = parseFloat(priceInput.value) || 0;
      previewPrice.textContent = price.toFixed(2);
      
      // Update status
      previewStatus.textContent = publishedCheckbox.checked ? 'Published' : 'Draft';
      previewStatus.style.color = publishedCheckbox.checked ? '#28a745' : '#dc3545';
    }
    
    // Add event listeners
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    levelSelect.addEventListener('change', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    publishedCheckbox.addEventListener('change', updatePreview);
    
    // Auto-generate slug based on title (optional - can be used for SEO)
    titleInput.addEventListener('input', function() {
      const title = this.value;
      if (title.length > 0) {
        // You could show a slug preview if needed
        console.log('Title changed:', title);
      }
    });
    
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
    descriptionCounter.innerHTML = '0/500 characters';
    descriptionInput.parentNode.insertBefore(descriptionCounter, descriptionInput.nextSibling);
    
    descriptionInput.addEventListener('input', function() {
      const length = this.value.length;
      descriptionCounter.innerHTML = length + '/500 characters';
      if (length > 500) {
        descriptionCounter.style.color = '#dc3545';
      } else {
        descriptionCounter.style.color = '#6c757d';
      }
    });
    
    // Confirmation before leaving page with unsaved changes
    let formChanged = false;
    const form = document.querySelector('form');
    const formInputs = form.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
      input.addEventListener('change', () => {
        formChanged = true;
      });
    });
    
    window.addEventListener('beforeunload', function(e) {
      if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
      }
    });
    
    form.addEventListener('submit', () => {
      formChanged = false;
    });
  });
  
  // Auto-hide alerts after 5 seconds
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    });
  }, 5000);
</script>

@stop