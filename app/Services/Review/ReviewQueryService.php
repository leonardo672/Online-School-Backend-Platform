<?php
// app/Services/Review/ReviewQueryService.php

namespace App\Services\Review;

use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewQueryService
{
    /**
     * Get filtered reviews with pagination
     */
    public function getFilteredReviews(Request $request): LengthAwarePaginator
    {
        $query = Review::with(['user', 'course'])
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('course_id'), fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->filled('rating'), fn($q) => $q->where('rating', $request->rating))
            ->when($request->filled('approved'), fn($q) => $q->where('approved', $request->approved))
            ->when($request->filled('search'), fn($q) => $this->applySearchFilter($q, $request->search));

        $this->applySorting($query, $request->get('sort', 'newest'));

        return $query->paginate(20)->appends($request->query());
    }

    /**
     * Apply search filter
     */
    private function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"))
                ->orWhereHas('course', fn($courseQuery) => $courseQuery->where('title', 'like', "%{$search}%"))
                ->orWhere('comment', 'like', "%{$search}%");
        });
    }

    /**
     * Apply sorting
     */
    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'highest' => $query->orderBy('rating', 'desc'),
            'lowest' => $query->orderBy('rating', 'asc'),
            default => $query->latest(),
        };
    }
}