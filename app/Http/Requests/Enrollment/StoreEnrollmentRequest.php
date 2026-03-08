<?php
// app/Http/Requests/Enrollment/StoreEnrollmentRequest.php

namespace App\Http\Requests\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'enrolled_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user is invalid.',
            'course_id.required' => 'Please select a course.',
            'course_id.exists' => 'The selected course is invalid.',
            'enrolled_at.required' => 'Please select an enrollment date.',
            'enrolled_at.date' => 'Please select a valid date.',
        ];
    }
}