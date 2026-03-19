@extends('layout')

@section('content')
<!-- Main Card with Updated Styling -->
<div class="card shadow-lg rounded">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2><i class="fas fa-folder me-2"></i> Categories Management</h2>
            <div>
                <!-- Search Form -->
                <form action="{{ url('/categories') }}" method="GET" class="d-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search categories..." 
                               value="{{ request('search') }}" style="max-width: 250px;">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Button to Add New Category -->
        <a href="{{ url('/categories/create') }}" class="btn btn-add-category mb-4" title="Add New Category">
            <i class="fas fa-plus-circle me-2" aria-hidden="true"></i> Add New Category
        </a>
        
        <!-- Category Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-primary text-white p-3 rounded">
                    <h6 class="mb-1">Total Categories</h6>
                    <h3 class="mb-0">
                        @if(method_exists($categories, 'total'))
                            {{ $categories->total() }}
                        @else
                            {{ $categories->count() }}
                        @endif
                    </h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-success text-white p-3 rounded">
                    <h6 class="mb-1">Categories with Courses</h6>
                    <h3 class="mb-0">{{ $categoriesWithCourses ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-info text-white p-3 rounded">
                    <h6 class="mb-1">Empty Categories</h6>
                    <h3 class="mb-0">{{ $emptyCategories ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-warning text-white p-3 rounded">
                    <h6 class="mb-1">Latest Added</h6>
                    <h5 class="mb-0">
                        @if($latestCategory)
                            {{ $latestCategory->created_at->diffForHumans() }}
                        @else
                            N/A
                        @endif
                    </h5>
                </div>
            </div>
        </div>
        
        <!-- Categories Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Icon & Color</th>
                        <th>Courses</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $item)
                        <tr>
                            <td>
                                @if(method_exists($categories, 'currentPage'))
                                    {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon me-2">
                                        @if($item->icon)
                                            @php
                                                $iconColor = $item->color ?? '#2ecc71';
                                            @endphp
                                            <i class="{{ $item->icon }}" style="color: {{ $iconColor }};"></i>
                                        @else
                                            <i class="fas fa-folder text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $item->name }}</strong>
                                        <br>
                                        <small class="text-muted"><code>{{ $item->slug }}</code></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($item->icon)
                                            @php
                                                $iconColor = $item->color ?? '#2ecc71';
                                            @endphp
                                            <i class="{{ $item->icon }} fa-lg" style="color: {{ $iconColor }};"></i>
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->color)
                                            <div class="color-preview" style="background-color: {{ $item->color }};" title="{{ $item->color }}"></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $courseCount = $item->courses_count ?? 0;
                                @endphp
                                @if($courseCount > 0)
                                    <a href="{{ url('/courses?category=' . $item->slug) }}" class="badge bg-success text-decoration-none">
                                        {{ $courseCount }} course{{ $courseCount !== 1 ? 's' : '' }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">No courses</span>
                                @endif
                            </td>
                            <td>
                                @if($item->description)
                                    <small class="text-muted">{{ Str::limit($item->description, 40) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted" title="{{ $item->created_at->format('F j, Y') }}">
                                    {{ $item->created_at->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- View Button -->
                                    <a href="{{ url('/categories/' . $item->slug) }}" title="View Category" class="btn btn-view btn-sm">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ url('/categories/' . $item->slug . '/edit') }}" title="Edit Category" class="btn btn-edit btn-sm">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ url('/categories/' . $item->slug) }}" accept-charset="UTF-8" style="display:inline">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-delete btn-sm" title="Delete Category">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No categories found</h4>
                                <p class="text-muted mb-3">Categories help organize your courses into logical groups</p>
                                <a href="{{ url('/categories/create') }}" class="btn btn-add-category">
                                    <i class="fas fa-plus-circle me-2" aria-hidden="true"></i> Create Your First Category
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(method_exists($categories, 'hasPages') && $categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/pages/categories.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/categories.js'])
@endpush