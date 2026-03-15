<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Models\Course;
use App\Services\PaymentService;
use App\Services\PaymentDataService;
use App\Traits\PaymentExportTrait;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Requests\Payments\UpdatePaymentRequest;
use App\Http\Requests\Payments\FilterPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use PaymentExportTrait;

    protected PaymentService $paymentService;
    protected PaymentDataService $paymentDataService;

    public function __construct(
        PaymentService $paymentService,
        PaymentDataService $paymentDataService
    ) {
        $this->paymentService = $paymentService;
        $this->paymentDataService = $paymentDataService;
    }

    /**
     * Display a listing of the payments.
     */
    public function index(FilterPaymentRequest $request)
    {
        // Get paginated payments
        $payments = $this->paymentService->getPaginatedPayments($request);
        
        // Get basic statistics
        $paymentsCount = Payment::count();
        $totalRevenue = Payment::sum('amount');
        $completedCount = Payment::where('status', 'completed')->count();
        $pendingCount = Payment::where('status', 'pending')->count();
        $failedCount = Payment::where('status', 'failed')->count();
        $refundedCount = Payment::where('status', 'refunded')->count();
        
        // Calculate completed revenue and refunded amount
        $completedRevenue = Payment::where('status', 'completed')->sum('amount');
        $refundedAmount = Payment::where('status', 'refunded')->sum('amount');
        
        // Get payment methods breakdown
        $paymentMethodsBreakdown = DB::table('payments')
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method')
            ->toArray();
        
        // Get users and courses for filters
        $users = User::all();
        $courses = Course::all();
        
        // Prepare data for view
        return view('payments.index', [
            'payments' => $payments,
            'users' => $users,
            'courses' => $courses,
            'statuses' => Payment::STATUSES,
            'methods' => Payment::METHODS,
            
            // Statistics
            'paymentsCount' => $paymentsCount,
            'totalRevenue' => $totalRevenue,
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
            'refundedCount' => $refundedCount,
            'completedRevenue' => $completedRevenue,
            'refundedAmount' => $refundedAmount,
            
            // Distributions
            'paymentMethodsBreakdown' => $paymentMethodsBreakdown,
            
            // Additional data your view might expect
            'latestPayment' => Payment::with(['user', 'course'])->latest()->first(),
            'averagePayment' => $paymentsCount > 0 ? $totalRevenue / $paymentsCount : 0,
            
            // Keep filter values
            'currentStatus' => $request->status,
            'currentMethod' => $request->payment_method,
            'currentUserId' => $request->user_id,
            'currentCourseId' => $request->course_id,
            'currentSearch' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $users = User::all();
        $courses = Course::all();
        $statuses = Payment::STATUSES;
        $methods = Payment::METHODS;
        
        return view('payments.create', compact('users', 'courses', 'statuses', 'methods'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $this->paymentService->createPayment($request->validated());

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment created successfully!');
    }

    /**
     * Display the specified payment.
     */
    public function show(string $id)
    {
        $payment = $this->paymentService->getPaymentWithRelations((int) $id);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(string $id)
    {
        $payment = $this->paymentService->getPaymentWithRelations((int) $id);
        $users = User::all();
        $courses = Course::all();
        $statuses = Payment::STATUSES;
        $methods = Payment::METHODS;
        
        return view('payments.edit', compact('payment', 'users', 'courses', 'statuses', 'methods'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(UpdatePaymentRequest $request, string $id)
    {
        $payment = Payment::findOrFail((int) $id);
        $this->paymentService->updatePayment($payment, $request->validated());

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail((int) $id);
        $this->paymentService->deletePayment($payment);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
    
    /**
     * Update payment status via AJAX
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Payment::STATUSES)
        ]);
        
        $payment = Payment::findOrFail((int) $id);
        $payment->update(['status' => $request->status]);
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }
        
        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }
    
    /**
     * Send receipt via AJAX
     */
    public function sendReceipt(Request $request, string $id)
    {
        $payment = Payment::with('user')->findOrFail((int) $id);
        
        // Add your receipt sending logic here
        // Mail::to($payment->user->email)->send(new PaymentReceipt($payment));
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Receipt sent successfully']);
        }
        
        return redirect()->back()->with('success', 'Receipt sent successfully!');
    }
    
    /**
     * Export payments to CSV
     */
    public function export(FilterPaymentRequest $request)
    {
        $payments = $this->paymentService->buildFilteredQuery($request)->get();
        
        return $this->exportPaymentsToCsv($payments);
    }
    
    /**
     * Get monthly revenue data for chart
     */
    public function monthlyRevenue()
    {
        $monthlyData = DB::table('payments')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
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
        
        return response()->json([
            'months' => $monthlyData->pluck('month'),
            'revenues' => $monthlyData->pluck('revenue')
        ]);
    }
}