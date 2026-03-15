<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Payment;

class FilterPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:' . implode(',', Payment::STATUSES),
            'payment_method' => 'nullable|in:' . implode(',', Payment::METHODS),
            'user_id' => 'nullable|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'search' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
}