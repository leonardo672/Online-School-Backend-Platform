<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Build payment query with filters
     */
    public function buildFilteredQuery(Request $request): Builder
    {
        return Payment::with(['user', 'course'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('payment_method'), function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->filled('course_id'), function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->whereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    })->orWhereHas('course', function ($courseQuery) use ($request) {
                        $courseQuery->where('title', 'like', '%' . $request->search . '%');
                    })->orWhere('transaction_id', 'like', '%' . $request->search . '%');
                });
            })
            ->latest();
    }

    /**
     * Get paginated payments with filters
     */
    public function getPaginatedPayments(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        return $this->buildFilteredQuery($request)
            ->paginate($perPage)
            ->appends($request->query());
    }

    /**
     * Create a new payment with sequence fix if needed
     */
    public function createPayment(array $data): Payment
    {
        try {
            return Payment::create([
                'user_id' => $data['user_id'],
                'course_id' => $data['course_id'] ?? null,
                'amount' => $data['amount'],
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'],
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // Fix the sequence and try again
            $this->fixSequence();
            
            return Payment::create([
                'user_id' => $data['user_id'],
                'course_id' => $data['course_id'] ?? null,
                'amount' => $data['amount'],
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'],
            ]);
        }
    }

    /**
     * Fix PostgreSQL sequence
     */
    protected function fixSequence(): void
    {
        DB::statement("SELECT setval('payments_id_seq', (SELECT MAX(id) FROM payments))");
    }

    /**
     * Update an existing payment
     */
    public function updatePayment(Payment $payment, array $data): bool
    {
        return $payment->update([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'] ?? null,
            'amount' => $data['amount'],
            'status' => $data['status'],
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'],
        ]);
    }

    /**
     * Delete a payment
     */
    public function deletePayment(Payment $payment): bool
    {
        return $payment->delete();
    }

    /**
     * Get payment by ID with relationships
     */
    public function getPaymentWithRelations(int $id): Payment
    {
        return Payment::with(['user', 'course'])->findOrFail($id);
    }
}