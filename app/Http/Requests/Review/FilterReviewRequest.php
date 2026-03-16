<?php
// app/Http/Requests/Review/FilterReviewRequest.php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class FilterReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'approved' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:newest,oldest,highest,lowest'],
        ];
    }
}