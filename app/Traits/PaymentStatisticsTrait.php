<?php

namespace App\Traits;

use App\Models\Payment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

trait PaymentStatisticsTrait
{
    protected function getPaymentStatistics(): array
    {
        $paymentsCount = Payment::count();
        $totalRevenue = Payment::sum('amount');
        
        return [
            'paymentsCount' => $paymentsCount,
            'totalRevenue' => $totalRevenue,
            'completedPayments' => Payment::where('status', 'completed')->count(),
            'pendingPayments' => Payment::where('status', 'pending')->count(),
            'failedPayments' => Payment::where('status', 'failed')->count(),
            'refundedPayments' => Payment::where('status', 'refunded')->count(),
            'latestPayment' => Payment::with(['user', 'course'])->latest()->first(),
            'averagePayment' => $paymentsCount > 0 ? $totalRevenue / $paymentsCount : 0,
        ];
    }

    protected function getStatusDistribution()
    {
        return DB::table('payments')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }

    protected function getMethodDistribution()
    {
        return DB::table('payments')
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method');
    }

    protected function getMonthlyRevenue(int $months = 6)
    {
        return DB::table('payments')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths($months))
            ->selectRaw("EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year, SUM(amount) as revenue")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'revenue' => $item->revenue
                ];
            });
    }

    protected function getTopUsers(int $limit = 5)
    {
        return User::select('users.*')
            ->selectRaw('COUNT(payments.id) as payment_count')
            ->selectRaw('SUM(payments.amount) as total_spent')
            ->leftJoin('payments', 'users.id', '=', 'payments.user_id')
            ->groupBy('users.id')
            ->orderByDesc('total_spent')
            ->take($limit)
            ->get();
    }

    protected function getTopCourses(int $limit = 5)
    {
        return Course::select('courses.*')
            ->selectRaw('COUNT(payments.id) as payment_count')
            ->selectRaw('SUM(payments.amount) as total_revenue')
            ->leftJoin('payments', 'courses.id', '=', 'payments.course_id')
            ->where('payments.status', 'completed') // Only count completed payments for revenue
            ->groupBy('courses.id')
            ->orderByDesc('total_revenue')
            ->take($limit)
            ->get();
    }

    protected function getRecentPayments(int $limit = 5)
    {
        return Payment::with(['user', 'course'])
            ->latest()
            ->take($limit)
            ->get();
    }
}