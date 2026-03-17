@extends('layout')

@section('title', 'Progress Details')


<style>
    /* ===== Container ===== */
    .detail-container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* ===== Profile Card ===== */
    .profile-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
    }

    .profile-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .profile-header p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
    }

    .profile-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    .profile-body {
        padding: 1.5rem;
    }

    /* ===== Info Grid ===== */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        border-left: 3px solid;
    }

    .info-item.user-item { border-left-color: #007bff; }
    .info-item.lesson-item { border-left-color: #28a745; }
    .info-item.status-item { border-left-color: #ffc107; }
    .info-item.date-item { border-left-color: #17a2b8; }

    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.3rem;
    }

    .info-label i {
        margin-right: 0.3rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
    }

    .info-value a {
        color: #007bff;
        text-decoration: none;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .info-value small {
        font-size: 0.85rem;
        font-weight: normal;
        color: #6c757d;
    }

    /* ===== Status Badges ===== */
    .status-completed {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        background: #28a745;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .status-inprogress {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        background: #ffc107;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .status-completed i, .status-inprogress i {
        margin-right: 0.3rem;
    }

    /* ===== Info Cards ===== */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }

    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }

    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 1.2rem;
        height: 100%;
    }

    .info-card:last-child {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .info-card i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .info-card h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }

    .info-card p {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    /* ===== Timeline ===== */
    .timeline-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .timeline-card h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.8rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        padding: 1rem 0 1rem 1.5rem;
        border-bottom: 1px solid #f8f9fa;
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-icon {
        position: absolute;
        left: -1.8rem;
        top: 1rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1;
    }

    .timeline-icon i {
        font-size: 0.9rem;
    }

    .timeline-icon.primary { background: #007bff; color: white; }
    .timeline-icon.success { background: #28a745; color: white; }
    .timeline-icon.warning { background: #ffc107; color: white; }
    .timeline-icon.info { background: #17a2b8; color: white; }

    .timeline-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }

    .timeline-time {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.2rem;
    }

    .timeline-time i {
        margin-right: 0.2rem;
    }

    .timeline-description {
        font-size: 0.9rem;
        color: #495057;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 500;
        border-radius: 4px;
        margin-left: 0.5rem;
    }

    .badge.bg-success { background: #28a745; color: white; }
    .badge.bg-warning { background: #ffc107; color: white; }
    .badge.bg-secondary { background: #6c757d; color: white; }

    /* ===== Action Buttons ===== */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-top: 1rem;
    }

    .btn-action {
        padding: 0.5rem 1.2rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: white;
        text-decoration: none;
        display: inline-block;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        color: white;
        text-decoration: none;
    }

    .btn-primary { background: #007bff; }
    .btn-warning { background: #ffc107; color: #212529; }
    .btn-danger { background: #dc3545; }
    .btn-secondary { background: #6c757d; }

    /* ===== Modal ===== */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        overflow: auto;
    }

    .modal.show {
        display: block;
    }

    .modal-dialog {
        max-width: 500px;
        margin: 2rem auto;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: #dc3545;
        color: white;
        border-radius: 8px 8px 0 0;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-header .btn-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn {
        padding: 0.4rem 1rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .alert-warning {
        background: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 0.75rem;
        border-radius: 4px;
        font-size: 0.9rem;
        margin-top: 1rem;
    }

    .alert-warning ul {
        margin: 0.5rem 0 0 1rem;
        padding: 0;
    }

    /* ===== Loading ===== */
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
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* ===== Toast Notifications ===== */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        padding: 1rem;
        border-radius: 6px;
        color: white;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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

    .toast-success { background: #28a745; }
    .toast-error { background: #dc3545; }
    .toast-warning { background: #ffc107; color: #212529; }
    .toast-info { background: #17a2b8; }

    .toast-notification .btn-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        float: right;
    }

    /* ===== Tooltip ===== */
    [data-tooltip] {
        position: relative;
        cursor: pointer;
    }

    [data-tooltip]:before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.3rem 0.6rem;
        background: #333;
        color: white;
        font-size: 0.75rem;
        border-radius: 4px;
        white-space: nowrap;
        display: none;
        z-index: 1000;
        margin-bottom: 0.3rem;
    }

    [data-tooltip]:hover:before {
        display: block;
    }

    /* ===== Utilities ===== */
    .text-center { text-align: center; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-4 { margin-top: 1.5rem; }
    .me-1 { margin-right: 0.25rem; }
    .me-2 { margin-right: 0.5rem; }
    .me-3 { margin-right: 1rem; }
    .ms-2 { margin-left: 0.5rem; }
    .mb-0 { margin-bottom: 0; }
    .d-none { display: none; }
    .d-inline { display: inline; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .justify-content-end { justify-content: flex-end; }
    .w-100 { width: 100%; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .timeline {
            padding-left: 1.5rem;
        }

        .timeline-icon {
            width: 28px;
            height: 28px;
            left: -1.6rem;
        }

        .toast-notification {
            min-width: auto;
            width: 90%;
            left: 5%;
            right: 5%;
        }
    }
</style>


@section('content')
<div class="container-fluid py-4">
    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="detail-container">
        {{-- Main Profile Card --}}
        <div class="profile-card">
            <div class="profile-header">
                <h2><i class="fas fa-chart-line me-2"></i>Progress Details</h2>
                <p>Viewing progress record #{{ $progress->id }}</p>
                <span class="profile-badge">
                    <i class="fas fa-clock me-1"></i>
                    Created {{ $progress->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="profile-body">
                {{-- Info Grid --}}
                <div class="info-grid">
                    {{-- User Info --}}
                    <div class="info-item user-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i> User
                        </div>
                        <div class="info-value">
                            <a href="{{ route('users.show', $progress->user_id) }}">
                                {{ $progress->user->name }}
                            </a>
                            <br>
                            <small>{{ $progress->user->email }}</small>
                        </div>
                    </div>

                    {{-- Lesson Info --}}
                    <div class="info-item lesson-item">
                        <div class="info-label">
                            <i class="fas fa-video"></i> Lesson
                        </div>
                        <div class="info-value">
                            <a href="{{ route('lessons.show', $progress->lesson_id) }}">
                                {{ $progress->lesson->title }}
                            </a>
                            <br>
                            @if($progress->lesson->course)
                                <small>Course: {{ $progress->lesson->course->title }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- Status Info --}}
                    <div class="info-item status-item">
                        <div class="info-label">
                            <i class="fas fa-tag"></i> Status
                        </div>
                        <div class="info-value">
                            @if($progress->completed)
                                <span class="status-completed">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            @else
                                <span class="status-inprogress">
                                    <i class="fas fa-hourglass"></i> In Progress
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Created Date --}}
                    <div class="info-item date-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Created
                        </div>
                        <div class="info-value">
                            <i class="fas fa-calendar-day"></i>
                            {{ $progress->created_at->format('M d, Y') }}
                            <br>
                            <small>{{ $progress->created_at->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Additional Info Row --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <i class="fas fa-graduation-cap"></i>
                            <h5>Lesson Position</h5>
                            <p>Lesson #{{ $progress->lesson->position }} in the course</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <i class="fas fa-tachometer-alt"></i>
                            <h5>Completion</h5>
                            <p>
                                @if($progress->completed && $progress->completed_at)
                                    Completed {{ $progress->completed_at->diffForHumans() }}
                                @else
                                    Started {{ $progress->created_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline Card --}}
        <div class="timeline-card">
            <h4><i class="fas fa-history me-2"></i>Progress Timeline</h4>
            
            <div class="timeline">
                {{-- Creation Event --}}
                <div class="timeline-item">
                    <div class="timeline-icon primary">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Progress Created</div>
                        <div class="timeline-time">
                            <i class="fas fa-clock"></i>
                            {{ $progress->created_at->format('M d, Y h:i A') }}
                            <span class="badge bg-secondary">{{ $progress->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="timeline-description">
                            User started tracking this lesson
                        </div>
                    </div>
                </div>

                {{-- Completion Event --}}
                @if($progress->completed && $progress->completed_at)
                <div class="timeline-item">
                    <div class="timeline-icon success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Lesson Completed</div>
                        <div class="timeline-time">
                            <i class="fas fa-clock"></i>
                            {{ $progress->completed_at->format('M d, Y h:i A') }}
                            <span class="badge bg-success">{{ $progress->completed_at->diffForHumans() }}</span>
                        </div>
                        <div class="timeline-description">
                            <i class="fas fa-trophy text-warning me-1"></i>
                            User successfully completed this lesson
                        </div>
                    </div>
                </div>
                @endif

                {{-- Current Status Event --}}
                <div class="timeline-item">
                    <div class="timeline-icon info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Current Status</div>
                        <div class="timeline-time">
                            <i class="fas fa-clock"></i>
                            {{ now()->format('M d, Y h:i A') }}
                        </div>
                        <div class="timeline-description">
                            @if($progress->completed)
                                <span class="badge bg-success">Completed</span> Lesson is complete
                            @else
                                <span class="badge bg-warning">In Progress</span> Lesson in progress
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <button class="btn-action btn-secondary" onclick="window.history.back()" data-tooltip="Go back">
                <i class="fas fa-arrow-left me-1"></i>Back
            </button>
            
            <a href="{{ route('lesson-progress.index') }}" class="btn-action btn-secondary" data-tooltip="View all progress">
                <i class="fas fa-list me-1"></i>All Progress
            </a>
            
            <a href="{{ route('lesson-progress.edit', $progress->id) }}" class="btn-action btn-warning" data-tooltip="Edit this record">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            
            <button class="btn-action btn-danger" onclick="showDeleteModal()" data-tooltip="Delete this record">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button class="btn-close" onclick="hideDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h5>Are you sure?</h5>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                
                <div class="alert-warning">
                    <strong>You are about to delete:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>User:</strong> {{ $progress->user->name }}</li>
                        <li><strong>Lesson:</strong> {{ $progress->lesson->title }}</li>
                        <li><strong>Status:</strong> {{ $progress->completed ? 'Completed' : 'In Progress' }}</li>
                        @if($progress->completed_at)
                        <li><strong>Completed:</strong> {{ $progress->completed_at->format('M d, Y') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideDeleteModal()">Cancel</button>
                <form id="deleteForm" action="{{ route('lesson-progress.destroy', $progress->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // DOM Elements
    const elements = {
        loadingOverlay: document.getElementById('loadingOverlay'),
        deleteModal: document.getElementById('deleteModal'),
        deleteForm: document.getElementById('deleteForm')
    };

    // Show Delete Modal
    window.showDeleteModal = function() {
        elements.deleteModal.classList.add('show');
    }

    // Hide Delete Modal
    window.hideDeleteModal = function() {
        elements.deleteModal.classList.remove('show');
    }

    // Show Loading
    function showLoading() {
        elements.loadingOverlay.style.display = 'flex';
    }

    // Hide Loading
    function hideLoading() {
        elements.loadingOverlay.style.display = 'none';
    }

    // Show Toast Notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${icon} me-2"></i>
                <span style="flex: 1;">${message}</span>
                <button class="btn-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Handle Delete Form Submit
    if (elements.deleteForm) {
        elements.deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            hideDeleteModal();
            showLoading();
            showToast('Deleting progress record...', 'info');
            
            setTimeout(() => {
                this.submit();
            }, 500);
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === elements.deleteModal) {
            hideDeleteModal();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape to close modal
        if (e.key === 'Escape' && elements.deleteModal.classList.contains('show')) {
            hideDeleteModal();
        }
        
        // Ctrl+E for edit
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            window.location.href = '{{ route("lesson-progress.edit", $progress->id) }}';
        }
        
        // Ctrl+B for back
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            window.history.back();
        }
    });

    // Copy link functionality
    function copyLink() {
        const url = window.location.href;
        
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Failed to copy link', 'error');
        });
    }

    // Add copy link button (optional)
    setTimeout(() => {
        const actionButtons = document.querySelector('.action-buttons');
        if (actionButtons) {
            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn-action btn-secondary';
            copyBtn.innerHTML = '<i class="fas fa-link me-1"></i>Copy Link';
            copyBtn.setAttribute('data-tooltip', 'Copy page link');
            copyBtn.onclick = copyLink;
            actionButtons.appendChild(copyBtn);
        }
    }, 100);
</script>
@endpush