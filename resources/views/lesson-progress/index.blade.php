{{-- resources/views/lesson-progress/index.blade.php --}}
@extends('layout')

@section('title', 'Lesson Progress')

@section('styles')
<style>
    /* ========== STATISTICS CARDS ========== */
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stat-card .card-body {
        padding: 1.5rem;
    }
    
    .stat-card .card-title {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .stat-card h2 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .stat-card small {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    /* ========== FILTER BAR ========== */
    .filter-bar {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    
    .filter-bar .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.3rem;
    }
    
    .filter-bar .form-control,
    .filter-bar .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
    }
    
    .filter-bar .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
    
    .filter-bar .btn-primary {
        background: linear-gradient(45deg, #007bff, #0069d9);
        border: none;
    }
    
    .filter-bar .btn-primary:hover {
        background: linear-gradient(45deg, #0069d9, #0056b3);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.2);
    }

    /* ========== TABLE STYLES ========== */
    .table-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #dee2e6;
    }
    
    .table tbody tr:hover {
        background-color: rgba(0,123,255,0.02);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ========== USER INFO ========== */
    .user-info {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #00bcd4);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .user-details {
        min-width: 0;
    }
    
    .user-details strong {
        color: #212529;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .user-details strong:hover {
        color: #007bff;
    }
    
    .user-details small {
        display: block;
        color: #6c757d;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ========== BADGES ========== */
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        font-size: 0.8rem;
        border-radius: 20px;
    }
    
    .badge.bg-success {
        background: linear-gradient(45deg, #28a745, #20c997) !important;
    }
    
    .badge.bg-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
        color: white;
    }

    /* ========== BUTTONS ========== */
    .btn-group .btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        margin: 0 2px;
        transition: all 0.2s;
    }
    
    .btn-group .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    
    .btn-group .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23,162,184,0.3);
    }
    
    .btn-group .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-group .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255,193,7,0.3);
    }
    
    .btn-group .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .btn-group .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40,167,69,0.3);
    }
    
    .btn-group .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .btn-group .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220,53,69,0.3);
    }

    /* ========== BULK ACTIONS ========== */
    .bulk-actions {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes slideUp {
        from {
            transform: translate(-50%, 100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
    
    .bulk-actions .alert {
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-bottom: 0;
    }
    
    .bulk-actions .btn-sm {
        border-radius: 30px;
        padding: 0.4rem 1.2rem;
        margin-left: 0.5rem;
        font-weight: 500;
    }

    /* ========== EMPTY STATE ========== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
    
    .empty-state h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    .empty-state .btn {
        border-radius: 30px;
        padding: 0.6rem 2rem;
        font-weight: 500;
    }

    /* ========== PAGINATION ========== */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        border: none;
        padding: 0.5rem 1rem;
        color: #007bff;
        border-radius: 8px;
        margin: 0 2px;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #007bff, #0069d9);
        color: white;
    }

    /* ========== LOADING OVERLAY ========== */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
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

    /* ========== CHECKBOX ========== */
    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #007bff;
    }

    /* ========== TOOLTIP ========== */
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
        padding: 0.4rem 0.8rem;
        background: #212529;
        color: white;
        font-size: 0.8rem;
        border-radius: 4px;
        white-space: nowrap;
        display: none;
        z-index: 1000;
    }
    
    [data-tooltip]:hover:before {
        display: block;
    }

    /* ========== ANIMATIONS ========== */
    @keyframes highlight {
        0% { background-color: rgba(40,167,69,0.2); }
        100% { background-color: transparent; }
    }
    
    .highlight-new {
        animation: highlight 2s ease-out;
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
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* ========== NOTIFICATION ========== */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease;
        margin-bottom: 10px;
    }
    
    .notification-toast:hover {
        transform: scale(1.02);
        transition: transform 0.2s;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .filter-bar .row > div {
            margin-bottom: 1rem;
        }
        
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .btn-group .btn {
            flex: 1;
            margin: 0;
        }
        
        .bulk-actions {
            width: 90%;
        }
        
        .bulk-actions .alert {
            border-radius: 10px;
            text-align: center;
        }
        
        .bulk-actions .btn-sm {
            display: block;
            width: 100%;
            margin: 0.5rem 0 0 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lesson Progress</h1>
        <div>
            <a href="{{ route('lesson-progress.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Progress
            </a>
            <button class="btn btn-success" onclick="exportProgress()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Progress</h6>
                    <h2 class="mb-0">{{ $lessonProgressesCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Completed</h6>
                    <h2 class="mb-0">{{ $completedCount }}</h2>
                    <small>{{ $completionPercentage }}% of total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">In Progress</h6>
                    <h2 class="mb-0">{{ $incompleteCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Latest Completion</h6>
                    <h5 class="mb-0">
                        @if($latestCompletion)
                            {{ $latestCompletion->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lesson-progress.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lesson</label>
                    <select name="lesson_id" class="form-select">
                        <option value="">All Lessons</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" @selected(request('lesson_id') == $lesson->id)>
                                {{ $lesson->title }} ({{ $lesson->course->title ?? 'No Course' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="completed" @selected(request('status') == 'completed')>Completed</option>
                        <option value="incomplete" @selected(request('status') == 'incomplete')>In Progress</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="User name, email, lesson..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('lesson-progress.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Progress Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>User</th>
                            <th>Lesson</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Completed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lessonProgresses as $progress)
                            <tr>
                                <td>
                                    <input type="checkbox" class="select-item" value="{{ $progress->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('users.show', $progress->user_id) }}" class="text-decoration-none">
                                        <strong>{{ $progress->user->name }}</strong>
                                        <br>
                                        <small>{{ $progress->user->email }}</small>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('lessons.show', $progress->lesson_id) }}" class="text-decoration-none">
                                        {{ $progress->lesson->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($progress->lesson->course)
                                        <a href="{{ route('courses.show', $progress->lesson->course_id) }}" class="text-decoration-none">
                                            {{ $progress->lesson->course->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">No Course</span>
                                    @endif
                                </td>
                                <td>
                                    @if($progress->completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                </td>
                                <td>
                                    @if($progress->completed_at)
                                        {{ $progress->completed_at->format('M d, Y H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $progress->completed_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('lesson-progress.show', $progress->id) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lesson-progress.edit', $progress->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success toggle-complete" 
                                                data-id="{{ $progress->id }}"
                                                data-completed="{{ $progress->completed }}"
                                                title="Toggle Status">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-progress" 
                                                data-id="{{ $progress->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                    <h5>No Progress Records Found</h5>
                                    <p class="text-muted">Start by adding a new lesson progress.</p>
                                    <a href="{{ route('lesson-progress.create') }}" class="btn btn-primary">
                                        Add Progress
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $lessonProgresses->firstItem() ?? 0 }} to {{ $lessonProgresses->lastItem() ?? 0 }} 
                    of {{ $lessonProgresses->total() }} records
                </div>
                <div>
                    {{ $lessonProgresses->links() }}
                </div>
            </div>

            {{-- Bulk Actions --}}
            <div class="mt-3" id="bulkActions" style="display: none;">
                <div class="alert alert-info">
                    <strong><span id="selectedCount">0</span> records selected</strong>
                    <div class="mt-2">
                        <button class="btn btn-success btn-sm" onclick="bulkComplete()">
                            <i class="fas fa-check-circle"></i> Mark Complete
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ========== STATE MANAGEMENT ==========
    let selectedIds = new Set();
    let isLoading = false;

    // Show/hide loading overlay
    function setLoading(loading) {
        isLoading = loading;
        document.getElementById('loadingOverlay').style.display = loading ? 'flex' : 'none';
    }

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', function() {
        initializeCheckboxes();
        initializeToggleButtons();
        initializeDeleteButtons();
        initializeFormSubmit();
        highlightNewRows();
    });

    // ========== CHECKBOX FUNCTIONS ==========
    function initializeCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.select-item');

        if (selectAll) {
            selectAll.addEventListener('change', function(e) {
                const checked = e.target.checked;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                    if (checked) {
                        selectedIds.add(checkbox.value);
                    } else {
                        selectedIds.delete(checkbox.value);
                    }
                });
                updateBulkActions();
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                if (e.target.checked) {
                    selectedIds.add(e.target.value);
                } else {
                    selectedIds.delete(e.target.value);
                }
                
                if (selectAll) {
                    selectAll.checked = checkboxes.length === document.querySelectorAll('.select-item:checked').length;
                }
                
                updateBulkActions();
            });
        });
    }

    function updateBulkActions() {
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const count = selectedIds.size;
        
        if (count > 0) {
            selectedCount.textContent = count;
            bulkActions.style.display = 'block';
            bulkActions.style.animation = 'none';
            bulkActions.offsetHeight;
            bulkActions.style.animation = 'slideUp 0.3s ease-out';
        } else {
            bulkActions.style.display = 'none';
        }
    }

    function clearSelection() {
        selectedIds.clear();
        document.querySelectorAll('.select-item').forEach(checkbox => {
            checkbox.checked = false;
        });
        if (document.getElementById('selectAll')) {
            document.getElementById('selectAll').checked = false;
        }
        updateBulkActions();
    }

    // ========== TOGGLE COMPLETION ==========
    function initializeToggleButtons() {
        document.querySelectorAll('.toggle-complete').forEach(button => {
            button.addEventListener('click', async function() {
                const id = this.dataset.id;
                const wasCompleted = this.dataset.completed === '1';
                const newStatus = !wasCompleted;
                
                if (!confirm(`Mark this lesson as ${newStatus ? 'complete' : 'incomplete'}?`)) {
                    return;
                }

                setLoading(true);

                try {
                    const response = await fetch(`/lesson-progress/${id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ completed: newStatus })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        showNotification('Status updated successfully!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Failed to update status. Please try again.', 'error');
                } finally {
                    setLoading(false);
                }
            });
        });
    }

    // ========== DELETE SINGLE ==========
    function initializeDeleteButtons() {
        document.querySelectorAll('.delete-progress').forEach(button => {
            button.addEventListener('click', async function() {
                const id = this.dataset.id;
                
                if (!confirm('Are you sure you want to delete this progress record? This action cannot be undone.')) {
                    return;
                }

                setLoading(true);

                try {
                    const response = await fetch(`/lesson-progress/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showNotification('Progress record deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        throw new Error('Failed to delete');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Failed to delete record. Please try again.', 'error');
                } finally {
                    setLoading(false);
                }
            });
        });
    }

    // ========== BULK ACTIONS ==========
    async function bulkComplete() {
        const ids = Array.from(selectedIds);
        
        if (ids.length === 0) return;
        
        if (!confirm(`Mark ${ids.length} selected records as complete?`)) {
            return;
        }

        setLoading(true);

        try {
            const response = await fetch('{{ route("lesson-progress.bulk-complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: ids })
            });

            const data = await response.json();
            
            if (data.success) {
                showNotification(`${data.updated || ids.length} records marked as complete!`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to complete records');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to complete records. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    }

    async function bulkDelete() {
        const ids = Array.from(selectedIds);
        
        if (ids.length === 0) return;
        
        if (!confirm(`Are you sure you want to delete ${ids.length} records? This action cannot be undone.`)) {
            return;
        }

        setLoading(true);

        try {
            const response = await fetch('{{ route("lesson-progress.bulk-delete") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: ids })
            });

            const data = await response.json();
            
            if (data.success) {
                showNotification(`${data.deleted || ids.length} records deleted successfully!`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to delete records');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to delete records. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    }

    // ========== EXPORT ==========
    async function exportProgress() {
        const params = new URLSearchParams(window.location.search);
        const exportUrl = `{{ route("lesson-progress.export") }}?${params.toString()}`;
        
        setLoading(true);
        
        try {
            window.location.href = exportUrl;
            showNotification('Export started. Your download will begin shortly.', 'info');
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to start export. Please try again.', 'error');
        } finally {
            setTimeout(() => setLoading(false), 2000);
        }
    }

    // ========== FORM SUBMIT ==========
    function initializeFormSubmit() {
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                setLoading(true);
            });
        }
    }

    // ========== HIGHLIGHT NEW ROWS ==========
    function highlightNewRows() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('new') === 'true') {
            const firstRow = document.querySelector('tbody tr:first-child');
            if (firstRow) {
                firstRow.classList.add('highlight-new');
            }
        }
    }

    // ========== NOTIFICATION SYSTEM ==========
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification-toast alert alert-${type}`;
        
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'exclamation-circle' : 'info-circle';
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${icon} me-2 fa-lg"></i>
                <div>${message}</div>
                <button class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
</script>
@endpush