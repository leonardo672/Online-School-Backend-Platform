<?php

namespace App\Traits;

use App\Models\Certificate;

trait CertificateStatistics
{
    public function certificateStatusCounts(): array
    {
        return [
            'valid' => Certificate::where(fn ($q) =>
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now())
            )->whereNotNull('issued_at')->count(),

            'expired' => Certificate::whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->count(),

            'expiringSoon' => Certificate::whereNotNull('expires_at')
                ->whereBetween('expires_at', [now(), now()->addDays(30)])
                ->count(),

            'notIssued' => Certificate::whereNull('issued_at')
                ->orWhere('issued_at', '>', now())
                ->count(),
        ];
    }
}
