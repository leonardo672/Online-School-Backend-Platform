<?php
namespace App\Services\Review;

use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReviewStatisticsService
{
    public function getStatistics(): array
    {
        return [
            'counts' => $this->getCounts(),
            'ratings' => $this->getRatingStats(),
            'distribution' => $this->getRatingDistribution(),
            'leaderboards' => $this->getLeaderboards(),
            'recent' => $this->getRecentReviews(),
        ];
    }

    private function getCounts(): array
    {
        return [
            'total' => Review::count(),
            'approved' => Review::where('approved', true)->count(),
            'pending' => Review::where('approved', false)->count(),
            'unique_reviewers' => Review::distinct('user_id')->count('user_id'),
            'five_star' => Review::where('rating', 5)->count(),
        ];
    }

    private function getRatingStats(): array
    {
        return [
            'average' => round(Review::avg('rating') ?? 0, 2),
            'highest' => Review::max('rating') ?? 0,
            'lowest' => Review::min('rating') ?? 0,
        ];
    }

    private function getRatingDistribution(): array
    {
        $distribution = Review::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        for ($i = 1; $i <= 5; $i++) {
            if (!isset($distribution[$i])) {
                $distribution[$i] = 0;
            }
        }

        ksort($distribution);
        return $distribution;
    }

    public function getMostReviewedCourse(): ?Course
    {
        return Course::select('courses.*', DB::raw('COUNT(reviews.id) as review_count'))
            ->leftJoin('reviews', 'courses.id', '=', 'reviews.course_id')
            ->groupBy('courses.id')
            ->orderByDesc('review_count')
            ->first();
    }

    public function getMostActiveReviewer(): ?User
    {
        return User::select('users.*', DB::raw('COUNT(reviews.id) as review_count'))
            ->leftJoin('reviews', 'users.id', '=', 'reviews.user_id')
            ->groupBy('users.id')
            ->orderByDesc('review_count')
            ->first();
    }

    private function getLeaderboards(): array
    {
        return [
            'top_reviewers' => $this->getTopReviewers(5),
            'most_reviewed_courses' => $this->getMostReviewedCourses(5),
        ];
    }

    public function getTopReviewers(int $limit = 5): Collection
    {
        // Fix for PostgreSQL - can't use alias in HAVING clause
        return User::select(
                'users.*',
                DB::raw('COUNT(reviews.id) as review_count'),
                DB::raw('COALESCE(AVG(reviews.rating), 0) as average_rating')
            )
            ->leftJoin('reviews', 'users.id', '=', 'reviews.user_id')
            ->groupBy('users.id')
            ->having(DB::raw('COUNT(reviews.id)'), '>', 0) // Use raw expression instead of alias
            ->orderByDesc('review_count')
            ->limit($limit)
            ->get();
    }

    public function getMostReviewedCourses(int $limit = 5): Collection
    {
        // Fix for PostgreSQL - can't use alias in HAVING clause
        return Course::select(
                'courses.*',
                DB::raw('COUNT(reviews.id) as review_count'),
                DB::raw('COALESCE(AVG(reviews.rating), 0) as average_rating')
            )
            ->leftJoin('reviews', 'courses.id', '=', 'reviews.course_id')
            ->groupBy('courses.id')
            ->having(DB::raw('COUNT(reviews.id)'), '>', 0) // Use raw expression instead of alias
            ->orderByDesc('review_count')
            ->limit($limit)
            ->get();
    }

    public function getRecentReviews(int $limit = 5): Collection
    {
        return Review::with(['user', 'course'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getApiStats(): array
    {
        return [
            'total' => Review::count(),
            'average_rating' => Review::avg('rating'),
            'approved' => Review::where('approved', true)->count(),
            'pending' => Review::where('approved', false)->count(),
            'rating_distribution' => $this->getRatingDistribution(),
        ];
    }
}