<?php
// app/Services/Review/ReviewValidationService.php

namespace App\Services\Review;

use App\Models\Review;
use App\Exceptions\DuplicateReviewException;

class ReviewValidationService
{
    /**
     * Check for duplicate review
     *
     * @throws DuplicateReviewException
     */
    public function validateNoDuplicate(int $userId, int $courseId, ?int $excludeReviewId = null): void
    {
        $query = Review::where('user_id', $userId)
            ->where('course_id', $courseId);

        if ($excludeReviewId) {
            $query->where('id', '!=', $excludeReviewId);
        }

        if ($query->exists()) {
            throw new DuplicateReviewException('This user has already reviewed this course.');
        }
    }

    /**
     * Check if user can review course
     */
    public function canReview(int $userId, int $courseId): bool
    {
        return !Review::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }
}