<?php
// app/Http/Controllers/LessonProgressController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonProgress; 
use App\Traits\HandlesApiResponses;
use App\Exceptions\DuplicateProgressException;
use App\Http\Requests\LessonProgress\StoreLessonProgressRequest;
use App\Http\Requests\LessonProgress\UpdateLessonProgressRequest;
use App\Http\Requests\LessonProgress\FilterLessonProgressRequest;
use App\Http\Requests\LessonProgress\BulkActionRequest;
use App\Services\LessonProgress\LessonProgressQueryService;
use App\Services\LessonProgress\LessonProgressStatisticsService;
use App\Services\LessonProgress\LessonProgressUpdateService;
use Illuminate\Http\Request; 

class LessonProgressController extends Controller
{
    use HandlesApiResponses;

    protected LessonProgressQueryService $queryService;
    protected LessonProgressStatisticsService $statisticsService;
    protected LessonProgressUpdateService $updateService;

    public function __construct(
        LessonProgressQueryService $queryService,
        LessonProgressStatisticsService $statisticsService,
        LessonProgressUpdateService $updateService
    ) {
        $this->queryService = $queryService;
        $this->statisticsService = $statisticsService;
        $this->updateService = $updateService;
    }

    /**
     * Display a listing of the lesson progress.
     */
    public function index(FilterLessonProgressRequest $request)
    {
        $lessonProgresses = $this->queryService->getFilteredProgress($request);
        $stats = $this->statisticsService->getStatistics();

        return view('lesson-progress.index', [
            'lessonProgresses' => $lessonProgresses,
            'users' => User::all(),
            'lessons' => Lesson::with('course')->get(),
            'lessonProgressesCount' => $stats['total'],
            'completedCount' => $stats['completed'],
            'incompleteCount' => $stats['incomplete'],
            'completionPercentage' => $stats['percentage'],
            'latestCompletion' => $stats['latest_completion'],
        ]);
    }

    /**
     * Show the form for creating a new lesson progress.
     */
    public function create()
    {
        return view('lesson-progress.create', [
            'users' => User::all(),
            'lessons' => Lesson::with('course')->get(),
        ]);
    }

    /**
     * Store a newly created lesson progress in storage.
     */
    public function store(StoreLessonProgressRequest $request)
    {
        try {
            $progress = $this->updateService->create($request->validated());

            return redirect()->route('lesson-progress.index')
                ->with('success', 'Lesson progress created successfully!');

        } catch (DuplicateProgressException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified lesson progress.
     */
    public function show(string $id)
    {
        $progress = LessonProgress::with(['user', 'lesson.course'])->findOrFail($id);
        return view('lesson-progress.show', compact('progress'));
    }

    /**
     * Show the form for editing the specified lesson progress.
     */
    public function edit($id)
    {
        $progress = LessonProgress::findOrFail($id);
        
        return view('lesson-progress.edit', [
            'progress' => $progress,
            'users' => User::all(),
            'lessons' => Lesson::with('course')->get(),
        ]);
    }

    /**
     * Update the specified lesson progress in storage.
     */
    public function update(UpdateLessonProgressRequest $request, $id)
    {
        try {
            $progress = $this->updateService->update($id, $request->validated());

            return redirect()->route('lesson-progress.index')
                ->with('success', 'Lesson progress updated successfully!');

        } catch (DuplicateProgressException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified lesson progress from storage.
     */
    public function destroy(string $id)
    {
        $this->updateService->delete($id);
        return redirect()->route('lesson-progress.index')
            ->with('success', 'Lesson progress deleted successfully.');
    }

    /**
     * Mark lesson as complete for authenticated user.
     */
    public function markComplete($lessonId) // Removed unused Request parameter
    {
        try {
            $progress = $this->updateService->markComplete(auth()->id(), $lessonId);
            
            return back()->with('success', 'Lesson marked as complete!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark lesson as complete.');
        }
    }

    /**
     * Mark lesson as incomplete for authenticated user.
     */
    public function markIncomplete($lessonId) // Removed unused Request parameter
    {
        try {
            $progress = $this->updateService->markIncomplete(auth()->id(), $lessonId);
            
            return back()->with('success', 'Lesson marked as incomplete!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark lesson as incomplete.');
        }
    }

    /**
     * Toggle completion status via AJAX
     */
    public function toggle(Request $request, $id)
    {
        try {
            $progress = $this->updateService->toggle($id, $request->input('completed'));

            return $this->successResponse(
                ['completed' => $progress->completed],
                'Progress updated successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update progress', 500);
        }
    }

    /**
     * Bulk complete progress records
     */
    public function bulkComplete(BulkActionRequest $request)
    {
        $updated = $this->updateService->bulkComplete($request->ids);

        return $this->successResponse(
            ['updated' => $updated],
            "{$updated} records marked as complete"
        );
    }

    /**
     * Bulk delete progress records
     */
    public function bulkDelete(BulkActionRequest $request)
    {
        $deleted = $this->updateService->bulkDelete($request->ids);

        return $this->successResponse(
            ['deleted' => $deleted],
            "{$deleted} records deleted successfully"
        );
    }

    /**
     * Get quick stats via AJAX
     */
    public function stats()
    {
        return $this->successResponse(
            $this->statisticsService->getQuickStats()
        );
    }

    /**
     * Get user progress statistics for API
     */
    public function getUserProgress($id)
    {
        $user = User::findOrFail($id);
        $stats = $this->statisticsService->getSingleUserStats($id);
        
        return $this->successResponse([
            'user_name' => $user->name,
            'user_email' => $user->email,
            ...$stats
        ]);
    }

    /**
     * Get lesson progress statistics for API
     */
    public function getLessonProgressStats($id)
    {
        $stats = $this->statisticsService->getSingleLessonStats($id);
        return $this->successResponse($stats);
    }

    /**
     * Check for duplicate progress record
     */
    public function checkDuplicate($userId, $lessonId)
    {
        $excludeId = request()->get('exclude');
        $duplicate = $this->queryService->findDuplicate($userId, $lessonId, $excludeId);

        return $this->successResponse([
            'exists' => !is_null($duplicate),
            'progress_id' => $duplicate?->id,
        ]);
    }
}