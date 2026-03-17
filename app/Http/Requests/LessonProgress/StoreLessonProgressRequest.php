<?php
// app/Http/Requests/LessonProgress/StoreLessonProgressRequest.php

namespace App\Http\Requests\LessonProgress;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonProgressRequest extends FormRequest
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

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user is invalid.',
            'lesson_id.required' => 'Please select a lesson.',
            'lesson_id.exists' => 'The selected lesson is invalid.',
        ];
    }
}