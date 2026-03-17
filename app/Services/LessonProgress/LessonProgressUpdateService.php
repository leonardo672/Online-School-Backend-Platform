<?php
// app/Services/LessonProgress/LessonProgressUpdateService.php

namespace App\Services\LessonProgress;

use App\Models\LessonProgress;
use App\Exceptions\DuplicateProgressException;
use Illuminate\Support\Facades\DB;

class LessonProgressUpdateService
{
    protected LessonProgressQueryService $queryService;

    public function __construct(LessonProgressQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Create new lesson progress
     */
    public function create(array $data): LessonProgress
    {
        $this->validateNoDuplicate($data['user_id'], $data['lesson_id']);

        return DB::transaction(function () use ($data) {
            $progress = LessonProgress::create([
                'user_id' => $data['user_id'],
                'lesson_id' => $data['lesson_id'],
                'completed' => $data['completed'] ?? false,
                'completed_at' => isset($data['completed']) && $data['completed'] ? now() : null,
            ]);

            return $progress->load(['user', 'lesson.course']);
        });
    }

    /**
     * Update lesson progress
     */
    public function update(int $id, array $data): LessonProgress
    {
        $progress = LessonProgress::findOrFail($id);
        
        $this->validateNoDuplicate(
            $data['user_id'] ?? $progress->user_id,
            $data['lesson_id'] ?? $progress->lesson_id,
            $id
        );

        return DB::transaction(function () use ($progress, $data) {
            $updateData = [
                'user_id' => $data['user_id'] ?? $progress->user_id,
                'lesson_id' => $data['lesson_id'] ?? $progress->lesson_id,
                'completed' => $data['completed'] ?? $progress->completed,
            ];

            // Handle completed_at based on completion status change
            if (isset($data['completed'])) {
                if ($data['completed'] && !$progress->completed) {
                    $updateData['completed_at'] = now();
                } elseif (!$data['completed'] && $progress->completed) {
                    $updateData['completed_at'] = null;
                }
            }

            $progress->update($updateData);
            
            return $progress->fresh(['user', 'lesson.course']);
        });
    }

    /**
     * Mark lesson as complete for user
     */
    public function markComplete(int $userId, int $lessonId): LessonProgress
    {
        return DB::transaction(function () use ($userId, $lessonId) {
            $progress = LessonProgress::firstOrCreate([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ], [
                'completed' => false,
            ]);

            $progress->update([
                'completed' => true,
                'completed_at' => now(),
            ]);

            return $progress;
        });
    }

    /**
     * Mark lesson as incomplete for user
     */
    public function markIncomplete(int $userId, int $lessonId): LessonProgress
    {
        $progress = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->firstOrFail();

        $progress->update([
            'completed' => false,
            'completed_at' => null,
        ]);

        return $progress;
    }

    /**
     * Toggle completion status
     */
    public function toggle(int $id, ?bool $completed = null): LessonProgress
    {
        $progress = LessonProgress::findOrFail($id);
        
        $newStatus = $completed ?? !$progress->completed;
        
        $progress->update([
            'completed' => $newStatus,
            'completed_at' => $newStatus ? now() : null,
        ]);

        return $progress;
    }

    /**
     * Bulk complete progress records
     */
    public function bulkComplete(array $ids): int
    {
        return LessonProgress::whereIn('id', $ids)
            ->update([
                'completed' => true,
                'completed_at' => now(),
            ]);
    }

    /**
     * Validate no duplicate progress
     *
     * @throws DuplicateProgressException
     */
    protected function validateNoDuplicate(int $userId, int $lessonId, ?int $excludeId = null): void
    {
        $duplicate = $this->queryService->findDuplicate($userId, $lessonId, $excludeId);

        if ($duplicate) {
            throw new DuplicateProgressException('This user already has progress for this lesson.');
        }
    }

    /**
     * Delete progress
     */
    public function delete(int $id): bool
    {
        $progress = LessonProgress::findOrFail($id);
        return $progress->delete();
    }

    /**
     * Bulk delete progress
     */
    public function bulkDelete(array $ids): int
    {
        return LessonProgress::whereIn('id', $ids)->delete();
    }
}