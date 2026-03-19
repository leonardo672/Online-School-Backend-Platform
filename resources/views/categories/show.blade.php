@extends('layout')

@section('content')
@php
    // Safely get course data
    $coursesCount = $category->courses()->count() ?? 0;
    $publishedCoursesCount = $category->courses()->where('published', true)->count() ?? 0;
    $draftCoursesCount = $category->courses()->where('published', false)->count() ?? 0;
    $categoryCourses = $category->courses()->with('instructor')->get() ?? collect([]);

    // Prepare array for JS (plain array, no closures)
    $coursesForJs = [];
    foreach ($categoryCourses as $course) {
        $coursesForJs[] = [
            'title' => $course->title,
            'description' => $course->description,
            'instructor' => $course->instructor->name ?? 'N/A',
            'price' => $course->price,
            'level' => $course->level,
            'published' => $course->published ? 'Published' : 'Draft',
        ];
    }

    $categoryDataForJs = [
        'name' => $category->name,
        'slug' => $category->slug,
        'icon' => $category->icon,
        'color' => $category->color,
        'description' => $category->description,
        'coursesCount' => $coursesCount,
        'publishedCount' => $publishedCoursesCount,
        'draftCount' => $draftCoursesCount,
        'courses' => $coursesForJs,
        'createdDate' => $category->created_at->format('Y-m-d'),
        'updatedDate' => $category->updated_at->format('Y-m-d'),
    ];
@endphp

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-folder me-2"></i> Category Details: {{ $category->name }}
            </h4>
            <a href="{{ route('categories.edit', $category->slug) }}" class="btn btn-edit">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="card-body">
        <!-- Category Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-success text-white rounded">
                        <div>
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $coursesCount }}</h3>
                            <small>Total Courses</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white rounded">
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $publishedCoursesCount }}</h3>
                            <small>Published</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-warning text-white rounded">
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $draftCoursesCount }}</h3>
                            <small>Draft</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card mb-3">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-info text-white rounded">
                        <div>
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0">{{ $category->created_at->format('M d, Y') }}</h5>
                            <small>Created</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Details -->
        <div class="row">
            <div class="col-md-6">
                <div class="category-details p-3 bg-light rounded mb-3">
                    <h5 class="text-success mb-3">
                        <i class="fas fa-info-circle me-2"></i> Basic Information
                    </h5>
                    <p><strong>Name:</strong> {{ $category->name }}</p>
                    <p><strong>Slug:</strong> <code>{{ $category->slug }}</code></p>
                    <p><strong>Icon:</strong> 
                        @if($category->icon)
                            <i class="{{ $category->icon }}" style="color: {{ $category->color ?? '#2ecc71' }}"></i>
                        @else
                            <span class="text-muted">No icon</span>
                        @endif
                    </p>
                    <p><strong>Color:</strong> 
                        @if($category->color)
                            <span class="color-preview" style="background-color: {{ $category->color }}; width: 20px; height: 20px; display: inline-block; border-radius: 3px;"></span>
                            {{ $category->color }}
                        @else
                            <span class="text-muted">Default</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="category-details p-3 bg-light rounded mb-3">
                    <h5 class="text-success mb-3">
                        <i class="fas fa-align-left me-2"></i> Description
                    </h5>
                    <div class="p-2">
                        @if($category->description)
                            {!! nl2br(e($category->description)) !!}
                        @else
                            <span class="text-muted fst-italic">No description provided</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses List -->
        @if($coursesCount > 0)
            <div class="courses-section mt-4">
                <h5 class="text-success mb-3">
                    <i class="fas fa-graduation-cap me-2"></i> Courses in this Category ({{ $coursesCount }})
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Instructor</th>
                                <th>Price</th>
                                <th>Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryCourses as $course)
                                <tr>
                                    <td>
                                        <strong>{{ $course->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($course->description, 40) }}</small>
                                    </td>
                                    <td>{{ $course->instructor->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($course->price == 0)
                                            <span class="badge bg-success">Free</span>
                                        @else
                                            ${{ number_format($course->price, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->level == 'beginner')
                                            <span class="badge bg-info">Beginner</span>
                                        @elseif($course->level == 'intermediate')
                                            <span class="badge bg-warning">Intermediate</span>
                                        @elseif($course->level == 'advanced')
                                            <span class="badge bg-danger">Advanced</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $course->level }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="empty-state text-center py-5 bg-light rounded mt-4">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Courses in This Category</h5>
                <p class="text-muted mb-4">This category doesn't have any courses yet.</p>
                <a href="{{ route('courses.create', ['category_id' => $category->id]) }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i> Create First Course
                </a>
            </div>
        @endif

        <!-- Additional Information -->
        <div class="additional-info p-3 bg-light rounded mt-4">
            <h5 class="text-success mb-3">
                <i class="fas fa-clock me-2"></i> Timeline
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <strong>Created:</strong> {{ $category->created_at->format('F d, Y h:i A') }}
                </div>
                <div class="col-md-6">
                    <strong>Last Updated:</strong> {{ $category->updated_at->format('F d, Y h:i A') }}
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-4 text-center">
            <button id="printCategoryBtn" class="btn btn-print">
                <i class="fas fa-print me-2"></i> Print Category Details
            </button>
        </div>
    </div>
</div>

<!-- Print Function Script -->
<script>
    // Pass PHP data to JavaScript
    const categoryPrintData = @json($categoryDataForJs);

    document.getElementById('printCategoryBtn').addEventListener('click', function() {
        // Create print window
        let printWindow = window.open('', '_blank', 'height=600,width=800');
        
        // Build HTML content
        printWindow.document.write('<html><head><title>Category Details - ' + categoryPrintData.name + '</title>');
        printWindow.document.write('<style>');
        printWindow.document.write(`
            body {
                font-family: Arial, sans-serif;
                padding: 30px;
                color: #333;
                max-width: 1200px;
                margin: 0 auto;
            }
            .print-header {
                text-align: center;
                border-bottom: 2px solid #2ecc71;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .print-header h1 {
                color: #27ae60;
                margin: 0 0 10px 0;
            }
            .print-header p {
                color: #666;
                margin: 5px 0;
            }
            .print-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .print-section h2 {
                color: #2ecc71;
                margin-bottom: 15px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
            .stats-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }
            .stat-box {
                flex: 1;
                padding: 15px;
                border-radius: 8px;
                color: white;
                text-align: center;
                margin-right: 10px;
            }
            .stat-box:last-child { margin-right: 0; }
            .stat-box.total { background: linear-gradient(135deg, #2ecc71, #27ae60); }
            .stat-box.published { background: linear-gradient(135deg, #3498db, #2980b9); }
            .stat-box.draft { background: linear-gradient(135deg, #f39c12, #e67e22); }
            .info-box {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 10px;
            }
            .color-preview {
                width: 20px;
                height: 20px;
                display: inline-block;
                border-radius: 3px;
                margin-right: 5px;
                vertical-align: middle;
            }
            .print-footer {
                margin-top: 50px;
                text-align: center;
                color: #666;
                font-size: 12px;
            }
        `);
        printWindow.document.write('</style></head><body>');

        // Header
        printWindow.document.write('<div class="print-header">');
        printWindow.document.write('<h1>Category Details Report</h1>');
        printWindow.document.write('<h2>' + categoryPrintData.name + '</h2>');
        printWindow.document.write('<p>Generated on: ' + new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString() + '</p>');
        printWindow.document.write('</div>');

        // Statistics
        printWindow.document.write('<div class="stats-row">');
        printWindow.document.write('<div class="stat-box total"><h4>Total Courses</h4><p style="font-size: 24px; margin:0;">' + categoryPrintData.coursesCount + '</p></div>');
        printWindow.document.write('<div class="stat-box published"><h4>Published</h4><p style="font-size: 24px; margin:0;">' + categoryPrintData.publishedCount + '</p></div>');
        printWindow.document.write('<div class="stat-box draft"><h4>Draft</h4><p style="font-size: 24px; margin:0;">' + categoryPrintData.draftCount + '</p></div>');
        printWindow.document.write('</div>');

        // Basic Information
        printWindow.document.write('<div class="print-section">');
        printWindow.document.write('<h2>Basic Information</h2>');
        printWindow.document.write('<div class="info-box">');
        printWindow.document.write('<p><strong>Name:</strong> ' + categoryPrintData.name + '</p>');
        printWindow.document.write('<p><strong>Slug:</strong> ' + categoryPrintData.slug + '</p>');
        printWindow.document.write('<p><strong>Icon:</strong> ' + (categoryPrintData.icon || 'Default icon') + '</p>');
        if (categoryPrintData.color) {
            printWindow.document.write('<p><strong>Color:</strong> <span class="color-preview" style="background-color: ' + categoryPrintData.color + ';"></span> ' + categoryPrintData.color + '</p>');
        }
        printWindow.document.write('</div>');
        printWindow.document.write('</div>');

        // Description
        printWindow.document.write('<div class="print-section">');
        printWindow.document.write('<h2>Description</h2>');
        printWindow.document.write('<div class="info-box">');
        printWindow.document.write('<p>' + (categoryPrintData.description ? categoryPrintData.description.replace(/\n/g, '<br>') : 'No description provided') + '</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('</div>');

        // Courses
        if (categoryPrintData.courses.length > 0) {
            printWindow.document.write('<div class="print-section">');
            printWindow.document.write('<h2>Courses in this Category (' + categoryPrintData.coursesCount + ')</h2>');
            printWindow.document.write('<table>');
            printWindow.document.write('<thead><tr><th>Title</th><th>Instructor</th><th>Price</th><th>Level</th><th>Status</th></tr></thead>');
            printWindow.document.write('<tbody>');
            
            categoryPrintData.courses.forEach(function(course) {
                printWindow.document.write('<tr>');
                printWindow.document.write('<td><strong>' + course.title + '</strong><br><small>' + (course.description ? course.description.substring(0, 50) + '...' : '') + '</small></td>');
                printWindow.document.write('<td>' + course.instructor + '</td>');
                printWindow.document.write('<td>' + (course.price == 0 ? 'Free' : '$' + parseFloat(course.price).toFixed(2)) + '</td>');
                printWindow.document.write('<td>' + course.level.charAt(0).toUpperCase() + course.level.slice(1) + '</td>');
                printWindow.document.write('<td>' + course.published + '</td>');
                printWindow.document.write('</tr>');
            });
            
            printWindow.document.write('</tbody></table>');
            printWindow.document.write('</div>');
        }

        // Timeline
        printWindow.document.write('<div class="print-section">');
        printWindow.document.write('<h2>Timeline</h2>');
        printWindow.document.write('<div class="info-box">');
        printWindow.document.write('<p><strong>Created:</strong> ' + categoryPrintData.createdDate + '</p>');
        printWindow.document.write('<p><strong>Last Updated:</strong> ' + categoryPrintData.updatedDate + '</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('</div>');

        // Footer
        printWindow.document.write('<div class="print-footer">');
        printWindow.document.write('<p>--- End of Category Details Report ---</p>');
        printWindow.document.write('</div>');

        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        // Wait for content to load then print
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    });
</script>

<!-- Add CSS for the print button -->
<style>
.btn-print {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-print:hover {
    background: linear-gradient(145deg, #27ae60, #219653);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
    color: white;
}

.btn-print i {
    margin-right: 8px;
}

.stats-card {
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 12px;
}
</style>
@endsection

@push('styles')
    @vite(['resources/css/pages/categories.css'])
@endpush