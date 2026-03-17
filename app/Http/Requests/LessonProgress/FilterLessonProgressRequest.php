<?php
// app/Http/Requests/LessonProgress/FilterLessonProgressRequest.php

namespace App\Http\Requests\LessonProgress;

use Illuminate\Foundation\Http\FormRequest;

class FilterLessonProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'status' => ['nullable', 'string', 'in:completed,incomplete'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}