<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Course;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'instructor_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:' . implode(',', Course::LEVELS),
            'published' => 'boolean',
        ];

        // Add unique rule only for store (POST)
        if ($this->isMethod('post')) {
            // FIXED: Convert to array and add unique rule
            $rules['title'] = ['required', 'string', 'max:255', 'unique:courses,title'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A course title is required.',
            'title.unique' => 'This course title already exists.',
            'category_id.required' => 'Please select a category.',
            'instructor_id.required' => 'Please select an instructor.',
            'level.in' => 'Please select a valid course level.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'published' => $this->boolean('published'),
        ]);
    }
}