<?php
// app/Http/Requests/Lesson/MarkLessonCompleteRequest.php

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class MarkLessonCompleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lesson_id' => 'sometimes|exists:lessons,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lesson_id.exists' => 'The specified lesson does not exist.',
        ];
    }
}