<?php
// app/Traits/HasCourseRelations.php

namespace App\Traits;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

trait HasCourseRelations
{
    /**
     * Get all courses for dropdown - FIXED: removed 'code' column
     */
    protected function getCoursesForDropdown(): Collection
    {
        return Course::select('id', 'title', 'slug')->orderBy('title')->get();
    }

    /**
     * Get course with its lessons
     */
    protected function getCourseWithLessons(int $courseId): Course
    {
        return Course::with(['lessons' => function ($query) {
            $query->orderBy('position');
        }])->findOrFail($courseId);
    }

    /**
     * Check if course has lessons
     */
    protected function courseHasLessons(int $courseId): bool
    {
        return Lesson::where('course_id', $courseId)->exists();
    }

    /**
     * Get lesson count for course
     */
    protected function getLessonCountForCourse(int $courseId): int
    {
        return Lesson::where('course_id', $courseId)->count();
    }

    /**
     * Get course by ID with optional loading
     */
    protected function findCourse(int $courseId, array $with = []): ?Course
    {
        $query = Course::query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->find($courseId);
    }

    /**
     * Get all courses with lesson counts
     */
    protected function getCoursesWithLessonCounts(): Collection
    {
        return Course::withCount('lessons')->get();
    }

    /**
     * Get published courses for dropdown
     */
    protected function getPublishedCoursesForDropdown(): Collection
    {
        return Course::published()
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
    }

    /**
     * Get courses by instructor for dropdown
     */
    protected function getCoursesByInstructorForDropdown(int $instructorId): Collection
    {
        return Course::byInstructor($instructorId)
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
    }
}