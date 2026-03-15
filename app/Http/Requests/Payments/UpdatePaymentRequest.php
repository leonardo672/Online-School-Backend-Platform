<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Payment;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', Payment::STATUSES),
            'payment_method' => 'required|in:' . implode(',', Payment::METHODS),
            'transaction_id' => 'required|string|unique:payments,transaction_id,' . $this->payment,
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user is invalid.',
            'course_id.exists' => 'The selected course is invalid.',
            'amount.required' => 'Please enter the payment amount.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status must be one of: ' . implode(', ', Payment::STATUSES),
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method must be one of: ' . implode(', ', Payment::METHODS),
            'transaction_id.required' => 'Please enter the transaction ID.',
            'transaction_id.unique' => 'This transaction ID already exists.',
        ];
    }
}