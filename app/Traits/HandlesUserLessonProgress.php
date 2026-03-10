<?php
// app/Traits/HandlesUserLessonProgress.php

namespace App\Traits;

use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

trait HandlesUserLessonProgress
{
    /**
     * Check if user has completed lesson
     */
    protected function isLessonCompletedByUser(Lesson $lesson): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->completedLessons()
            ->where('lesson_id', $lesson->id)
            ->exists();
    }

    /**
     * Get user's completed lessons for a course
     */
    protected function getUserCompletedLessonsForCourse(int $courseId): Collection
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()->completedLessons()
            ->where('course_id', $courseId)
            ->get();
    }

    /**
     * Get user's progress percentage for a course
     */
    protected function getCourseProgressPercentage(int $courseId): int
    {
        if (!Auth::check()) {
            return 0;
        }

        $totalLessons = Lesson::where('course_id', $courseId)->count();
        
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = $this->getUserCompletedLessonsForCourse($courseId)->count();
        
        return (int) round(($completedLessons / $totalLessons) * 100);
    }

    /**
     * Get next incomplete lesson for user
     */
    protected function getNextIncompleteLesson(int $courseId): ?Lesson
    {
        if (!Auth::check()) {
            return Lesson::where('course_id', $courseId)
                ->orderBy('position')
                ->first();
        }

        $completedLessonIds = Auth::user()
            ->completedLessons()
            ->where('course_id', $courseId)
            ->pluck('lesson_id')
            ->toArray();

        return Lesson::where('course_id', $courseId)
            ->whereNotIn('id', $completedLessonIds)
            ->orderBy('position')
            ->first();
    }

    /**
     * Mark lesson as completed
     */
    protected function markLessonAsCompleted(int $lessonId): void
    {
        if (!Auth::check()) {
            return;
        }

        Auth::user()->completedLessons()->syncWithoutDetaching([$lessonId]);
    }

    /**
     * Mark lesson as incomplete
     */
    protected function markLessonAsIncomplete(int $lessonId): void
    {
        if (!Auth::check()) {
            return;
        }

        Auth::user()->completedLessons()->detach($lessonId);
    }

    /**
     * Toggle lesson completion status
     */
    protected function toggleLessonCompletion(int $lessonId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if ($user->completedLessons()->where('lesson_id', $lessonId)->exists()) {
            $user->completedLessons()->detach($lessonId);
            return false; // Now incomplete
        } else {
            $user->completedLessons()->syncWithoutDetaching([$lessonId]);
            return true; // Now complete
        }
    }

    /**
     * Get user's overall progress across all courses
     */
    protected function getUserOverallProgress(): array
    {
        if (!Auth::check()) {
            return ['completed' => 0, 'total' => 0, 'percentage' => 0];
        }

        $totalLessons = Lesson::count();
        $completedLessons = Auth::user()->completedLessons()->count();

        return [
            'completed' => $completedLessons,
            'total' => $totalLessons,
            'percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
        ];
    }

    /**
     * Check if user has completed all lessons in a course
     */
    protected function hasCompletedCourse(int $courseId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $totalLessons = Lesson::where('course_id', $courseId)->count();
        $completedLessons = $this->getUserCompletedLessonsForCourse($courseId)->count();

        return $totalLessons > 0 && $completedLessons === $totalLessons;
    }

    /**
     * Get completion status for multiple lessons
     */
    protected function getLessonsCompletionStatus(Collection $lessons): array
    {
        if (!Auth::check()) {
            return [];
        }

        $completedLessonIds = Auth::user()
            ->completedLessons()
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        $status = [];
        foreach ($lessons as $lesson) {
            $status[$lesson->id] = in_array($lesson->id, $completedLessonIds);
        }

        return $status;
    }
}