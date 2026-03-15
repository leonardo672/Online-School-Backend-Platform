<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait PaymentExportTrait
{
    protected function exportPaymentsToCsv(Collection $payments, string $filename = null): StreamedResponse
    {
        $filename = $filename ?? 'payments_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $columns = ['ID', 'User', 'Course', 'Amount', 'Status', 'Payment Method', 'Transaction ID', 'Created At'];
        
        $callback = function() use($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user->name ?? 'N/A',
                    $payment->course->title ?? 'N/A',
                    number_format($payment->amount, 2),
                    ucfirst($payment->status),
                    ucfirst($payment->payment_method),
                    $payment->transaction_id,
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}