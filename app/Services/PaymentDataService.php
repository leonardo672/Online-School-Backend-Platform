<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Payment;
use App\Traits\PaymentStatisticsTrait;
use Illuminate\Http\Request;

class PaymentDataService
{
    use PaymentStatisticsTrait;

    public function getDashboardData(?Request $request = null): array
    {
        $data = [
            'statistics' => $this->getPaymentStatistics(),
            'statusDistribution' => $this->getStatusDistribution(),
            'methodDistribution' => $this->getMethodDistribution(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
            'topUsers' => $this->getTopUsers(),
            'topCourses' => $this->getTopCourses(),
            'recentPayments' => $this->getRecentPayments(),
        ];

        // If request is provided, add filter-related data
        if ($request) {
            $data['users'] = User::all();
            $data['courses'] = Course::all();
            $data['statuses'] = Payment::STATUSES;
            $data['methods'] = Payment::METHODS;
        }

        return $data;
    }

    public function getFormData(): array
    {
        return [
            'users' => User::all(),
            'courses' => Course::all(),
            'statuses' => Payment::STATUSES,
            'methods' => Payment::METHODS,
        ];
    }
}