<?php
// app/Services/LessonProgress/LessonProgressStatisticsService.php

namespace App\Services\LessonProgress;

use App\Models\LessonProgress;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;  

class LessonProgressStatisticsService
{
    /**
     * Get all statistics
     */
    public function getStatistics(): array
    {
        $total = LessonProgress::count();
        $completed = LessonProgress::where('completed', true)->count();
        $incomplete = LessonProgress::where('completed', false)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $incomplete,
            'percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'latest_completion' => $this->getLatestCompletion(), // Now returns Carbon|null
            'by_user' => $this->getUserStats(),
            'by_lesson' => $this->getLessonStats(),
        ];
    }

    /**
     * Get latest completion timestamp
     */
    public function getLatestCompletion(): ?\Carbon\Carbon
    {
        $timestamp = LessonProgress::where('completed', true)
            ->latest('completed_at')
            ->value('completed_at');
        
        return $timestamp ? \Carbon\Carbon::parse($timestamp) : null;
    }

    /**
     * Get user-specific statistics
     */
    public function getUserStats(?int $userId = null): array
    {
        if ($userId) {
            return $this->getSingleUserStats($userId);
        }

        return [
            'top_users' => $this->getTopUsers(5),
            'total_users_with_progress' => LessonProgress::distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Get statistics for a single user
     */
    public function getSingleUserStats(int $userId): array
    {
        $totalLessons = Lesson::count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->where('completed', true)
            ->count();
        $inProgress = LessonProgress::where('user_id', $userId)
            ->where('completed', false)
            ->count();

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'in_progress' => $inProgress,
            'completion_percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0,
            'last_activity' => LessonProgress::where('user_id', $userId)
                ->latest()
                ->value('updated_at'),
        ];
    }

    /**
     * Get top users by completed lessons
     */
    public function getTopUsers(int $limit = 5): Collection
    {
        // FIXED: Use DB::raw() in HAVING clause instead of alias
        return User::select(
                'users.*',
                DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END) as completed_count'),
                DB::raw('COUNT(lesson_progress.id) as total_progress')
            )
            ->leftJoin('lesson_progress', 'users.id', '=', 'lesson_progress.user_id')
            ->groupBy('users.id')
            ->having(DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END)'), '>', 0)
            ->orderByDesc('completed_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get lesson-specific statistics
     */
    public function getLessonStats(?int $lessonId = null): array
    {
        if ($lessonId) {
            return $this->getSingleLessonStats($lessonId);
        }

        return [
            'most_completed' => $this->getMostCompletedLessons(5),
            'least_completed' => $this->getLeastCompletedLessons(5),
        ];
    }

    /**
     * Get statistics for a single lesson
     */
    public function getSingleLessonStats(int $lessonId): array
    {
        $lesson = Lesson::with('course')->findOrFail($lessonId);
        
        $completedCount = LessonProgress::where('lesson_id', $lessonId)
            ->where('completed', true)
            ->count();
        $inProgressCount = LessonProgress::where('lesson_id', $lessonId)
            ->where('completed', false)
            ->count();
        $totalUsers = LessonProgress::where('lesson_id', $lessonId)
            ->distinct('user_id')
            ->count('user_id');

        return [
            'lesson_title' => $lesson->title,
            'course_title' => $lesson->course->title ?? null,
            'position' => $lesson->position,
            'is_published' => $lesson->is_published ?? true,
            'has_video' => !empty($lesson->video_url),
            'completed_count' => $completedCount,
            'in_progress_count' => $inProgressCount,
            'total_users' => $totalUsers,
            'completion_rate' => $totalUsers > 0 ? round(($completedCount / $totalUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Get most completed lessons
     */
    public function getMostCompletedLessons(int $limit = 5): Collection
    {
        // FIXED: Use DB::raw() in HAVING clause
        return Lesson::select(
                'lessons.*',
                DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END) as completed_count')
            )
            ->leftJoin('lesson_progress', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->groupBy('lessons.id')
            ->having(DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END)'), '>', 0)
            ->orderByDesc('completed_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get least completed lessons
     */
    public function getLeastCompletedLessons(int $limit = 5): Collection
    {
        // FIXED: Use DB::raw() in HAVING clause
        return Lesson::select(
                'lessons.*',
                DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END) as completed_count')
            )
            ->leftJoin('lesson_progress', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->groupBy('lessons.id')
            ->having(DB::raw('COUNT(CASE WHEN lesson_progress.completed = true THEN 1 END)'), '>', 0)
            ->orderBy('completed_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get quick stats for AJAX
     */
    public function getQuickStats(): array
    {
        $total = LessonProgress::count();
        $completed = LessonProgress::where('completed', true)->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $total - $completed,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}