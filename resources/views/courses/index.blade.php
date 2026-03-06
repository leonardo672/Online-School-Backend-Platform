@extends('layout')
@section('content')

<!-- Main Card with Updated Styling -->
<div class="card shadow-lg rounded card-bg">
    <div class="card-header header-bg">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2><i class="fas fa-graduation-cap"></i> Courses Management</h2>
            <div class="d-flex gap-2">
                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="dropdown-menu p-3" style="min-width: 250px;">
                        <form action="{{ route('courses.index') }}" method="GET">
                            <div class="mb-2">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select">
                                    <option value="">All Levels</option>
                                    @foreach($filterOptions['levels'] ?? [] as $level)
                                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($filterOptions['categories'] ?? [] as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Per Page</label>
                                <select name="per_page" class="form-select">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary w-100 mt-2">Clear Filters</a>
                        </form>
                    </div>
                </div>

                <!-- Search Form -->
                <form action="{{ route('courses.index') }}" method="GET" class="d-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search courses..." 
                               value="{{ request('search') }}" style="min-width: 250px;">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Button to Add New Course -->
        <a href="{{ route('courses.create') }}" class="btn btn-add-course mb-4" title="Add New Course">
            <i class="fas fa-plus-circle" aria-hidden="true" style="margin-right: 8px;"></i> Add New Course
        </a>
        
        <!-- Course Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-primary text-white p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Total Courses</h6>
                            <h3 class="mb-0">{{ $total }}</h3>
                        </div>
                        <i class="fas fa-book fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-success text-white p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Published</h6>
                            <h3 class="mb-0">{{ $published }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-info text-white p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Draft</h6>
                            <h3 class="mb-0">{{ $draft }}</h3>
                        </div>
                        <i class="fas fa-pencil-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-warning text-white p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Free Courses</h6>
                            <h3 class="mb-0">{{ $free }}</h3>
                        </div>
                        <i class="fas fa-gift fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Statistics (if available) -->
        @if(isset($by_level) || isset($by_category))
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Courses by Level</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($by_level ?? [] as $level => $count)
                            <div class="col-4 text-center mb-2">
                                <div class="small">{{ ucfirst($level) }}</div>
                                <div class="h5">{{ $count }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Courses by Category</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($by_category ?? [] as $category => $count)
                            <div class="col-6 text-center mb-2">
                                <div class="small">{{ Str::limit($category, 15) }}</div>
                                <div class="h5">{{ $count }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Active Filters Display -->
        @if(request('search') || request('category') || request('level'))
        <div class="alert alert-info mb-4">
            <i class="fas fa-filter"></i> Active Filters:
            @if(request('search'))
                <span class="badge bg-primary me-2">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('category'))
                <span class="badge bg-primary me-2">Category: {{ request('category') }}</span>
            @endif
            @if(request('level'))
                <span class="badge bg-primary me-2">Level: {{ ucfirst(request('level')) }}</span>
            @endif
            <a href="{{ route('courses.index') }}" class="float-end text-decoration-none">Clear All</a>
        </div>
        @endif

        <!-- Courses Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th>Category</th>
                        <th>Instructor</th>
                        <th>Price</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Enrollments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $item)
                        <tr>
                            <td>{{ ($courses->currentPage() - 1) * $courses->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="course-icon me-2">
                                        <i class="fas fa-book text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $item->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->category)
                                    <span class="badge bg-secondary">{{ $item->category->name }}</span>
                                @else
                                    <span class="badge bg-danger">No Category</span>
                                @endif
                            </td>
                            <td>
                                @if($item->instructor)
                                    <span class="text-primary">{{ $item->instructor->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $item->instructor->email }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->price == 0)
                                    <span class="badge bg-success">Free</span>
                                @else
                                    <strong>${{ number_format($item->price, 2) }}</strong>
                                @endif
                            </td>
                            <td>
                                @if($item->level == 'beginner')
                                    <span class="badge bg-info">Beginner</span>
                                @elseif($item->level == 'intermediate')
                                    <span class="badge bg-warning">Intermediate</span>
                                @elseif($item->level == 'advanced')
                                    <span class="badge bg-danger">Advanced</span>
                                @endif
                            </td>
                            <td>
                                @if($item->published)
                                    <span class="badge badge-published">Published</span>
                                @else
                                    <span class="badge badge-draft">Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $item->enrollments_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- View Button -->
                                    <a href="{{ route('courses.show', $item->id) }}" title="View Course" class="btn btn-view btn-sm">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('courses.edit', $item->id) }}" title="Edit Course" class="btn btn-edit btn-sm">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-delete btn-sm" title="Delete Course" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal for each course -->
                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the course <strong>"{{ $item->title }}"</strong>?</p>
                                                <p class="text-danger"><small>This action cannot be undone. All associated data will also be deleted.</small></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('courses.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> Delete Course
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No courses found</h4>
                                @if(request('search') || request('category') || request('level'))
                                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                @else
                                    <p class="text-muted">Get started by creating your first course!</p>
                                    <a href="{{ route('courses.create') }}" class="btn btn-add-course">
                                        <i class="fas fa-plus-circle"></i> Create Your First Course
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($courses->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} courses
                </div>
                <div>
                    {{ $courses->withQueryString()->links() }}
                </div>
            </div>
        @endif
        
        <!-- Export Options -->
        <div class="mt-4 text-end">
            <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('courses.index') }}?{{ http_build_query(request()->all()) }}&export=csv" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
                <a href="{{ route('courses.index') }}?{{ http_build_query(request()->all()) }}&export=pdf" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styling -->
<style>
    /* General Styles */
    .card {
        border: none;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .card-header {
        background: linear-gradient(145deg, #2c3e50, #3498db);
        color: white;
        padding: 20px 30px;
        border-bottom: none;
    }
    
    .card-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 1.8rem;
    }
    
    .card-body {
        padding: 30px;
    }

    /* Button Styles */
    .btn {
        font-size: 14px;
        padding: 8px 16px;
        margin: 2px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        outline: none;
        transition: all 0.3s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    /* Add New Course Button */
    .btn-add-course {
        background: linear-gradient(145deg, #3498db, #2980b9);
        color: white;
        font-weight: 600;
        padding: 12px 25px;
    }

    .btn-add-course:hover {
        background: linear-gradient(145deg, #2980b9, #1c5a7a);
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
        color: white;
    }

    /* Action Buttons */
    .btn-view {
        background-color: #007BFF;
        color: white;
    }

    .btn-view:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-edit {
        background: linear-gradient(145deg, #fd7e14, #e8650f);
        color: white;
    }

    .btn-edit:hover {
        background: linear-gradient(145deg, #e8650f, #c94f0c);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(253, 126, 20, 0.3);
    }

    .btn-delete {
        background: linear-gradient(145deg, #dc3545, #c82333);
        color: white;
    }

    .btn-delete:hover {
        background: linear-gradient(145deg, #c82333, #a71d2a);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Table Styling */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: #e3f2fd;
        transform: scale(1.01);
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .table th {
        background: linear-gradient(145deg, #34495e, #2c3e50);
        color: white;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
        border: none;
    }

    .table td {
        padding: 15px;
        text-align: left;
        font-size: 14px;
        color: #444;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    /* Statistics Cards */
    .stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .opacity-50 {
        opacity: 0.5;
    }

    /* Badge Styling */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 12px;
    }

    .badge-published {
        background: linear-gradient(145deg, #2ecc71, #27ae60);
        color: white;
    }

    .badge-draft {
        background: linear-gradient(145deg, #f39c12, #d35400);
        color: white;
    }

    /* Pagination */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: #3498db;
        border: 1px solid #dee2e6;
        margin: 0 3px;
        border-radius: 6px;
        padding: 8px 12px;
    }

    .page-item.active .page-link {
        background: linear-gradient(145deg, #3498db, #2980b9);
        border-color: #3498db;
        color: white;
    }

    .page-link:hover {
        color: #1c5a7a;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    /* Filter Dropdown */
    .dropdown-menu {
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            gap: 10px;
        }
        
        .card-header h2 {
            font-size: 1.5rem;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        
        .table td, .table th {
            font-size: 12px;
            padding: 10px;
        }
    }

    /* Print Styles */
    @media print {
        .btn, .btn-group, .dropdown, .modal, .card-header .d-flex form {
            display: none !important;
        }
        
        .table {
            border: 1px solid #ddd;
        }
        
        .table th {
            background: #f0f0f0 !important;
            color: black !important;
        }
    }
</style>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add tooltips to action buttons
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            tooltipTriggerEl.setAttribute('data-bs-toggle', 'tooltip');
        });

        // Initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
        }

        // Export functionality (if needed)
        document.querySelectorAll('[href*="export=csv"], [href*="export=pdf"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const exportUrl = this.href;
                window.location.href = exportUrl;
                // Show loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
                loadingDiv.style.backgroundColor = 'rgba(0,0,0,0.5)';
                loadingDiv.style.zIndex = '9999';
                loadingDiv.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
                document.body.appendChild(loadingDiv);
                
                // Remove loading after 2 seconds (adjust based on actual export time)
                setTimeout(() => loadingDiv.remove(), 2000);
            });
        });
    });
</script>

@endsection