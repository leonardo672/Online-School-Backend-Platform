@extends('layout')
@section('content')

<div class="card">
  <div class="card-header">
    <h4 class="card-title">
      <i class="fas fa-user-plus"></i> Create New Enrollment
    </h4>
  </div>
  <div class="card-body">
    
    <!-- Display validation errors -->
    @if ($errors->any())
      <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <!-- Display success message -->
    @if(session('success'))
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <!-- Display error message -->
    @if(session('error'))
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
      </div>
    @endif

    <form action="{{ route('enrollments.store') }}" method="POST">
      @csrf
      
      <div class="mb-3">
        <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
          <option value="">Select User</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
              {{ $user->name }} ({{ $user->email }})
            </option>
          @endforeach
        </select>
        @error('user_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
        <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
          <option value="">Select Course</option>
          @foreach($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
              {{ $course->title }}
            </option>
          @endforeach
        </select>
        @error('course_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="enrolled_at" class="form-label">Enrollment Date <span class="text-danger">*</span></label>
        <input type="datetime-local" 
               name="enrolled_at" 
               id="enrolled_at" 
               class="form-control @error('enrolled_at') is-invalid @enderror" 
               value="{{ old('enrolled_at', now()->format('Y-m-d\TH:i')) }}" 
               required>
        @error('enrolled_at')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ route('enrollments.index') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Back
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Create Enrollment
        </button>
      </div>
    </form>
   
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Set default enrollment date if not set
    const enrolledAtInput = document.getElementById('enrolled_at');
    if (!enrolledAtInput.value) {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      enrolledAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
  });
</script>

@stop