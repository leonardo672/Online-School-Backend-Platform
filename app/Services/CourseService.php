<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log; // Add for logging

class CourseService
{
    /**
     * Get filtered courses with pagination.
     */
    public function getFilteredCourses(array $filters = []): LengthAwarePaginator
    {
        return Course::with(['category', 'instructor'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'ilike', '%' . $search . '%') // PostgreSQL uses ILIKE for case-insensitive
                      ->orWhere('description', 'ilike', '%' . $search . '%');
                });
            })
            ->when($filters['category'] ?? null, function ($query, $category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->when($filters['level'] ?? null, function ($query, $level) {
                $query->where('level', $level);
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get course statistics.
     */
    public function getCourseStatistics(): array
    {
        return [
            'total' => Course::count(),
            'published' => Course::where('published', true)->count(),
            'draft' => Course::where('published', false)->count(),
            'free' => Course::where('price', 0)->count(),
            'by_level' => $this->getCoursesCountByLevel(),
            'by_category' => $this->getCoursesCountByCategory(),
        ];
    }

    /**
     * Create a new course.
     */
    public function createCourse(array $data): Course
    {
        // FIXED: Handle published field properly
        $data['published'] = $data['published'] ?? false;
        
        // Generate unique slug
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        
        // Log the data for debugging
        Log::info('CourseService: Creating course', $data);
        
        return Course::create($data);
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(Course $course, array $data): Course
    {
        // FIXED: Handle published field properly
        $data['published'] = $data['published'] ?? false;
        
        // Only update slug if title changed
        if (isset($data['title']) && $data['title'] !== $course->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }
        
        Log::info('CourseService: Updating course', ['id' => $course->id, 'data' => $data]);
        
        $course->update($data);
        
        return $course->fresh();
    }

    /**
     * Delete a course.
     */
    public function deleteCourse(Course $course): bool
    {
        Log::info('CourseService: Deleting course', ['id' => $course->id]);
        
        // Add any pre-deletion logic here (check enrollments, etc.)
        // You might want to check if there are enrollments before deleting
        
        return $course->delete();
    }

    /**
     * Get data for course forms (create/edit).
     */
    public function getFormData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'instructors' => User::where('role', 'instructor')->orderBy('name')->get(),
            'levels' => Course::LEVELS,
        ];
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Course::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Get courses count by level.
     */
    private function getCoursesCountByLevel(): array
    {
        $stats = [];
        foreach (Course::LEVELS as $level) {
            $stats[$level] = Course::where('level', $level)->count();
        }
        return $stats;
    }

    /**
     * Get courses count by category.
     * Using collection filtering - works with all databases including PostgreSQL
     */
    private function getCoursesCountByCategory(): array
    {
        return Category::withCount('courses')
            ->get()
            ->filter(function ($category) {
                return $category->courses_count > 0;
            })
            ->pluck('courses_count', 'name')
            ->toArray();
    }

    /**
     * Get a single course with all relationships
     */
    public function getCourseWithDetails(int $id): ?Course
    {
        return Course::with(['category', 'instructor'])
            ->withCount(['enrollments', 'lessons', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->find($id);
    }
}