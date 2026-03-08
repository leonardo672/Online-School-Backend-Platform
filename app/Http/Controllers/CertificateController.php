<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Services\CertificateService;
use App\Traits\CertificateStatistics;
use App\Http\Requests\StoreCertificateRequest;
use App\Http\Requests\UpdateCertificateRequest;

class CertificateController extends Controller
{
    use CertificateStatistics;

    // Inject the service via constructor
    public function __construct(private CertificateService $service) {}

    /**
     * Display a listing of certificates with filters and statistics.
     */
    public function index(Request $request)
    {
        // Get filtered & paginated certificates
        $certificates = $this->service->getFilteredCertificates($request);

        // Get status counts via Trait
        $statusCounts = $this->certificateStatusCounts();

        // Most certified course via Service
        [$mostCourse, $mostCount] = $this->service->mostCertifiedCourse();

        // Latest certificate
        $latestCertificate = Certificate::with(['user', 'course'])->latest()->first();

        // Additional data for filters
        $users = User::all();
        $courses = Course::all();

        return view('certificates.index', compact(
            'certificates',
            'statusCounts',
            'mostCourse',
            'mostCount',
            'latestCertificate',  
            'users',
            'courses'
        ));
    }


    /**
     * Show form for creating a certificate.
     */
    public function create()
    {
        $users = User::all();
        $courses = Course::all();
        return view('certificates.create', compact('users', 'courses'));
    }

    /**
     * Store a newly created certificate.
     */
    public function store(StoreCertificateRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('certificates.index')
            ->with('success', 'Certificate created successfully!');
    }

    /**
     * Display a specific certificate.
     */
/**
 * Display a specific certificate.
 */
/**
 * Display a specific certificate.
 */
/**
 * Display a specific certificate.
 */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'course']);

        // --- Status flags ---
        $isExpired =
            $certificate->expires_at !== null
            && $certificate->expires_at->isPast();

        $isExpiringSoon =
            $certificate->expires_at !== null
            && $certificate->expires_at->isFuture()
            && $certificate->expires_at <= now()->addDays(30);

        // --- Status presentation ---
        [$certificateStatusColor, $certificateStatusIcon, $certificateStatusText] = match (true) {
            $certificate->issued_at === null
                || $certificate->issued_at > now()
                => ['secondary', 'fa-clock', 'Pending'],

            $isExpired
                => ['danger', 'fa-times-circle', 'Expired'],

            $isExpiringSoon
                => ['warning', 'fa-exclamation-triangle', 'Expiring Soon'],

            default
                => ['success', 'fa-check-circle', 'Valid'],
        };

        // --- User valid certificates count ---
        $userValidCertificatesCount = $certificate->user
            ->certificates()
            ->where(function ($q) {
                $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
            })
            ->count();

        // --- Course average rating (if ratings exist) ---
        $courseAverageRating = method_exists($certificate->course, 'ratings')
            ? $certificate->course->ratings()->avg('rating') ?? 0
            : 0;

        // --- Related certificates for same course ---
        $relatedCertificates = Certificate::with('user')
            ->where('course_id', $certificate->course_id)
            ->where('id', '<>', $certificate->id)
            ->latest()
            ->take(5)
            ->get();

        return view('certificates.show', compact(
            'certificate',
            'certificateStatusColor',
            'certificateStatusIcon',
            'certificateStatusText',
            'isExpired',
            'isExpiringSoon',
            'userValidCertificatesCount',
            'courseAverageRating',
            'relatedCertificates'
        ));
    }


    /**
     * Show form for editing a certificate.
     */
    public function edit(Certificate $certificate)
    {
        $certificate->load(['user', 'course', 'creator', 'updater']); // preload relationships
        $userCertificatesCount = $certificate->user->certificates()->count();
        $courseCertificatesCount = $certificate->course->certificates()->count();

        // Pre-calculate status variables
        $now = now();
        $isExpired = $certificate->expires_at?->isPast() ?? false;
        $isExpiringSoon = $certificate->expires_at && $certificate->expires_at->diffInDays($now, false) <= 30;
        $certificateStatusColor = $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'success');
        $certificateStatusText = $isExpired ? 'Expired' : ($isExpiringSoon ? 'Expiring Soon' : 'Active');
        $certificateStatusIcon = $isExpired ? 'fa-times-circle' : ($isExpiringSoon ? 'fa-exclamation-triangle' : 'fa-check-circle');

        return view('certificates.edit', compact(
            'certificate', 
            'userCertificatesCount', 
            'courseCertificatesCount', 
            'certificateStatusColor', 
            'certificateStatusText', 
            'certificateStatusIcon'
        ));
    }


    /**
     * Update a certificate.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $request->validate([
            'issued_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:issued_at',
            'update_reason' => 'nullable|string|max:500',
            'send_update_email' => 'nullable|boolean',
            'generate_new_pdf' => 'nullable|boolean',
            'add_to_audit_log' => 'nullable|boolean',
        ]);

        // Save updates
        $certificate->update([
            'issued_at' => $request->issued_at,
            'expires_at' => $request->expires_at,
        ]);

        // Optional: regenerate PDF
        if ($request->filled('generate_new_pdf')) {
            // $certificate->generatePDF();
        }

        // Optional: send notification
        if ($request->filled('send_update_email')) {
            // Mail::to($certificate->user->email)->send(new CertificateUpdated($certificate));
        }

        return redirect()->route('certificates.show', $certificate->id)
            ->with('success', 'Certificate updated successfully!');
    }


    /**
     * Delete a certificate.
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Dashboard statistics.
     */
    public function dashboard()
    {
        $totalCertificates = Certificate::count();
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $statusCounts = $this->certificateStatusCounts();
        [$mostCourse, $mostCount] = $this->service->mostCertifiedCourse();

        $recentCertificates = Certificate::with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();

        // Course distribution
        $courseDistribution = DB::table('courses')
            ->leftJoin('certificates', 'courses.id', '=', 'certificates.course_id')
            ->select('courses.*', DB::raw('COUNT(certificates.id) as certificates_count'))
            ->groupBy('courses.id')
            ->orderByDesc('certificates_count')
            ->get();

        // Monthly trends (last 12 months)
        $monthlyTrends = Certificate::selectRaw(
                "TO_CHAR(issued_at, 'YYYY-MM') as month, COUNT(*) as count"
            )
            ->whereNotNull('issued_at')
            ->where('issued_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('certificates.dashboard', compact(
            'totalCertificates',
            'totalUsers',
            'totalCourses',
            'statusCounts',
            'mostCourse',
            'mostCount',
            'recentCertificates',
            'courseDistribution',
            'monthlyTrends'
        ));
    }
}
