@extends('layout')

@section('title', 'Create Lesson Progress')

@section('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 2rem;
    }

    .form-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .form-header h2 {
        color: #333;
        font-weight: 600;
    }

    .form-header p {
        color: #666;
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
    }

    .btn-submit {
        background: linear-gradient(45deg, #007bff, #0069d9);
        border: none;
        border-radius: 8px;
        padding: 0.6rem 2rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.3);
    }

    .btn-cancel {
        border-radius: 8px;
        padding: 0.6rem 2rem;
        font-weight: 500;
        margin-left: 1rem;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .info-card i {
        color: #007bff;
        margin-right: 0.5rem;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .duplicate-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        display: none;
    }

    .duplicate-warning.show {
        display: block;
    }

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }

    .toast-error {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
    }

    .toast-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        color: white;
    }

    .preview-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }

    .preview-card.show {
        display: block;
    }

    .preview-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }

    .preview-item:last-child {
        border-bottom: none;
    }

    .preview-label {
        font-weight: 600;
        width: 120px;
        color: #495057;
    }

    .preview-value {
        color: #212529;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle text-primary me-2"></i>Create Lesson Progress</h2>
            <p>Track a student's progress for a specific lesson</p>
        </div>

        {{-- Duplicate Warning --}}
        <div id="duplicateWarning" class="duplicate-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="duplicateMessage"></span>
        </div>

        <form id="progressForm" action="{{ route('lesson-progress.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i>Select User
                    </label>
                    <select name="user_id" id="userSelect" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">Choose a user...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    @selected(old('user_id') == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="fas fa-video me-1 text-primary"></i>Select Lesson
                    </label>
                    <select name="lesson_id" id="lessonSelect" class="form-select @error('lesson_id') is-invalid @enderror" required>
                        <option value="">Choose a lesson...</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" 
                                    data-title="{{ $lesson->title }}"
                                    data-course="{{ $lesson->course->title ?? 'No Course' }}"
                                    data-position="{{ $lesson->position }}"
                                    @selected(old('lesson_id') == $lesson->id)>
                                {{ $lesson->title }} 
                                @if($lesson->course)
                                    ({{ $lesson->course->title }})
                                @endif
                                - Position: {{ $lesson->position }}
                            </option>
                        @endforeach
                    </select>
                    @error('lesson_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="completed" class="form-check-input" 
                               id="completedSwitch" value="1" {{ old('completed') ? 'checked' : '' }}>
                        <label class="form-check-label" for="completedSwitch">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Mark as completed immediately
                        </label>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        If checked, completion timestamp will be set to now
                    </small>
                </div>
            </div>

            {{-- Preview Card --}}
            <div id="previewCard" class="preview-card">
                <h6 class="mb-3"><i class="fas fa-eye me-2"></i>Preview Selection</h6>
                <div class="preview-item">
                    <span class="preview-label">User:</span>
                    <span class="preview-value" id="previewUser">Not selected</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Lesson:</span>
                    <span class="preview-value" id="previewLesson">Not selected</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Status:</span>
                    <span class="preview-value" id="previewStatus">In Progress</span>
                </div>
            </div>

            <div class="info-card">
                <i class="fas fa-lightbulb"></i>
                <strong>Note:</strong> You can track student progress through lessons. 
                Progress can be updated later as students complete their lessons.
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('lesson-progress.index') }}" class="btn btn-secondary btn-cancel" id="cancelBtn">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
                <div>
                    <button type="button" class="btn btn-info me-2" id="checkDuplicateBtn">
                        <i class="fas fa-search me-1"></i>Check Duplicate
                    </button>
                    <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Create Progress
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ========== STATE MANAGEMENT ==========
    let formState = {
        userSelected: false,
        lessonSelected: false,
        isDuplicate: false,
        formChanged: false
    };

    // ========== DOM ELEMENTS ==========
    const elements = {
        form: document.getElementById('progressForm'),
        userSelect: document.getElementById('userSelect'),
        lessonSelect: document.getElementById('lessonSelect'),
        completedSwitch: document.getElementById('completedSwitch'),
        duplicateWarning: document.getElementById('duplicateWarning'),
        duplicateMessage: document.getElementById('duplicateMessage'),
        loadingOverlay: document.getElementById('loadingOverlay'),
        previewCard: document.getElementById('previewCard'),
        previewUser: document.getElementById('previewUser'),
        previewLesson: document.getElementById('previewLesson'),
        previewStatus: document.getElementById('previewStatus'),
        checkDuplicateBtn: document.getElementById('checkDuplicateBtn'),
        submitBtn: document.getElementById('submitBtn'),
        cancelBtn: document.getElementById('cancelBtn')
    };

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        initializePreview();
        checkForOldInputs();
    });

    function initializeEventListeners() {
        // User select change
        elements.userSelect.addEventListener('change', function() {
            formState.userSelected = this.value !== '';
            formState.formChanged = true;
            updatePreview();
            checkDuplicate();
        });

        // Lesson select change
        elements.lessonSelect.addEventListener('change', function() {
            formState.lessonSelected = this.value !== '';
            formState.formChanged = true;
            updatePreview();
            checkDuplicate();
        });

        // Completed switch change
        elements.completedSwitch.addEventListener('change', function() {
            updatePreview();
            formState.formChanged = true;
        });

        // Check duplicate button
        elements.checkDuplicateBtn.addEventListener('click', function() {
            checkDuplicate(true);
        });

        // Form submit
        elements.form.addEventListener('submit', handleFormSubmit);

        // Cancel button
        elements.cancelBtn.addEventListener('click', function(e) {
            if (formState.formChanged) {
                e.preventDefault();
                confirmCancel();
            }
        });

        // Warn before leaving
        window.addEventListener('beforeunload', function(e) {
            if (formState.formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    // ========== PREVIEW FUNCTIONALITY ==========
    function initializePreview() {
        if (elements.userSelect.value || elements.lessonSelect.value) {
            elements.previewCard.classList.add('show');
        }
    }

    function updatePreview() {
        // Show preview card if any selection is made
        if (elements.userSelect.value || elements.lessonSelect.value) {
            elements.previewCard.classList.add('show');
        } else {
            elements.previewCard.classList.remove('show');
            return;
        }

        // Update user preview
        if (elements.userSelect.value) {
            const selectedOption = elements.userSelect.options[elements.userSelect.selectedIndex];
            const userName = selectedOption.dataset.name;
            const userEmail = selectedOption.dataset.email;
            elements.previewUser.textContent = `${userName} (${userEmail})`;
        } else {
            elements.previewUser.textContent = 'Not selected';
        }

        // Update lesson preview
        if (elements.lessonSelect.value) {
            const selectedOption = elements.lessonSelect.options[elements.lessonSelect.selectedIndex];
            const lessonTitle = selectedOption.dataset.title;
            const lessonCourse = selectedOption.dataset.course;
            elements.previewLesson.textContent = `${lessonTitle} (${lessonCourse})`;
        } else {
            elements.previewLesson.textContent = 'Not selected';
        }

        // Update status preview
        elements.previewStatus.textContent = elements.completedSwitch.checked ? 'Completed' : 'In Progress';
    }

    // ========== DUPLICATE CHECK ==========
    async function checkDuplicate(showNotification = false) {
        const userId = elements.userSelect.value;
        const lessonId = elements.lessonSelect.value;

        // Hide warning if no selections
        if (!userId || !lessonId) {
            elements.duplicateWarning.classList.remove('show');
            formState.isDuplicate = false;
            return;
        }

        showLoading();

        try {
            const response = await fetch(`/lesson-progress/check-duplicate/${userId}/${lessonId}`);
            const data = await response.json();

            if (data.exists) {
                formState.isDuplicate = true;
                elements.duplicateMessage.textContent = 
                    `⚠️ Warning: This user already has progress for this lesson. Progress ID: ${data.progress_id}`;
                elements.duplicateWarning.classList.add('show');
                
                if (showNotification) {
                    showToast('Duplicate progress found!', 'warning');
                }
            } else {
                formState.isDuplicate = false;
                elements.duplicateWarning.classList.remove('show');
                
                if (showNotification) {
                    showToast('No duplicate found - you can proceed', 'success');
                }
            }
        } catch (error) {
            console.error('Error checking duplicate:', error);
            showToast('Failed to check for duplicates', 'error');
        } finally {
            hideLoading();
        }
    }

    // ========== FORM HANDLING ==========
    async function handleFormSubmit(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return;
        }

        // Check for duplicates before submit
        if (!formState.isDuplicate) {
            await checkDuplicate();
        }

        // If duplicate exists, confirm with user
        if (formState.isDuplicate) {
            const confirmDuplicate = await showConfirmDialog(
                'Duplicate Progress Warning',
                'This user already has progress for this lesson. Are you sure you want to create another record?'
            );
            
            if (!confirmDuplicate) {
                return;
            }
        }

        showLoading();

        try {
            // Submit form
            elements.form.submit();
        } catch (error) {
            console.error('Error submitting form:', error);
            showToast('Failed to create progress record', 'error');
            hideLoading();
        }
    }

    function validateForm() {
        if (!elements.userSelect.value) {
            showToast('Please select a user', 'error');
            elements.userSelect.focus();
            return false;
        }

        if (!elements.lessonSelect.value) {
            showToast('Please select a lesson', 'error');
            elements.lessonSelect.focus();
            return false;
        }

        return true;
    }

    // ========== UTILITY FUNCTIONS ==========
    function showLoading() {
        elements.loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        elements.loadingOverlay.style.display = 'none';
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'exclamation-triangle'} me-2 fa-lg"></i>
                <div>${message}</div>
                <button class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    function showConfirmDialog(title, message) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'modal fade show';
            modal.style.display = 'block';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-white">
                                <i class="fas fa-exclamation-triangle me-2"></i>${title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove(); resolve(false)">Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="this.closest('.modal').remove(); resolve(true)">Continue Anyway</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Store resolve function globally for this modal
            window.resolveConfirm = resolve;
            
            // Clean up when modal is closed
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
                resolve(false);
            });
        });
    }

    function confirmCancel() {
        if (confirm('You have unsaved changes. Are you sure you want to leave?')) {
            window.location.href = elements.cancelBtn.href;
        }
    }

    function checkForOldInputs() {
        // If there are old inputs (from validation error), update preview
        if (elements.userSelect.value || elements.lessonSelect.value) {
            updatePreview();
            checkDuplicate();
        }
    }

    // ========== LIVE SEARCH/ FILTER (Optional) ==========
    function initializeSearch() {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mt-2';
        searchInput.placeholder = 'Search users...';
        
        elements.userSelect.parentNode.insertBefore(searchInput, elements.userSelect);
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const options = elements.userSelect.options;
            
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (i === 0) continue; // Skip first option
                
                const text = option.text.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        });
    }

    // Uncomment below if you want search functionality
    // initializeSearch();
</script>
@endpush