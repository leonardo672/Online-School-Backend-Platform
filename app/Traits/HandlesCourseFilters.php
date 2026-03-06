<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesCourseFilters
{
    /**
     * Extract filters from request.
     */
    protected function extractFilters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'level' => $request->input('level'),
            'per_page' => $request->input('per_page', 10),
        ];
    }

    /**
     * Build filter options for view.
     */
    protected function getFilterOptions(): array
    {
        return [
            'levels' => \App\Models\Course::LEVELS,
            'categories' => \App\Models\Category::all(),
        ];
    }
}