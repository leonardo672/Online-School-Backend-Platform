<?php
// app/Http/Controllers/ReviewController.php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Traits\HandlesApiResponses;
use App\Traits\HandlesBulkActions;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Requests\Review\BulkActionRequest;
use App\Http\Requests\Review\FilterReviewRequest;
use App\Services\Review\ReviewQueryService;
use App\Services\Review\ReviewStatisticsService;
use App\Services\Review\ReviewValidationService;
use App\Services\Review\ReviewExportService;
use App\Exceptions\DuplicateReviewException;

class ReviewController extends Controller
{
    use HandlesApiResponses, HandlesBulkActions;

    protected ReviewQueryService $queryService;
    protected ReviewStatisticsService $statisticsService;
    protected ReviewValidationService $validationService;
    protected ReviewExportService $exportService;

    public function __construct(
        ReviewQueryService $queryService,
        ReviewStatisticsService $statisticsService,
        ReviewValidationService $validationService,
        ReviewExportService $exportService
    ) {
        $this->queryService = $queryService;
        $this->statisticsService = $statisticsService;
        $this->validationService = $validationService;
        $this->exportService = $exportService;
    }

    /**
     * Display a listing of the reviews.
     */
    public function index(FilterReviewRequest $request)
    {
        $reviews = $this->queryService->getFilteredReviews($request);
        $stats = $this->statisticsService->getStatistics();
        
        // Get most reviewed course and most active reviewer (these might not be in stats)
        $mostReviewedCourse = $this->statisticsService->getMostReviewedCourse();
        $mostReviewedCourseCount = $mostReviewedCourse ? $mostReviewedCourse->review_count : 0;
        
        $mostActiveReviewer = $this->statisticsService->getMostActiveReviewer();
        $mostActiveReviewerCount = $mostActiveReviewer ? $mostActiveReviewer->review_count : 0;
        
        return view('reviews.index', [
            // Main data
            'reviews' => $reviews,
            'users' => User::orderBy('name')->get(),
            'courses' => Course::orderBy('title')->get(),
            
            // Statistics from stats array
            'reviewsCount' => $stats['counts']['total'],
            'averageRating' => $stats['ratings']['average'],
            'approvedReviews' => $stats['counts']['approved'],
            'pendingReviews' => $stats['counts']['pending'],
            'fiveStarCount' => $stats['counts']['five_star'],
            'totalReviews' => $stats['counts']['total'],
            'uniqueReviewers' => $stats['counts']['unique_reviewers'],
            
            // Rating distribution
            'ratingDistribution' => $stats['distribution'],
            
            // Leaderboards
            'topReviewers' => $stats['leaderboards']['top_reviewers'],
            'mostReviewedCourses' => $stats['leaderboards']['most_reviewed_courses'],
            'recentReviews' => $stats['recent'],
            
            // Most reviewed course and active reviewer
            'mostReviewedCourse' => $mostReviewedCourse,
            'mostReviewedCourseCount' => $mostReviewedCourseCount,
            'mostActiveReviewer' => $mostActiveReviewer,
            'mostActiveReviewerCount' => $mostActiveReviewerCount,
        ]);
    }

    /**
     * Show the form for creating a new review.
     */
    public function create()
    {
        return view('reviews.create', [
            'users' => User::orderBy('name')->get(),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        try {
            $this->validationService->validateNoDuplicate(
                $request->user_id,
                $request->course_id
            );

            Review::create([
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'approved' => $request->approved ?? false,
            ]);

            return redirect()->route('reviews.index')
                ->with('success', 'Review created successfully!');

        } catch (DuplicateReviewException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified review.
     */
    public function show(string $id)
    {
        $review = Review::with(['user', 'course'])->findOrFail($id);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        
        return view('reviews.edit', [
            'review' => $review,
            'users' => User::orderBy('name')->get(),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    /**
     * Update the specified review in storage.
     */
    public function update(UpdateReviewRequest $request, $id)
    {
        try {
            $this->validationService->validateNoDuplicate(
                $request->user_id,
                $request->course_id,
                $id
            );

            $review = Review::findOrFail($id);
            $review->update($request->validated());

            return redirect()->route('reviews.index')
                ->with('success', 'Review updated successfully!');

        } catch (DuplicateReviewException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Approve or disapprove a review
     */
    public function approve(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $status = $request->input('status', 1);
        
        $review->update(['approved' => $status]);
        
        $action = $status ? 'approved' : 'disapproved';
        $message = "Review has been {$action} successfully.";

        return $this->handleApiOrRedirect($request, $message);
    }

    /**
     * Disapprove a review
     */
    public function disapprove(Request $request, $id)
    {
        return $this->approve($request->merge(['status' => 0]), $id);
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(BulkActionRequest $request)
    {
        return $this->bulkApprove($request, Review::query());
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete(BulkActionRequest $request)
    {
        return $this->bulkDelete($request, Review::query());
    }

    /**
     * Update review rating via AJAX
     */
    public function updateRating(Request $request, $id)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        $review = Review::findOrFail($id);
        $review->update(['rating' => $request->rating]);

        return $this->successResponse(
            ['rating' => $review->rating],
            'Rating updated successfully'
        );
    }

    /**
     * Export reviews
     */
    public function export(Request $request, $format = 'csv')
    {
        if ($format === 'pdf') {
            return redirect()->back()->with('info', 
                'PDF export requires dompdf package. Please install: composer require barryvdh/laravel-dompdf'
            );
        }

        return $this->exportService->exportToCsv($request);
    }

    /**
     * Get review statistics for API
     */
    public function apiStats(Request $request)
    {
        return $this->successResponse(
            $this->statisticsService->getApiStats()
        );
    }

    /**
     * Get reviews for a specific course
     */
    public function getCourseReviews($courseId)
    {
        $reviews = Review::with('user')
            ->where('course_id', $courseId)
            ->where('approved', true)
            ->latest()
            ->paginate(10);

        return $this->successResponse($reviews);
    }

    /**
     * Get reviews by a specific user
     */
    public function getUserReviews($userId)
    {
        $reviews = Review::with('course')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return $this->successResponse($reviews);
    }

    /**
     * Toggle approve status via API
     */
    public function toggleApprove($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['approved' => !$review->approved]);

        return $this->successResponse(
            ['approved' => $review->approved],
            'Review status updated'
        );
    }

    /**
     * Quick approve for AJAX requests
     */
    public function quickApprove($id)
    {
        try {
            $review = Review::findOrFail($id);
            $newStatus = !$review->approved;
            $review->update(['approved' => $newStatus]);

            return $this->successResponse(
                ['approved' => $newStatus],
                $newStatus ? 'Review approved' : 'Review disapproved'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update review status', 500);
        }
    }

    /**
     * Student view of their own reviews
     */
    public function studentIndex(Request $request)
    {
        $reviews = Review::with('course')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('reviews.student-index', compact('reviews'));
    }

    /**
     * Student store review
     */
    public function studentStore(StoreReviewRequest $request)
    {
        try {
            $this->validationService->validateNoDuplicate(
                auth()->id(),
                $request->course_id
            );

            Review::create([
                'user_id' => auth()->id(),
                'course_id' => $request->course_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'approved' => false,
            ]);

            return redirect()->back()
                ->with('success', 'Review submitted successfully. It will be visible after approval.');

        } catch (DuplicateReviewException $e) {
            return redirect()->back()
                ->with('error', 'You have already reviewed this course.');
        }
    }

    /**
     * Get review statistics page
     */
    public function stats()
    {
        return view('reviews.stats', $this->statisticsService->getStatistics());
    }

    /**
     * Handle API or redirect response
     */
    protected function handleApiOrRedirect(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->successResponse(null, $message);
        }

        return redirect()->route('reviews.index')->with('success', $message);
    }
}