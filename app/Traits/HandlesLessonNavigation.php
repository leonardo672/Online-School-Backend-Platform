<?php
// app/Traits/HandlesLessonNavigation.php

namespace App\Traits;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

trait HandlesLessonNavigation
{
    /**
     * Get navigation data for lesson
     */
    protected function getLessonNavigation(Lesson $lesson): array
    {
        $courseLessons = $this->getOrderedCourseLessons($lesson->course_id);
        $currentIndex = $this->findLessonIndex($courseLessons, $lesson->id);

        return [
            'previous' => $currentIndex > 0 ? $courseLessons[$currentIndex - 1] : null,
            'next' => $currentIndex < $courseLessons->count() - 1 ? $courseLessons[$currentIndex + 1] : null,
            'current_position' => $currentIndex + 1,
            'total' => $courseLessons->count(),
            'all_lessons' => $courseLessons,
            'has_previous' => $currentIndex > 0,
            'has_next' => $currentIndex < $courseLessons->count() - 1,
        ];
    }

    /**
     * Get ordered course lessons - FIXED: removed 'slug'
     */
    protected function getOrderedCourseLessons(int $courseId): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->orderBy('position')
            ->get(['id', 'title', 'position']); // Removed 'slug'
    }

    /**
     * Find lesson index in collection
     */
    protected function findLessonIndex(Collection $lessons, int $lessonId): int
    {
        return $lessons->search(function ($item) use ($lessonId) {
            return $item->id === $lessonId;
        });
    }

    /**
     * Get related lessons excluding current - FIXED: removed 'slug' from any potential queries
     */
    protected function getRelatedLessonsExcludingCurrent(Lesson $lesson, int $limit = 5): Collection
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('id', '!=', $lesson->id)
            ->with('course')
            ->orderBy('position')
            ->limit($limit)
            ->get(['id', 'title', 'position', 'course_id']); // Specify only existing columns
    }

    /**
     * Get lesson breadcrumb trail
     */
    protected function getLessonBreadcrumbs(Lesson $lesson): array
    {
        return [
            ['title' => 'Courses', 'url' => route('courses.index')],
            ['title' => $lesson->course->title, 'url' => route('courses.show', $lesson->course_id)],
            ['title' => $lesson->title, 'url' => route('lessons.show', $lesson->id)],
        ];
    }

    /**
     * Get adjacent lessons (previous and next) - FIXED: removed 'slug'
     */
    protected function getAdjacentLessons(Lesson $lesson): array
    {
        return [
            'previous' => $this->getPreviousLesson($lesson),
            'next' => $this->getNextLesson($lesson),
        ];
    }

    /**
     * Get previous lesson - FIXED: removed 'slug'
     */
    protected function getPreviousLesson(Lesson $lesson): ?Lesson
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('position', '<', $lesson->position)
            ->orderBy('position', 'desc')
            ->first(['id', 'title', 'position']); // Removed 'slug'
    }

    /**
     * Get next lesson - FIXED: removed 'slug'
     */
    protected function getNextLesson(Lesson $lesson): ?Lesson
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('position', '>', $lesson->position)
            ->orderBy('position', 'asc')
            ->first(['id', 'title', 'position']); // Removed 'slug'
    }
}