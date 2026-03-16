<?php
// app/Http/Requests/Review/BulkActionRequest.php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review_ids' => ['required', 'array'],
            'review_ids.*' => ['exists:reviews,id'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}