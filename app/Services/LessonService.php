<?php
// app/Services/LessonService.php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LessonService
{
    /**
     * Get paginated lessons with course relationship
     */
    public function getPaginatedLessons(int $perPage = 10): LengthAwarePaginator
    {
        return Lesson::with('course')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new lesson
     */
    public function createLesson(array $data): Lesson
    {
        return DB::transaction(function () use ($data) {
            // First, make sure position is valid
            $maxPosition = Lesson::where('course_id', $data['course_id'])->max('position') ?? 0;
            $position = min($data['position'], $maxPosition + 1);
            
            // Shift positions to make room for new lesson
            if ($position <= $maxPosition) {
                // Get all lessons that need to be shifted, ordered by position DESC
                $lessonsToShift = Lesson::where('course_id', $data['course_id'])
                    ->where('position', '>=', $position)
                    ->orderBy('position', 'desc')
                    ->lockForUpdate()
                    ->get();
                
                // Update them one by one from highest to lowest
                foreach ($lessonsToShift as $lesson) {
                    $lesson->update(['position' => $lesson->position + 1]);
                }
            }
            
            return Lesson::create([
                'course_id' => $data['course_id'],
                'title' => $data['title'],
                'content' => $data['content'] ?? null,
                'video_url' => $data['video_url'] ?? null,
                'position' => $position,
            ]);
        });
    }

    /**
     * Update an existing lesson - FIXED VERSION
     */
    public function updateLesson(Lesson $lesson, array $data): bool
    {
        return DB::transaction(function () use ($lesson, $data) {
            $oldCourseId = $lesson->course_id;
            $oldPosition = $lesson->position;
            $newCourseId = $data['course_id'];
            $newPosition = (int) $data['position'];
            
            // If nothing changed, just update the lesson data
            if ($oldCourseId == $newCourseId && $oldPosition == $newPosition) {
                return $lesson->update([
                    'title' => $data['title'],
                    'content' => $data['content'] ?? null,
                    'video_url' => $data['video_url'] ?? null,
                ]);
            }
            
            // If course changed
            if ($oldCourseId != $newCourseId) {
                // First, move the lesson to a temporary high position in the new course
                $tempPosition = Lesson::where('course_id', $newCourseId)->max('position') + 1000;
                $lesson->update([
                    'course_id' => $newCourseId,
                    'position' => $tempPosition,
                ]);
                
                // Close the gap in old course
                $lessonsToShift = Lesson::where('course_id', $oldCourseId)
                    ->where('position', '>', $oldPosition)
                    ->orderBy('position', 'asc')
                    ->lockForUpdate()
                    ->get();
                
                foreach ($lessonsToShift as $shiftLesson) {
                    $shiftLesson->update(['position' => $shiftLesson->position - 1]);
                }
                
                // Make space in new course at the target position
                $maxPosition = Lesson::where('course_id', $newCourseId)->max('position') ?? 0;
                $newPosition = min($newPosition, $maxPosition);
                
                $lessonsToShift = Lesson::where('course_id', $newCourseId)
                    ->where('position', '>=', $newPosition)
                    ->where('id', '!=', $lesson->id) // Exclude the current lesson
                    ->orderBy('position', 'desc')
                    ->lockForUpdate()
                    ->get();
                
                foreach ($lessonsToShift as $shiftLesson) {
                    $shiftLesson->update(['position' => $shiftLesson->position + 1]);
                }
                
                // Finally, move the lesson to its target position
                $lesson->update(['position' => $newPosition]);
            } 
            // If only position changed within same course
            else if ($oldPosition != $newPosition) {
                // Temporarily set the lesson to a very high position
                $maxPosition = Lesson::where('course_id', $oldCourseId)->max('position');
                $tempPosition = $maxPosition + 1000;
                
                // Move lesson to temporary position first
                $lesson->update(['position' => $tempPosition]);
                
                if ($newPosition < $oldPosition) {
                    // Moving up - shift lessons between new and old position down
                    $lessonsToShift = Lesson::where('course_id', $oldCourseId)
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->orderBy('position', 'desc')
                        ->lockForUpdate()
                        ->get();
                    
                    foreach ($lessonsToShift as $shiftLesson) {
                        $shiftLesson->update(['position' => $shiftLesson->position + 1]);
                    }
                } else {
                    // Moving down - shift lessons between old and new position up
                    $lessonsToShift = Lesson::where('course_id', $oldCourseId)
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->orderBy('position', 'asc')
                        ->lockForUpdate()
                        ->get();
                    
                    foreach ($lessonsToShift as $shiftLesson) {
                        $shiftLesson->update(['position' => $shiftLesson->position - 1]);
                    }
                }
                
                // Now set the lesson to its final position
                $lesson->update(['position' => $newPosition]);
            }
            
            // Update the lesson data (title, content, etc.)
            return $lesson->update([
                'title' => $data['title'],
                'content' => $data['content'] ?? null,
                'video_url' => $data['video_url'] ?? null,
            ]);
        });
    }

    /**
     * Delete a lesson
     */
    public function deleteLesson(Lesson $lesson): bool
    {
        return DB::transaction(function () use ($lesson) {
            $courseId = $lesson->course_id;
            $deletedPosition = $lesson->position;
            
            $result = $lesson->delete();
            
            if ($result) {
                // Reorder remaining lessons
                $lessonsToShift = Lesson::where('course_id', $courseId)
                    ->where('position', '>', $deletedPosition)
                    ->orderBy('position', 'asc')
                    ->lockForUpdate()
                    ->get();
                
                foreach ($lessonsToShift as $shiftLesson) {
                    $shiftLesson->update(['position' => $shiftLesson->position - 1]);
                }
            }
            
            return $result;
        });
    }

    /**
     * Get lesson with navigation data
     */
    public function getLessonWithNavigation(Lesson $lesson): array
    {
        $lesson->load('course');
        
        $previousLesson = $this->getPreviousLesson($lesson);
        $nextLesson = $this->getNextLesson($lesson);
        $courseLessons = $this->getCourseLessons($lesson->course_id);
        
        $lessonPosition = $courseLessons->search(function ($item) use ($lesson) {
            return $item->id === $lesson->id;
        }) + 1;

        $relatedLessons = $this->getRelatedLessons($lesson);

        return [
            'lesson' => $lesson,
            'previousLesson' => $previousLesson,
            'nextLesson' => $nextLesson,
            'courseLessons' => $courseLessons,
            'lessonPosition' => $lessonPosition,
            'totalLessons' => $courseLessons->count(),
            'relatedLessons' => $relatedLessons
        ];
    }

    /**
     * Get previous lesson in the same course
     */
    protected function getPreviousLesson(Lesson $lesson): ?Lesson
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('position', '<', $lesson->position)
            ->orderBy('position', 'desc')
            ->first();
    }

    /**
     * Get next lesson in the same course
     */
    protected function getNextLesson(Lesson $lesson): ?Lesson
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('position', '>', $lesson->position)
            ->orderBy('position', 'asc')
            ->first();
    }

    /**
     * Get all lessons in a course ordered by position
     */
    protected function getCourseLessons(int $courseId): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->orderBy('position')
            ->get(['id', 'title', 'position']);
    }

    /**
     * Get related lessons from the same course
     */
    protected function getRelatedLessons(Lesson $lesson, int $limit = 5): Collection
    {
        return Lesson::where('course_id', $lesson->course_id)
            ->where('id', '!=', $lesson->id)
            ->with('course:id,title')
            ->orderBy('position')
            ->limit($limit)
            ->get(['id', 'title', 'position', 'course_id']);
    }

    /**
     * Get lessons by course with pagination
     */
    public function getLessonsByCourse(int $courseId, int $perPage = 10): LengthAwarePaginator
    {
        return Lesson::where('course_id', $courseId)
            ->with('course:id,title')
            ->orderBy('position')
            ->paginate($perPage);
    }

    /**
     * Mark lesson as completed for user
     */
    public function markLessonAsCompleted($user, int $lessonId): void
    {
        $user->completedLessons()->syncWithoutDetaching([$lessonId]);
    }

    /**
     * Check if user has completed lesson
     */
    public function isLessonCompletedByUser($user, int $lessonId): bool
    {
        if (!$user) {
            return false;
        }

        return $user->completedLessons()
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    /**
     * Get user's completed lessons for a course
     */
    public function getUserCompletedLessonsForCourse($user, int $courseId): Collection
    {
        if (!$user) {
            return collect();
        }

        return $user->completedLessons()
            ->where('course_id', $courseId)
            ->get();
    }

    /**
     * Get user's progress percentage for a course
     */
    public function getCourseProgressPercentage($user, int $courseId): int
    {
        if (!$user) {
            return 0;
        }

        $totalLessons = Lesson::where('course_id', $courseId)->count();
        
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = $this->getUserCompletedLessonsForCourse($user, $courseId)->count();
        
        return (int) round(($completedLessons / $totalLessons) * 100);
    }

    /**
     * Get next incomplete lesson for user
     */
    public function getNextIncompleteLesson($user, int $courseId): ?Lesson
    {
        if (!$user) {
            return Lesson::where('course_id', $courseId)
                ->orderBy('position')
                ->first();
        }

        $completedLessonIds = $user->completedLessons()
            ->where('course_id', $courseId)
            ->pluck('lesson_id')
            ->toArray();

        return Lesson::where('course_id', $courseId)
            ->whereNotIn('id', $completedLessonIds)
            ->orderBy('position')
            ->first();
    }

    /**
     * Fix positions by renumbering all lessons in a course
     */
    public function fixPositions(int $courseId): void
    {
        DB::transaction(function () use ($courseId) {
            $lessons = Lesson::where('course_id', $courseId)
                ->orderBy('position')
                ->lockForUpdate()
                ->get();
            
            $position = 1;
            foreach ($lessons as $lesson) {
                if ($lesson->position != $position) {
                    $lesson->update(['position' => $position]);
                }
                $position++;
            }
        });
    }
}