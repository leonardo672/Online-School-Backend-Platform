@extends('layout')

@section('title', 'Edit Lesson Progress')


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

    .btn-update {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        border-radius: 8px;
        padding: 0.6rem 2rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40,167,69,0.3);
    }

    .btn-cancel {
        border-radius: 8px;
        padding: 0.6rem 2rem;
        font-weight: 500;
        margin-left: 1rem;
    }

    .btn-danger {
        border-radius: 8px;
        padding: 0.6rem 2rem;
        font-weight: 500;
    }

    .current-status {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
    }

    .current-status.completed {
        border-left-color: #28a745;
    }

    .current-status.in-progress {
        border-left-color: #ffc107;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .status-badge.completed {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }

    .status-badge.in-progress {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        color: white;
    }

    .completion-info {
        background: #e8f4f8;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        border-left: 4px solid #17a2b8;
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

    .toast-info {
        background: linear-gradient(45deg, #17a2b8, #138496);
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

    .history-timeline {
        margin-top: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .timeline-time {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .delete-modal .modal-content {
        border-radius: 10px;
        border: none;
    }

    .delete-modal .modal-header {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
        border-radius: 10px 10px 0 0;
    }

    .delete-modal .btn-delete-confirm {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: none;
        border-radius: 8px;
        padding: 0.6rem 2rem;
    }

    .delete-modal .btn-delete-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220,53,69,0.3);
    }
</style>


@section('content')
<div class="container-fluid py-4">
    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-edit text-warning me-2"></i>Edit Lesson Progress</h2>
            <p>Update progress information for <strong>{{ $progress->user->name }}</strong> - <strong>{{ $progress->lesson->title }}</strong></p>
        </div>

        {{-- Duplicate Warning --}}
        <div id="duplicateWarning" class="duplicate-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="duplicateMessage"></span>
        </div>

        <form id="progressForm" action="{{ route('lesson-progress.update', $progress->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Current Status --}}
            <div class="current-status {{ $progress->completed ? 'completed' : 'in-progress' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-2">Current Status:</h6>
                        @if($progress->completed)
                            <span class="status-badge completed">
                                <i class="fas fa-check-circle me-1"></i>Completed
                            </span>
                        @else
                            <span class="status-badge in-progress">
                                <i class="fas fa-hourglass me-1"></i>In Progress
                            </span>
                        @endif
                    </div>
                    @if($progress->completed_at)
                        <div class="text-end">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $progress->completed_at->format('M d, Y H:i') }}
                            </small>
                            <br>
                            <small class="text-muted">
                                {{ $progress->completed_at->diffForHumans() }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i>User
                    </label>
                    <select name="user_id" id="userSelect" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">Choose a user...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    @selected(old('user_id', $progress->user_id) == $user->id)>
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
                        <i class="fas fa-video me-1 text-primary"></i>Lesson
                    </label>
                    <select name="lesson_id" id="lessonSelect" class="form-select @error('lesson_id') is-invalid @enderror" required>
                        <option value="">Choose a lesson...</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" 
                                    data-title="{{ $lesson->title }}"
                                    data-course="{{ $lesson->course->title ?? 'No Course' }}"
                                    data-position="{{ $lesson->position }}"
                                    @selected(old('lesson_id', $progress->lesson_id) == $lesson->id)>
                                {{ $lesson->title }} 
                                @if($lesson->course)
                                    ({{ $lesson->course->title }})
                                @endif
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
                               id="completedSwitch" value="1" {{ old('completed', $progress->completed) ? 'checked' : '' }}>
                        <label class="form-check-label" for="completedSwitch">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Mark as completed
                        </label>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Toggle to update completion status
                    </small>
                </div>
            </div>

            {{-- Preview Card --}}
            <div id="previewCard" class="preview-card">
                <h6 class="mb-3"><i class="fas fa-eye me-2"></i>Preview Changes</h6>
                <div class="preview-item">
                    <span class="preview-label">User:</span>
                    <span class="preview-value" id="previewUser">{{ $progress->user->name }} ({{ $progress->user->email }})</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Lesson:</span>
                    <span class="preview-value" id="previewLesson">{{ $progress->lesson->title }} ({{ $progress->lesson->course->title ?? 'No Course' }})</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Status:</span>
                    <span class="preview-value" id="previewStatus">{{ $progress->completed ? 'Completed' : 'In Progress' }}</span>
                </div>
                @if($progress->completed_at)
                <div class="preview-item">
                    <span class="preview-label">Completed:</span>
                    <span class="preview-value" id="previewCompletedAt">{{ $progress->completed_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>

            {{-- Completion History --}}
            @if($progress->completed_at)
            <div class="history-timeline">
                <h6 class="mb-3"><i class="fas fa-history me-2 text-info"></i>Completion History</h6>
                <div class="timeline-item">
                    <div class="timeline-icon bg-success text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Lesson Completed</div>
                        <div class="timeline-time">
                            {{ $progress->completed_at->format('F d, Y \a\t h:i A') }}
                            ({{ $progress->completed_at->diffForHumans() }})
                        </div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon bg-primary text-white">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Progress Created</div>
                        <div class="timeline-time">
                            {{ $progress->created_at->format('F d, Y \a\t h:i A') }}
                            ({{ $progress->created_at->diffForHumans() }})
                        </div>
                    </div>
                </div>
                @if($progress->updated_at != $progress->created_at)
                <div class="timeline-item">
                    <div class="timeline-icon bg-warning text-white">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Last Updated</div>
                        <div class="timeline-time">
                            {{ $progress->updated_at->format('F d, Y \a\t h:i A') }}
                            ({{ $progress->updated_at->diffForHumans() }})
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <a href="{{ route('lesson-progress.index') }}" class="btn btn-secondary btn-cancel" id="cancelBtn">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-info me-2" id="checkDuplicateBtn">
                        <i class="fas fa-search me-1"></i>Check Duplicate
                    </button>
                    <button type="button" class="btn btn-danger me-2" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                    <button type="submit" class="btn btn-success btn-update" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Update Progress
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade delete-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this progress record?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>This action cannot be undone.</strong>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="mb-1"><strong>User:</strong> {{ $progress->user->name }}</p>
                    <p class="mb-1"><strong>Lesson:</strong> {{ $progress->lesson->title }}</p>
                    <p class="mb-0"><strong>Status:</strong> {{ $progress->completed ? 'Completed' : 'In Progress' }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form action="{{ route('lesson-progress.destroy', $progress->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-delete-confirm">
                        <i class="fas fa-trash me-1"></i>Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ========== STATE MANAGEMENT ==========
    let formState = {
        originalUserId: '{{ $progress->user_id }}',
        originalLessonId: '{{ $progress->lesson_id }}',
        originalCompleted: {{ $progress->completed ? 'true' : 'false' }},
        userSelected: true,
        lessonSelected: true,
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
        previewCompletedAt: document.getElementById('previewCompletedAt'),
        checkDuplicateBtn: document.getElementById('checkDuplicateBtn'),
        submitBtn: document.getElementById('submitBtn'),
        cancelBtn: document.getElementById('cancelBtn'),
        deleteBtn: document.getElementById('deleteBtn')
    };

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        initializePreview();
    });

    function initializeEventListeners() {
        // User select change
        elements.userSelect.addEventListener('change', function() {
            formState.userSelected = this.value !== '';
            checkFormChanged();
            updatePreview();
            if (this.value !== formState.originalUserId) {
                checkDuplicate();
            } else {
                hideDuplicateWarning();
            }
        });

        // Lesson select change
        elements.lessonSelect.addEventListener('change', function() {
            formState.lessonSelected = this.value !== '';
            checkFormChanged();
            updatePreview();
            if (this.value !== formState.originalLessonId) {
                checkDuplicate();
            } else {
                hideDuplicateWarning();
            }
        });

        // Completed switch change
        elements.completedSwitch.addEventListener('change', function() {
            checkFormChanged();
            updatePreview();
            
            // Show notification if status changed
            if (this.checked !== formState.originalCompleted) {
                showToast(`Status will change to ${this.checked ? 'Completed' : 'In Progress'}`, 'info');
            }
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

    // ========== FORM CHANGE DETECTION ==========
    function checkFormChanged() {
        const userIdChanged = elements.userSelect.value !== formState.originalUserId;
        const lessonIdChanged = elements.lessonSelect.value !== formState.originalLessonId;
        const completedChanged = elements.completedSwitch.checked !== formState.originalCompleted;
        
        formState.formChanged = userIdChanged || lessonIdChanged || completedChanged;
    }

    // ========== PREVIEW FUNCTIONALITY ==========
    function initializePreview() {
        updatePreview();
    }

    function updatePreview() {
        // Update user preview
        if (elements.userSelect.value) {
            const selectedOption = elements.userSelect.options[elements.userSelect.selectedIndex];
            const userName = selectedOption.dataset.name;
            const userEmail = selectedOption.dataset.email;
            elements.previewUser.textContent = `${userName} (${userEmail})`;
        }

        // Update lesson preview
        if (elements.lessonSelect.value) {
            const selectedOption = elements.lessonSelect.options[elements.lessonSelect.selectedIndex];
            const lessonTitle = selectedOption.dataset.title;
            const lessonCourse = selectedOption.dataset.course;
            elements.previewLesson.textContent = `${lessonTitle} (${lessonCourse})`;
        }

        // Update status preview
        elements.previewStatus.textContent = elements.completedSwitch.checked ? 'Completed' : 'In Progress';
        
        // Update completed at preview
        if (elements.previewCompletedAt) {
            if (elements.completedSwitch.checked && !formState.originalCompleted) {
                elements.previewCompletedAt.textContent = 'Will be set to now';
            } else if (!elements.completedSwitch.checked && formState.originalCompleted) {
                elements.previewCompletedAt.textContent = 'Will be cleared';
            }
        }
    }

    // ========== DUPLICATE CHECK ==========
    async function checkDuplicate(showNotification = false) {
        const userId = elements.userSelect.value;
        const lessonId = elements.lessonSelect.value;

        // Skip if same as original
        if (userId === formState.originalUserId && lessonId === formState.originalLessonId) {
            hideDuplicateWarning();
            return;
        }

        // Hide warning if no selections
        if (!userId || !lessonId) {
            hideDuplicateWarning();
            formState.isDuplicate = false;
            return;
        }

        showLoading();

        try {
            const response = await fetch(`/lesson-progress/check-duplicate/${userId}/${lessonId}?exclude={{ $progress->id }}`);
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
                hideDuplicateWarning();
                
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

    function hideDuplicateWarning() {
        formState.isDuplicate = false;
        elements.duplicateWarning.classList.remove('show');
    }

    // ========== FORM HANDLING ==========
    async function handleFormSubmit(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return;
        }

        // Check if any changes were made
        if (!formState.formChanged) {
            const confirmNoChanges = await showConfirmDialog(
                'No Changes Made',
                'You haven\'t made any changes. Are you sure you want to update?'
            );
            
            if (!confirmNoChanges) {
                return;
            }
        }

        // Check for duplicates before submit
        if (formState.userSelected && formState.lessonSelected) {
            await checkDuplicate();
        }

        // If duplicate exists, confirm with user
        if (formState.isDuplicate) {
            const confirmDuplicate = await showConfirmDialog(
                'Duplicate Progress Warning',
                'This user already has progress for this lesson. Are you sure you want to update to this duplicate?'
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
            showToast('Failed to update progress record', 'error');
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
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${icon} me-2 fa-lg"></i>
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
            // Create modal dynamically
            const modalId = 'confirmModal_' + Date.now();
            const modal = document.createElement('div');
            modal.className = 'modal fade show';
            modal.id = modalId;
            modal.style.display = 'block';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.setAttribute('tabindex', '-1');
            
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-white">
                                <i class="fas fa-question-circle me-2"></i>${title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('${modalId}').remove()"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('${modalId}').remove(); resolve(false)">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-warning" onclick="document.getElementById('${modalId}').remove(); resolve(true)">
                                <i class="fas fa-check me-1"></i>Continue
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Store resolve function globally for this modal
            window['resolve_' + modalId] = resolve;
            
            // Update button onclick to use the stored resolve
            const buttons = modal.querySelectorAll('.modal-footer button');
            buttons[0].onclick = () => {
                document.getElementById(modalId).remove();
                resolve(false);
            };
            buttons[1].onclick = () => {
                document.getElementById(modalId).remove();
                resolve(true);
            };
        });
    }

    function confirmCancel() {
        showConfirmDialog(
            'Unsaved Changes',
            'You have unsaved changes. Are you sure you want to leave?'
        ).then((confirmed) => {
            if (confirmed) {
                window.location.href = elements.cancelBtn.href;
            }
        });
    }

    // ========== LIVE SEARCH/FILTER (Optional) ==========
    function addSearchToSelect(selectElement, placeholder) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control mt-2 mb-2';
        searchInput.placeholder = placeholder;
        
        selectElement.parentNode.insertBefore(searchInput, selectElement);
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const options = selectElement.options;
            
            for (let i = 0; i < options.length; i++) {
                if (i === 0) continue; // Skip first option
                
                const text = options[i].text.toLowerCase();
                options[i].style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }

    // Uncomment to add search to selects
    // addSearchToSelect(elements.userSelect, 'Search users...');
    // addSearchToSelect(elements.lessonSelect, 'Search lessons...');
</script>
@endpush