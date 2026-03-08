@extends('layout')
@section('content')

<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <i class="fas fa-edit"></i> Edit Enrollment
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

    <form action="{{ url('enrollments/' . $enrollment->id) }}" method="post">
      @csrf
      @method('PUT')
      
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="user_id" class="form-label">User *</label>
          <select name="user_id" id="user_id" class="form-select" required>
            <option value="">Select User</option>
            @foreach($users ?? [] as $user)
              <option value="{{ $user->id }}" {{ old('user_id', $enrollment->user_id) == $user->id ? 'selected' : '' }}>
                {{ $user->name }} ({{ $user->email }})
              </option>
            @endforeach
          </select>
          @if(empty($users))
            <div class="alert alert-warning mt-2">
              <i class="fas fa-exclamation-triangle"></i> No users found. 
              <a href="{{ url('users/create') }}" class="alert-link">Create a user first</a>.
            </div>
          @endif
          <div class="form-text">Select the user to enroll in a course</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="course_id" class="form-label">Course *</label>
          <select name="course_id" id="course_id" class="form-select" required>
            <option value="">Select Course</option>
            @foreach($courses ?? [] as $course)
              <option value="{{ $course->id }}" {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
                {{ $course->title }} ({{ $course->code ?? 'N/A' }})
              </option>
            @endforeach
          </select>
          @if(empty($courses))
            <div class="alert alert-warning mt-2">
              <i class="fas fa-exclamation-triangle"></i> No courses found. 
              <a href="{{ url('courses/create') }}" class="alert-link">Create a course first</a>.
            </div>
          @endif
          <div class="form-text">Select the course for enrollment</div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="enrolled_at" class="form-label">Enrollment Date *</label>
          <input type="datetime-local" name="enrolled_at" id="enrolled_at" class="form-control" 
                 value="{{ old('enrolled_at', $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
          <div class="form-text">Date and time when the user enrolled</div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Enrollment Status</label>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                   value="1" {{ old('is_active', $enrollment->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
              Mark as active enrollment
            </label>
            <div class="form-text">Uncheck to mark as inactive/pending</div>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label for="notes" class="form-label">Notes (Optional)</label>
        <textarea name="notes" id="notes" class="form-control" rows="3" 
                  placeholder="Any additional notes about this enrollment">{{ old('notes', $enrollment->notes ?? '') }}</textarea>
        <div class="form-text">Optional notes about the enrollment</div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <div class="card bg-light">
            <div class="card-body">
              <h6><i class="fas fa-user"></i> Current User Information</h6>
              <p class="mb-1"><strong>Name:</strong> {{ $enrollment->user->name ?? 'N/A' }}</p>
              <p class="mb-1"><strong>Email:</strong> {{ $enrollment->user->email ?? 'N/A' }}</p>
              <p class="mb-0"><strong>User ID:</strong> {{ $enrollment->user_id }}</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 mb-3">
          <div class="card bg-light">
            <div class="card-body">
              <h6><i class="fas fa-book"></i> Current Course Information</h6>
              <p class="mb-1"><strong>Title:</strong> {{ $enrollment->course->title ?? 'N/A' }}</p>
              <p class="mb-1"><strong>Code:</strong> {{ $enrollment->course->code ?? 'N/A' }}</p>
              <p class="mb-0"><strong>Course ID:</strong> {{ $enrollment->course_id }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="alert alert-info">
        <h6><i class="fas fa-info-circle"></i> Enrollment Information</h6>
        <ul class="mb-0">
          <li>Created: {{ $enrollment->created_at->format('M d, Y \a\t h:i A') }}</li>
          <li>Last Updated: {{ $enrollment->updated_at->format('M d, Y \a\t h:i A') }}</li>
          <li>Changing user or course will move this enrollment to different user/course</li>
        </ul>
      </div>

      <div class="d-flex justify-content-between">
        <div>
          <a href="{{ url('enrollments') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Enrollments
          </a>
          <a href="{{ url('enrollments/' . $enrollment->id) }}" class="btn btn-view ms-2">
            <i class="fas fa-eye"></i> View Details
          </a>
        </div>
        <div>
          <button type="reset" class="btn btn-outline-secondary me-2">
            <i class="fas fa-redo"></i> Reset Changes
          </button>
          <button type="submit" class="btn btn-update">
            <i class="fas fa-save"></i> Update Enrollment
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
    max-width: 900px;
    margin: 0 auto;
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
  
  .btn-update {
    background: linear-gradient(145deg, #f39c12, #e67e22);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .btn-update:hover {
    background: linear-gradient(145deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
    color: white;
  }
  
  .btn-secondary {
    background: linear-gradient(145deg, #6c757d, #495057);
    color: white;
    border: none;
  }
  
  .btn-secondary:hover {
    background: linear-gradient(145deg, #495057, #343a40);
    color: white;
  }
  
  .btn-view {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
    border: none;
  }
  
  .btn-view:hover {
    background: linear-gradient(145deg, #27ae60, #219653);
    color: white;
    transform: translateY(-2px);
  }
  
  .form-check-input:checked {
    background-color: #3498db;
    border-color: #3498db;
  }
  
  .alert-school {
    border-left: 4px solid #3498db;
    background-color: #f0f9ff;
  }
  
  .alert-info {
    background-color: #e7f3ff;
    border-color: #b6d4fe;
    color: #084298;
  }
  
  .alert-warning {
    background-color: #fff3cd;
    border-color: #ffecb5;
    color: #664d03;
  }
  
  .alert-warning a {
    color: #664d03;
    text-decoration: underline;
  }
  
  .bg-light {
    background-color: #f8f9fa !important;
  }
  
  .form-text {
    font-size: 0.875rem;
    color: #6c757d;
  }
  
  .current-info-card {
    border-left: 4px solid #3498db;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const userIdSelect = document.getElementById('user_id');
    const courseIdSelect = document.getElementById('course_id');
    const form = document.getElementById('enrollmentForm');
    const currentEnrollmentId = {{ $enrollment->id ?? 'null' }};
    
    let duplicateCheckTimeout;
    let warningShown = false;

    // Function to check for duplicate enrollment
    function checkDuplicateEnrollment() {
      const userId = userIdSelect.value;
      const courseId = courseIdSelect.value;
      
      // Clear previous timeout
      clearTimeout(duplicateCheckTimeout);
      
      // Remove existing warnings
      removeDuplicateWarning();
      
      // Only check if both fields have values
      if (!userId || !courseId) {
        return;
      }
      
      // Show checking indicator
      showCheckingIndicator();
      
      // Debounce the check to avoid too many requests
      duplicateCheckTimeout = setTimeout(() => {
        // Make AJAX call to check enrollment
        fetch(`/enrollments/check-duplicate?user_id=${userId}&course_id=${courseId}&exclude_id=${currentEnrollmentId}`)
          .then(response => response.json())
          .then(data => {
            // Remove checking indicator
            removeCheckingIndicator();
            
            if (data.exists) {
              showDuplicateWarning();
              warningShown = true;
            } else {
              warningShown = false;
            }
          })
          .catch(error => {
            console.error('Error checking enrollment:', error);
            removeCheckingIndicator();
          });
      }, 500); // Wait 500ms after last change before checking
    }

    // Show checking indicator
    function showCheckingIndicator() {
      removeCheckingIndicator();
      
      const indicator = document.createElement('div');
      indicator.className = 'alert alert-info checking-indicator mt-2';
      indicator.innerHTML = `
        <i class="fas fa-spinner fa-spin"></i> 
        Checking for existing enrollment...
      `;
      
      const formGroup = courseIdSelect.closest('.mb-3');
      formGroup.appendChild(indicator);
    }

    // Remove checking indicator
    function removeCheckingIndicator() {
      document.querySelector('.checking-indicator')?.remove();
    }

    // Show duplicate warning
    function showDuplicateWarning() {
      removeDuplicateWarning();
      
      const warningDiv = document.createElement('div');
      warningDiv.className = 'alert alert-warning duplicate-warning mt-2';
      warningDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Warning:</strong> This user is already enrolled in this course.
        <button type="button" class="btn btn-sm btn-warning ms-2" onclick="ignoreDuplicateWarning()">
          Continue Anyway
        </button>
      `;
      
      const formGroup = courseIdSelect.closest('.mb-3');
      formGroup.appendChild(warningDiv);
    }

    // Remove duplicate warning
    function removeDuplicateWarning() {
      document.querySelector('.duplicate-warning')?.remove();
    }

    // Ignore warning and allow form submission
    window.ignoreDuplicateWarning = function() {
      removeDuplicateWarning();
      warningShown = false;
      
      // Add hidden input to indicate warning was ignored
      let ignoreInput = document.getElementById('ignore_duplicate');
      if (!ignoreInput) {
        ignoreInput = document.createElement('input');
        ignoreInput.type = 'hidden';
        ignoreInput.name = 'ignore_duplicate';
        ignoreInput.id = 'ignore_duplicate';
        ignoreInput.value = '1';
        form.appendChild(ignoreInput);
      }
    };

    // Handle form submission
    form.addEventListener('submit', function(e) {
      if (warningShown) {
        e.preventDefault();
        // Show confirmation dialog
        if (confirm('This user might already be enrolled in this course. Continue anyway?')) {
          ignoreDuplicateWarning();
          form.submit();
        }
      }
    });

    // Add event listeners for changes
    userIdSelect.addEventListener('change', checkDuplicateEnrollment);
    courseIdSelect.addEventListener('change', checkDuplicateEnrollment);

    // Initial check if both fields have values (for edit form)
    if (userIdSelect.value && courseIdSelect.value) {
      checkDuplicateEnrollment();
    }

    // Remove invalid class on input
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
      input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
      });
    });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.duplicate-warning):not(.checking-indicator)');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }, 5000);
    });
  });
</script>
@stop