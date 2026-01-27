<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateService
{
    public function getFilteredCertificates(Request $request)
    {
        return Certificate::with(['user', 'course'])
            ->when($request->filled('user_id'), fn ($q) =>
                $q->where('user_id', $request->user_id)
            )
            ->when($request->filled('course_id'), fn ($q) =>
                $q->where('course_id', $request->course_id)
            )
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%")
                )->orWhereHas('course', fn ($c) =>
                    $c->where('title', 'like', "%{$request->search}%")
                );
            })
            ->latest()
            ->paginate(20)
            ->appends($request->query());
    }

    public function create(array $data): Certificate
    {
        return Certificate::create([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'certificate_code' => $data['certificate_code'],
            'issued_at' => now(),
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    public function update(Certificate $certificate, array $data): Certificate
    {
        $certificate->update($data);
        return $certificate;
    }

    public function mostCertifiedCourse(): array
    {
        $data = Certificate::select('course_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('course_id')
            ->orderByDesc('count')
            ->first();

        return $data
            ? [Course::find($data->course_id), $data->count]
            : [null, 0];
    }
}
