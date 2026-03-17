<?php
// app/Http/Requests/LessonProgress/UpdateLessonProgressRequest.php

namespace App\Http\Requests\LessonProgress;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'lesson_id' => ['required', 'exists:lessons,id'],
            'completed' => ['nullable', 'boolean'],
        ];
    }
}