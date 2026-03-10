<?php
// app/Http/Controllers/LessonController.php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use App\Services\LessonService;
use App\Traits\HasCourseRelations;
use App\Traits\HandlesLessonNavigation;
use App\Traits\HandlesUserLessonProgress;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Requests\Lesson\MarkLessonCompleteRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LessonController extends Controller
{
    use HasCourseRelations, HandlesLessonNavigation, HandlesUserLessonProgress;

    /**
     * The lesson service instance.
     */
    protected LessonService $lessonService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    /**
     * Display a listing of the lessons.
     */
    public function index()
    {
        $lessons = $this->lessonService->getPaginatedLessons();
        
        return view('lessons.index', [
            'lessons' => $lessons,
            'totalCount' => $lessons->total(),
        ]);
    }

    /**
     * Show the form for creating a new lesson.
     */
    public function create()
    {
        $courses = $this->getCoursesForDropdown();
        
        return view('lessons.create', [
            'courses' => $courses,
            'nextPosition' => request('course_id') ? 
                $this->getLessonCountForCourse(request('course_id')) + 1 : 1,
        ]);
    }

    /**
     * Store a newly created lesson in storage.
     */
    public function store(StoreLessonRequest $request)
    {
        $lesson = $this->lessonService->createLesson($request->validated());

        return redirect()
            ->route('lessons.index')
            ->with('success', "Lesson '{$lesson->title}' created successfully!");
    }

    /**
     * Display the specified lesson.
     */
    public function show(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        
        // Get all navigation and related data from service
        $navigationData = $this->lessonService->getLessonWithNavigation($lesson);
        
        // Add user progress data if authenticated
        if (auth()->check()) {
            $navigationData['isCompleted'] = $this->lessonService->isLessonCompletedByUser(
                auth()->user(), 
                $lesson->id
            );
            $navigationData['courseProgress'] = $this->lessonService->getCourseProgressPercentage(
                auth()->user(), 
                $lesson->course_id
            );
            $navigationData['nextIncompleteLesson'] = $this->lessonService->getNextIncompleteLesson(
                auth()->user(),
                $lesson->course_id
            );
        }

        // Get breadcrumbs
        $navigationData['breadcrumbs'] = $this->getLessonBreadcrumbs($lesson);

        return view('lessons.show', $navigationData);
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit(string $id)
    {
        $lesson = Lesson::with('course')->findOrFail($id);
        $courses = $this->getCoursesForDropdown();
        
        // Get course lessons for position dropdown
        $courseLessons = $this->getOrderedCourseLessons($lesson->course_id);
        $maxPosition = $courseLessons->count();
        
        return view('lessons.edit', [
            'lesson' => $lesson,
            'courses' => $courses,
            'courseLessons' => $courseLessons,
            'maxPosition' => $maxPosition,
        ]);
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(UpdateLessonRequest $request, string $id)
    {
        $lesson = Lesson::findOrFail($id);
        
        $this->lessonService->updateLesson($lesson, $request->validated());

        return redirect()
            ->route('lessons.show', $lesson->id)
            ->with('success', "Lesson '{$lesson->title}' updated successfully!");
    }

    /**
     * Remove the specified lesson from storage.
     */
    public function destroy(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lessonTitle = $lesson->title;
        
        $this->lessonService->deleteLesson($lesson);

        return redirect()
            ->route('lessons.index')
            ->with('success', "Lesson '{$lessonTitle}' deleted successfully.");
    }

    /**
     * Mark lesson as completed for the authenticated user.
     */
    public function markAsComplete(MarkLessonCompleteRequest $request, string $id)
    {
        $lesson = Lesson::findOrFail($id);
        
        $this->lessonService->markLessonAsCompleted(auth()->user(), $lesson->id);
        
        // Check if this was the last lesson
        $nextLesson = $this->lessonService->getNextIncompleteLesson(auth()->user(), $lesson->course_id);
        
        $message = 'Lesson marked as complete!';
        
        if (!$nextLesson) {
            $message = '🎉 Congratulations! You have completed all lessons in this course!';
            
            // Check if this completed the entire course
            if ($this->hasCompletedCourse($lesson->course_id)) {
                $message = '🎓 Amazing! You have successfully completed the entire course!';
            }
        }

        // Get updated progress
        $progress = $this->lessonService->getCourseProgressPercentage(auth()->user(), $lesson->course_id);
        
        return back()->with([
            'success' => $message,
            'progress' => $progress,
            'nextLesson' => $nextLesson,
        ]);
    }

    /**
     * Get lessons by course.
     */
    public function byCourse(int $courseId)
    {
        $lessons = $this->lessonService->getLessonsByCourse($courseId);
        $course = Course::findOrFail($courseId);
        
        $viewData = [
            'lessons' => $lessons,
            'course' => $course,
            'totalLessons' => $this->getLessonCountForCourse($courseId),
        ];
        
        // Add user progress data if authenticated
        if (auth()->check()) {
            $viewData['completedLessons'] = $this->lessonService->getUserCompletedLessonsForCourse(
                auth()->user(), 
                $courseId
            );
            $viewData['progress'] = $this->lessonService->getCourseProgressPercentage(
                auth()->user(), 
                $courseId
            );
            $viewData['completionStatus'] = $this->getLessonsCompletionStatus($lessons);
        }
        
        return view('lessons.by-course', $viewData);
    }

    /**
     * Reorder lessons (AJAX endpoint)
     */
    public function reorder(Request $request, int $courseId)
    {
        $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'integer|min:1',
        ]);

        // This would need a method in your service to handle reordering
        // $this->lessonService->reorderLessons($courseId, $request->positions);

        return response()->json(['success' => true]);
    }

    /**
     * Get lesson preview (AJAX endpoint)
     */
    public function preview(int $id)
    {
        $lesson = Lesson::with('course')->findOrFail($id);
        
        return response()->json([
            'title' => $lesson->title,
            'content' => $lesson->content,
            'video_url' => $lesson->video_url,
            'course' => $lesson->course->title,
        ]);
    }
}