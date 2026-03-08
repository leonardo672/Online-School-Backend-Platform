<?php
// app/Services/EnrollmentService.php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;

class EnrollmentService
{
    /**
     * Get all enrollments with related user and course data
     */
    public function getAllEnrollments(): Collection
    {
        return Enrollment::with(['user', 'course'])->get();
    }

    /**
     * Get enrollment by ID with related user and course data
     */
    public function getEnrollmentById(int $id): Enrollment
    {
        return Enrollment::with(['user', 'course'])->findOrFail($id);
    }

    /**
     * Create a new enrollment
     */
    public function createEnrollment(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    /**
     * Update an existing enrollment
     */
    public function updateEnrollment(int $id, array $data): Enrollment
    {
        $enrollment = $this->getEnrollmentById($id);
        $enrollment->update($data);
        
        return $enrollment->fresh(['user', 'course']);
    }

    /**
     * Delete an enrollment
     */
    public function deleteEnrollment(int $id): bool
    {
        $enrollment = $this->getEnrollmentById($id);
        return $enrollment->delete();
    }

    /**
     * Check if user is already enrolled in course
     */
    public function isUserEnrolled(int $userId, int $courseId): bool
    {
        return Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Get all users for dropdown
     */
    public function getUsersForDropdown(): Collection
    {
        return User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all courses for dropdown
     */
    public function getCoursesForDropdown(): Collection
    {
        return Course::select('id', 'title')
            ->orderBy('title')
            ->get();
    }
}