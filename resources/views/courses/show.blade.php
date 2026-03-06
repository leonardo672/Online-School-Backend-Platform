@extends('layout')
@section('content')

<!-- Success Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0">
                <i class="fas fa-graduation-cap"></i> Course Details: {{ $course->title }}
            </h4>
            <div class="mt-2 mt-md-0">
                <span class="badge {{ $course->published ? 'badge-published' : 'badge-draft' }} me-2">
                    <i class="fas {{ $course->published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $course->published ? 'Published' : 'Draft' }}
                </span>
                <div class="btn-group">
                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-edit btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-delete btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
  
    <div class="card-body">
        <div class="row">
            <!-- Left Column: Course Info -->
            <div class="col-md-8">
                <div class="course-details mb-4">
                    <!-- Course Metadata Badges -->
                    <div class="mb-3">
                        @if($course->category)
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-folder"></i> {{ $course->category->name }}
                            </span>
                        @endif
                        
                        @if($course->level == 'beginner')
                            <span class="badge bg-info me-2">
                                <i class="fas fa-seedling"></i> Beginner
                            </span>
                        @elseif($course->level == 'intermediate')
                            <span class="badge bg-warning me-2">
                                <i class="fas fa-tree"></i> Intermediate
                            </span>
                        @elseif($course->level == 'advanced')
                            <span class="badge bg-danger me-2">
                                <i class="fas fa-fire"></i> Advanced
                            </span>
                        @endif
                        
                        @if($course->price == 0)
                            <span class="badge bg-success me-2">
                                <i class="fas fa-gift"></i> Free
                            </span>
                        @endif
                    </div>
                    
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-info-circle"></i> Course Information
                    </h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-book text-muted"></i> Title:</strong>
                                <p class="mb-2">{{ $course->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-link text-muted"></i> Slug:</strong>
                                <p class="mb-2"><code>{{ $course->slug }}</code></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-layer-group text-muted"></i> Category:</strong>
                                <p class="mb-2">
                                    @if($course->category)
                                        <span class="badge bg-secondary">{{ $course->category->name }}</span>
                                        <br>
                                        <small class="text-muted">Slug: {{ $course->category->slug }}</small>
                                    @else
                                        <span class="badge bg-danger">No Category Assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-user-tie text-muted"></i> Instructor:</strong>
                                <p class="mb-2">
                                    @if($course->instructor)
                                        {{ $course->instructor->name }}
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope"></i> {{ $course->instructor->email }}
                                        </small>
                                    @else
                                        <span class="text-danger">No Instructor Assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-money-bill-wave text-muted"></i> Price:</strong>
                                <p class="mb-2">
                                    @if($course->price == 0)
                                        <span class="badge bg-success">Free Course</span>
                                    @else
                                        <span class="h5 text-success">${{ number_format($course->price, 2) }}</span>
                                        <br>
                                        <small class="text-muted">Regular Price</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong><i class="fas fa-signal text-muted"></i> Difficulty Level:</strong>
                                <p class="mb-2">
                                    @if($course->level == 'beginner')
                                        <span class="badge bg-info">Beginner - No experience needed</span>
                                    @elseif($course->level == 'intermediate')
                                        <span class="badge bg-warning">Intermediate - Some experience required</span>
                                    @elseif($course->level == 'advanced')
                                        <span class="badge bg-danger">Advanced - Experienced learners only</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong><i class="fas fa-align-left text-muted"></i> Description:</strong>
                        <div class="p-4 bg-light rounded mt-2">
                            {!! nl2br(e($course->description)) !!}
                        </div>
                    </div>

                    <!-- Course Tags (if you have a tags relationship) -->
                    @if(method_exists($course, 'tags') && $course->tags->isNotEmpty())
                    <div class="mb-4">
                        <strong><i class="fas fa-tags text-muted"></i> Tags:</strong>
                        <div class="mt-2">
                            @foreach($course->tags as $tag)
                                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Statistics & Actions -->
            <div class="col-md-4">
                <div class="course-stats">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-chart-bar"></i> Course Analytics
                    </h5>
                    
                    <div class="stats-card mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white rounded">
                            <div>
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0">{{ $course->enrollments_count ?? 0 }}</h3>
                                <small>Total Enrolled Students</small>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-success text-white rounded">
                            <div>
                                <i class="fas fa-book-open fa-2x"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0">{{ $course->lessons_count ?? 0 }}</h3>
                                <small>Total Lessons</small>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-warning text-white rounded">
                            <div>
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0">{{ number_format($course->average_rating ?? 0, 1) }}</h3>
                                <small>Average Rating ({{ $course->reviews_count ?? 0 }} reviews)</small>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-info text-white rounded">
                            <div>
                                <i class="fas fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0">{{ $course->created_at->format('M d, Y') }}</h5>
                                <small>Created ({{ $course->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions mt-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('lessons.index', ['course_id' => $course->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus-circle"></i> Manage Lessons
                        </a>
                        <a href="{{ route('enrollments.create', ['course_id' => $course->id]) }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus"></i> Enroll Students
                        </a>
                        <a href="{{ route('certificates.create', ['course_id' => $course->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-certificate"></i> Issue Certificate
                        </a>
                        <a href="{{ route('reviews.index', ['course_id' => $course->id]) }}" class="btn btn-outline-warning">
                            <i class="fas fa-star"></i> View Reviews ({{ $course->reviews_count ?? 0 }})
                        </a>
                        <button class="btn btn-outline-secondary" onclick="copyCourseLink()">
                            <i class="fas fa-link"></i> Copy Course Link
                        </button>
                        <button class="btn btn-print mt-3" onclick="printCourseDetails()">
                            <i class="fas fa-print"></i> Print Course Details
                        </button>
                    </div>
                </div>

                <!-- Share Options -->
                <div class="share-options mt-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-share-alt"></i> Share Course
                    </h5>
                    <div class="d-flex justify-content-around">
                        <a href="#" class="text-primary" onclick="shareOnFacebook()">
                            <i class="fab fa-facebook fa-2x"></i>
                        </a>
                        <a href="#" class="text-info" onclick="shareOnTwitter()">
                            <i class="fab fa-twitter fa-2x"></i>
                        </a>
                        <a href="#" class="text-success" onclick="shareOnWhatsApp()">
                            <i class="fab fa-whatsapp fa-2x"></i>
                        </a>
                        <a href="#" class="text-secondary" onclick="shareViaEmail()">
                            <i class="fas fa-envelope fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="additional-info p-4 bg-light rounded">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-clock"></i> Timeline & Metadata
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Course ID:</strong>
                            <p><code>#{{ $course->id }}</code></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Created At:</strong>
                            <p>{{ $course->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Last Updated:</strong>
                            <p>{{ $course->updated_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <p>
                                @if($course->published)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle"></i> Published
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-clock"></i> Draft
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (if available) -->
        @if(isset($recentActivity) && $recentActivity->isNotEmpty())
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="recent-activity p-4 bg-light rounded">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-history"></i> Recent Activity
                    </h5>
                    <div class="timeline">
                        @foreach($recentActivity as $activity)
                            <div class="timeline-item">
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                <p>{{ $activity->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the course <strong>"{{ $course->title }}"</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    This action cannot be undone. All associated data will be permanently deleted:
                    <ul class="mt-2 mb-0">
                        <li>{{ $course->lessons_count ?? 0 }} lessons</li>
                        <li>{{ $course->enrollments_count ?? 0 }} student enrollments</li>
                        <li>{{ $course->reviews_count ?? 0 }} reviews</li>
                        <li>All certificates and progress records</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Permanently Delete Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Functions -->
<script>
// Print function
function printCourseDetails() {
    const printWindow = window.open('', '_blank');
    const courseTitle = "{{ $course->title }}";
    const courseDescription = `{{ $course->description }}`;
    const coursePrice = {{ $course->price }};
    const courseLevel = "{{ $course->level }}";
    const courseStatus = "{{ $course->published ? 'Published' : 'Draft' }}";
    const createdDate = "{{ $course->created_at->format('F d, Y') }}";
    const updatedDate = "{{ $course->updated_at->format('F d, Y') }}";
    const categoryName = "{{ $course->category ? $course->category->name : 'No Category' }}";
    const instructorName = "{{ $course->instructor ? $course->instructor->name : 'No Instructor' }}";
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Course Details - ${courseTitle}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
                .header { text-align: center; border-bottom: 2px solid #3498db; padding-bottom: 20px; margin-bottom: 30px; }
                .header h1 { color: #2c3e50; margin: 0; }
                .section { margin-bottom: 30px; }
                .section h2 { color: #3498db; border-bottom: 1px solid #eee; padding-bottom: 10px; }
                .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
                .info-item { margin-bottom: 15px; }
                .info-item strong { display: block; color: #666; font-size: 12px; text-transform: uppercase; }
                .info-item p { margin: 5px 0; font-size: 16px; }
                .badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
                .badge-published { background: #2ecc71; color: white; }
                .badge-draft { background: #f39c12; color: white; }
                .description { background: #f8f9fa; padding: 20px; border-radius: 8px; line-height: 1.6; }
                .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Course Details Report</h1>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </div>
            
            <div class="section">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Course Title</strong>
                        <p>${courseTitle}</p>
                    </div>
                    <div class="info-item">
                        <strong>Status</strong>
                        <p><span class="badge ${courseStatus === 'Published' ? 'badge-published' : 'badge-draft'}">${courseStatus}</span></p>
                    </div>
                    <div class="info-item">
                        <strong>Category</strong>
                        <p>${categoryName}</p>
                    </div>
                    <div class="info-item">
                        <strong>Instructor</strong>
                        <p>${instructorName}</p>
                    </div>
                    <div class="info-item">
                        <strong>Price</strong>
                        <p>${coursePrice === 0 ? 'Free' : '$' + coursePrice.toFixed(2)}</p>
                    </div>
                    <div class="info-item">
                        <strong>Level</strong>
                        <p>${courseLevel.charAt(0).toUpperCase() + courseLevel.slice(1)}</p>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>Description</h2>
                <div class="description">
                    ${courseDescription.replace(/\n/g, '<br>')}
                </div>
            </div>
            
            <div class="section">
                <h2>Course Details</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Created On</strong>
                        <p>${createdDate}</p>
                    </div>
                    <div class="info-item">
                        <strong>Last Updated</strong>
                        <p>${updatedDate}</p>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>--- End of Report ---</p>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Copy course link function
function copyCourseLink() {
    const courseUrl = window.location.href;
    navigator.clipboard.writeText(courseUrl).then(function() {
        alert('Course link copied to clipboard!');
    }, function() {
        alert('Failed to copy link. Please copy manually.');
    });
}

// Share functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("{{ $course->title }}");
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent("Check out this course: {{ $course->title }}");
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareOnWhatsApp() {
    const text = encodeURIComponent("Check out this course: {{ $course->title }} - " + window.location.href);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareViaEmail() {
    const subject = encodeURIComponent("Course: {{ $course->title }}");
    const body = encodeURIComponent("I thought you might be interested in this course:\n\n{{ $course->title }}\n\n" + window.location.href);
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Tooltip initialization
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        tooltipTriggerEl.setAttribute('data-bs-toggle', 'tooltip');
    });
    
    if (typeof bootstrap !== 'undefined') {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    }
});
</script>

<!-- CSS Styling -->
<style>
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

.card-body {
    padding: 30px;
}

/* Badge Styling */
.badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 14px;
}

.badge-published {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
}

.badge-draft {
    background: linear-gradient(145deg, #f39c12, #d35400);
    color: white;
}

/* Button Styling */
.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.btn-sm {
    padding: 5px 12px;
    font-size: 13px;
}

.btn-edit {
    background: linear-gradient(145deg, #fd7e14, #e8650f);
    color: white;
    border: none;
}

.btn-edit:hover {
    background: linear-gradient(145deg, #e8650f, #c0550d);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(253, 126, 20, 0.3);
    color: white;
}

.btn-delete {
    background: linear-gradient(145deg, #dc3545, #c82333);
    color: white;
    border: none;
}

.btn-delete:hover {
    background: linear-gradient(145deg, #c82333, #a71d2a);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    color: white;
}

.btn-print {
    background: linear-gradient(145deg, #3498db, #2980b9);
    color: white;
    border: none;
    width: 100%;
}

.btn-print:hover {
    background: linear-gradient(145deg, #2980b9, #1c5a7a);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    color: white;
}

.btn-outline-primary, .btn-outline-success, .btn-outline-info, .btn-outline-warning, .btn-outline-secondary {
    border-width: 2px;
    font-weight: 600;
}

/* Stats Cards */
.stats-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

/* Info Items */
.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    height: 100%;
    border-left: 3px solid #3498db;
}

.info-item strong i {
    width: 20px;
    margin-right: 5px;
    color: #3498db;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.rounded {
    border-radius: 10px !important;
}

/* Quick Actions */
.quick-actions .btn-outline-primary {
    color: #3498db;
    border-color: #3498db;
}

.quick-actions .btn-outline-primary:hover {
    background: linear-gradient(145deg, #3498db, #2980b9);
    color: white;
    border-color: transparent;
}

.quick-actions .btn-outline-success {
    color: #2ecc71;
    border-color: #2ecc71;
}

.quick-actions .btn-outline-success:hover {
    background: linear-gradient(145deg, #2ecc71, #27ae60);
    color: white;
    border-color: transparent;
}

.quick-actions .btn-outline-info {
    color: #17a2b8;
    border-color: #17a2b8;
}

.quick-actions .btn-outline-info:hover {
    background: linear-gradient(145deg, #17a2b8, #138496);
    color: white;
    border-color: transparent;
}

.quick-actions .btn-outline-warning {
    color: #f39c12;
    border-color: #f39c12;
}

.quick-actions .btn-outline-warning:hover {
    background: linear-gradient(145deg, #f39c12, #d35400);
    color: white;
    border-color: transparent;
}

/* Share Options */
.share-options a {
    transition: all 0.3s ease;
    display: inline-block;
}

.share-options a:hover {
    transform: translateY(-5px) scale(1.1);
}

/* Additional Info */
.additional-info {
    border-left: 4px solid #3498db;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #3498db;
    border: 2px solid white;
}

/* Text Colors */
.text-primary {
    color: #3498db !important;
}

.text-success {
    color: #2ecc71 !important;
}

.text-warning {
    color: #f39c12 !important;
}

.text-info {
    color: #17a2b8 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-header .d-flex {
        flex-direction: column;
        align-items: start !important;
    }
    
    .card-header h4 {
        margin-bottom: 10px;
    }
    
    .btn-group {
        margin-top: 10px;
    }
    
    .info-item {
        margin-bottom: 10px;
    }
}

/* Alert Styling */
.alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>

@endsection