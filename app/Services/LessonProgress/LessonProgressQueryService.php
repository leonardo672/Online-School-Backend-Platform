<?php
// app/Services/LessonProgress/LessonProgressQueryService.php

namespace App\Services\LessonProgress;

use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LessonProgressQueryService
{
    /**
     * Get filtered lesson progress with pagination
     */
    public function getFilteredProgress(Request $request): LengthAwarePaginator
    {
        $query = LessonProgress::with(['user', 'lesson.course'])
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('lesson_id'), fn($q) => $q->where('lesson_id', $request->lesson_id))
            ->when($request->filled('status'), fn($q) => $this->applyStatusFilter($q, $request->status))
            ->when($request->filled('search'), fn($q) => $this->applySearchFilter($q, $request->search))
            ->latest();

        return $query->paginate(20)->appends($request->query());
    }

    /**
     * Apply status filter
     */
    private function applyStatusFilter($query, string $status): void
    {
        if ($status === 'completed') {
            $query->where('completed', true);
        } elseif ($status === 'incomplete') {
            $query->where('completed', false);
        }
    }

    /**
     * Apply search filter
     */
    private function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                ->orWhereHas('lesson', fn($lessonQuery) => $lessonQuery->where('title', 'like', "%{$search}%"));
        });
    }

    /**
     * Check for duplicate progress
     */
    public function findDuplicate(int $userId, int $lessonId, ?int $excludeId = null): ?LessonProgress
    {
        $query = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }
}