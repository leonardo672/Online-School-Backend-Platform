<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use App\Traits\HandlesCourseFilters;
use App\Traits\HandlesCourseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Add this

class CourseController extends Controller
{
    use HandlesCourseFilters, HandlesCourseResponse;

    protected CourseService $courseService;

    /**
     * Inject CourseService dependency.
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the courses.
     */
    public function index(Request $request)
    {
        $filters = $this->extractFilters($request);
        
        $courses = $this->courseService->getFilteredCourses($filters);
        $statistics = $this->courseService->getCourseStatistics();
        $filterOptions = $this->getFilterOptions();

        if ($this->expectsJson($request)) {
            return $this->jsonResponse([
                'courses' => $courses,
                'statistics' => $statistics,
            ]);
        }

        return view('courses.index', array_merge(
            compact('courses'),
            $statistics,
            ['filterOptions' => $filterOptions]
        ));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $formData = $this->courseService->getFormData();
        
        return view('courses.create', $formData);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(CourseRequest $request)
    {
        try {
            // Log the validated data
            Log::info('Course store attempt', ['data' => $request->validated()]);
            
            $course = $this->courseService->createCourse($request->validated());
            
            // Log success
            Log::info('Course created successfully', ['course_id' => $course->id]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse([
                    'message' => 'Course created successfully!',
                    'course' => $course->load(['category', 'instructor']),
                ], 201);
            }
            
            // FIXED: Make sure we're redirecting with success message
            return redirect()
                ->route('courses.index')
                ->with('success', 'Course created successfully!');
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('Course creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
            
            // FIXED: Return back with error and input
            return back()
                ->with('error', 'Error creating course: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Request $request, string $id)
    {
        try {
            $course = Course::with(['category', 'instructor'])->findOrFail($id);
            
            // Add counts if relationships exist
            if (method_exists($course, 'enrollments')) {
                $course->loadCount('enrollments');
            }
            if (method_exists($course, 'lessons')) {
                $course->loadCount('lessons');
            }
            if (method_exists($course, 'reviews')) {
                $course->loadCount('reviews');
                $course->average_rating = $course->reviews()->avg('rating') ?? 0;
            }
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse(['course' => $course]);
            }
            
            return view('courses.show', compact('course'));
            
        } catch (\Exception $e) {
            Log::error('Course show failed', ['error' => $e->getMessage()]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse(['error' => 'Course not found'], 404);
            }
            
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found');
        }
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            $formData = $this->courseService->getFormData();
            
            return view('courses.edit', array_merge(
                compact('course'),
                $formData
            ));
            
        } catch (\Exception $e) {
            Log::error('Course edit failed', ['error' => $e->getMessage()]);
            
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found');
        }
    }

    /**
     * Update the specified course in storage.
     */
    public function update(CourseRequest $request, string $id)
    {
        try {
            Log::info('Course update attempt', ['id' => $id, 'data' => $request->validated()]);
            
            $course = Course::findOrFail($id);
            $updatedCourse = $this->courseService->updateCourse($course, $request->validated());
            
            Log::info('Course updated successfully', ['course_id' => $updatedCourse->id]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse([
                    'message' => 'Course updated successfully!',
                    'course' => $updatedCourse->load(['category', 'instructor']),
                ]);
            }
            
            return redirect()
                ->route('courses.index')
                ->with('success', 'Course updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Course update failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
            
            return back()
                ->with('error', 'Error updating course: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            Log::info('Course delete attempt', ['id' => $id]);
            
            $course = Course::findOrFail($id);
            $this->courseService->deleteCourse($course);
            
            Log::info('Course deleted successfully', ['id' => $id]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse([
                    'message' => 'Course deleted successfully!'
                ]);
            }
            
            return redirect()
                ->route('courses.index')
                ->with('success', 'Course deleted successfully!');
            
        } catch (\Exception $e) {
            Log::error('Course delete failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            if ($this->expectsJson($request)) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
}