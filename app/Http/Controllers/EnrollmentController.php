<?php
// app/Http/Controllers/EnrollmentController.php

namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Traits\HasFlashMessages;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    use HasFlashMessages;

    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display a listing of the enrollments.
     */
    public function index()
    {
        $enrollments = $this->enrollmentService->getAllEnrollments();
        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create()
    {
        $users = $this->enrollmentService->getUsersForDropdown();
        $courses = $this->enrollmentService->getCoursesForDropdown();
        
        // Debug: Uncomment to check if data is being passed
        // dd($users, $courses);
        
        return view('enrollments.create', compact('users', 'courses'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        try {
            // Check if user is already enrolled
            if ($this->enrollmentService->isUserEnrolled($request->user_id, $request->course_id)) {
                return redirect()->back()
                    ->with('error', 'User is already enrolled in this course.')
                    ->withInput();
            }

            // Create enrollment
            $this->enrollmentService->createEnrollment([
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'enrolled_at' => $request->enrolled_at,
            ]);
            
            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment created successfully!');
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Enrollment creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create enrollment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified enrollment.
     */
    public function show(string $id)
    {
        $enrollment = $this->enrollmentService->getEnrollmentById($id);
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit(string $id)
    {
        $enrollment = $this->enrollmentService->getEnrollmentById($id);
        $users = $this->enrollmentService->getUsersForDropdown();
        $courses = $this->enrollmentService->getCoursesForDropdown();
        
        return view('enrollments.edit', compact('enrollment', 'users', 'courses'));
    }

    /**
     * Update the specified enrollment in storage.
     */
    public function update(UpdateEnrollmentRequest $request, string $id)
    {
        try {
            $this->enrollmentService->updateEnrollment($id, [
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'enrolled_at' => $request->enrolled_at,
            ]);
            
            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update enrollment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->enrollmentService->deleteEnrollment($id);
            
            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment deleted successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete enrollment. Please try again.');
        }
    }
}