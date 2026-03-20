@extends('layout')

@section('content')

<div class="card shadow-lg rounded card-bg">
    <div class="card-header header-bg">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-certificate"></i> Issue New Certificate</h2>
            <a href="{{ url('/certificates') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ url('/certificates') }}" id="certificateForm">
            @csrf

            <div class="row">
                <!-- LEFT -->
                <div class="col-md-8">

                    {{-- USER --}}
                    <div class="mb-4">
                        <label class="form-label">User *</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- COURSE --}}
                    <div class="mb-4">
                        <label class="form-label">Course *</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}"
                                    data-title="{{ $course->title }}">
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CODE --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label>Certificate Code *</label>
                            <button type="button" id="generateCodeBtn" class="btn btn-sm btn-outline-primary">
                                Generate
                            </button>
                        </div>

                        <input type="text" name="certificate_code" id="certificate_code"
                            class="form-control mt-2" required>

                        <div id="codeAvailability" class="mt-2">
                            <span id="availabilityBadge" class="badge bg-secondary">Checking...</span>
                        </div>
                    </div>

                    {{-- DATE --}}
                    <div class="mb-4">
                        <label>Issued Date</label>
                        <div class="input-group">
                            <input type="datetime-local" name="issued_at" id="issued_at"
                                class="form-control">
                            <button type="button" id="setNowBtn" class="btn btn-outline-secondary">Now</button>
                        </div>
                    </div>

                    {{-- EXPIRY --}}
                    <div class="mb-4">
                        <label>Expiry</label>

                        <div class="form-check mb-2">
                            <input type="checkbox" id="noExpiry" class="form-check-input">
                            <label>No Expiry</label>
                        </div>

                        <input type="datetime-local" name="expires_at" id="expires_at"
                            class="form-control mb-2">

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary expiry-btn" data-days="30">30d</button>
                            <button type="button" class="btn btn-outline-secondary expiry-btn" data-days="90">90d</button>
                            <button type="button" class="btn btn-outline-secondary expiry-btn" data-days="365">1y</button>
                        </div>
                    </div>

                    {{-- PREVIEW --}}
                    <div class="card mb-4">
                        <div class="card-body">

                            <h4 class="text-center mb-4">Preview</h4>

                            <div class="certificate-preview text-center">

                                <div class="certificate-inner">

                                    <!-- Title -->
                                    <div class="cert-title">
                                        <h2>CERTIFICATE</h2>
                                        <h4>OF COMPLETION</h4>
                                    </div>

                                    <!-- Subtitle -->
                                    <p class="cert-subtitle">This is proudly presented to</p>

                                    <!-- Name -->
                                    <h1 id="previewUserName" class="cert-name">
                                        User Name
                                    </h1>

                                    <!-- Course -->
                                    <p class="cert-subtitle">for successfully completing</p>

                                    <h3 id="previewCourseName" class="cert-course">
                                        Course Name
                                    </h3>

                                    <!-- Divider -->
                                    <div class="cert-divider"></div>

                                    <!-- Details -->
                                    <div class="cert-details">
                                        <div>
                                            <span>Certificate Code</span>
                                            <strong id="previewCertCode">CODE</strong>
                                        </div>

                                        <div>
                                            <span>Issued On</span>
                                            <strong id="previewIssuedDate">DATE</strong>
                                        </div>
                                    </div>

                                    <!-- Signature -->
                                    <div class="cert-signature">
                                        <div class="line"></div>
                                        <p>Authorized Signature</p>
                                    </div>

                                </div>

                            </div>

                            <div class="text-center mt-3">
                                <button type="button" id="refreshPreviewBtn" class="btn btn-outline-info">
                                    Refresh Preview
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="d-flex justify-content-between">
                        <button type="button" id="resetFormBtn" class="btn btn-warning">Reset</button>

                        <div>
                            <button type="button" id="saveDraftBtn" class="btn btn-secondary">Draft</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-md-4">

                    {{-- USER STATS --}}
                    <div id="userCertificateStats" style="display:none;">
                        <h6>User Stats</h6>
                        <p>Total: <span id="userTotalCertificates">0</span></p>
                        <p>Valid: <span id="userValidCertificates">0</span></p>
                        <p>Expired: <span id="userExpiredCertificates">0</span></p>
                    </div>

                    {{-- COURSE STATS --}}
                    <div id="courseCertificateStats" style="display:none;">
                        <h6>Course Stats</h6>
                        <p>Total: <span id="courseTotalCertificates">0</span></p>
                        <p>Valid: <span id="courseValidCertificates">0</span></p>
                        <p>Expired: <span id="courseExpiredCertificates">0</span></p>
                    </div>

                    {{-- DUPLICATE --}}
                    <button type="button" id="checkDuplicateBtn" class="btn btn-warning w-100 mt-3">
                        Check Duplicate
                    </button>

                </div>
            </div>

        </form>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="duplicateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Duplicate Found</h5>
            </div>
            <div class="modal-body">
                <p id="duplicateCertCode"></p>
                <p id="duplicateIssuedDate"></p>
                <p id="duplicateStatus"></p>
            </div>
            <div class="modal-footer">
                <button id="proceedDuplicateBtn" class="btn btn-warning">Proceed</button>
            </div>
        </div>
    </div>
</div>

@endsection

@vite(['resources/js/pages/certificates/create.js'])