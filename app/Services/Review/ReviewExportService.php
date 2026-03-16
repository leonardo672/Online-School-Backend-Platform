<?php
// app/Services/Review/ReviewExportService.php

namespace App\Services\Review;

use App\Models\Review;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewExportService
{
    /**
     * Export reviews to CSV
     */
    public function exportToCsv(Request $request): StreamedResponse
    {
        $reviews = $this->getFilteredReviewsForExport($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviews-export-' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, ['ID', 'User Name', 'User Email', 'Course Title', 'Rating', 'Comment', 'Approved', 'Created At']);
            
            // Data rows
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->user->name ?? 'N/A',
                    $review->user->email ?? 'N/A',
                    $review->course->title ?? 'N/A',
                    $review->rating,
                    $review->comment,
                    $review->approved ? 'Yes' : 'No',
                    $review->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get filtered reviews for export
     */
    private function getFilteredReviewsForExport(Request $request)
    {
        return Review::with(['user', 'course'])
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('course_id'), fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->filled('rating'), fn($q) => $q->where('rating', $request->rating))
            ->when($request->filled('approved'), fn($q) => $q->where('approved', $request->approved))
            ->get();
    }
}