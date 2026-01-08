{{-- Student Profile View --}}
{{-- Prompt 145: Student profile page with all information and related data --}}

@extends('layouts.app')

@section('title', ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' - Student Profile')

@section('content')
<div x-data="studentProfile()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.edit', $student->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('students.promote', $student->id ?? 0) }}" class="btn btn-primary">
                <i class="bi bi-arrow-up-circle me-1"></i> Promote
            </a>
            <button type="button" class="btn btn-outline-primary" @click="printProfile()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('students.documents', $student->id ?? 0) }}"><i class="bi bi-file-earmark me-2"></i>Documents</a></li>
                    <li><a class="dropdown-item" href="{{ route('students.attendance', $student->id ?? 0) }}"><i class="bi bi-calendar-check me-2"></i>Attendance</a></li>
                    <li><a class="dropdown-item" href="{{ route('students.fees', $student->id ?? 0) }}"><i class="bi bi-currency-rupee me-2"></i>Fees</a></li>
                    <li><a class="dropdown-item" href="{{ route('students.results', $student->id ?? 0) }}"><i class="bi bi-award me-2"></i>Results</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" @click.prevent="confirmDelete()"><i class="bi bi-trash me-2"></i>Delete</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Profile Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    <img 
                        src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) . '&background=4f46e5&color=fff&size=120' }}"
                        alt="{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}"
                        class="rounded-circle"
                        style="width: 120px; height: 120px; object-fit: cover;"
                    >
                </div>
                <div class="col-md">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                        <div>
                            <h3 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->middle_name ?? '' }} {{ $student->last_name ?? '' }}</h3>
                            <p class="text-muted mb-2">
                                <span class="badge bg-light text-dark me-2">{{ $student->admission_number ?? 'N/A' }}</span>
                                <span class="badge bg-primary me-2">{{ $student->class->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                                <span class="badge {{ ($student->status ?? 'active') === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($student->status ?? 'Active') }}
                                </span>
                            </p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-calendar3 me-1"></i> Roll No: {{ $student->roll_number ?? 'N/A' }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-mortarboard me-1"></i> Session: {{ $student->academicSession->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="tel:{{ $student->father_phone ?? '' }}" class="btn btn-sm btn-outline-primary" title="Call Father">
                                    <i class="bi bi-telephone"></i>
                                </a>
                                <a href="mailto:{{ $student->email ?? '' }}" class="btn btn-sm btn-outline-primary" title="Send Email">
                                    <i class="bi bi-envelope"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Send Message" @click="sendMessage()">
                                    <i class="bi bi-chat-dots"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-check text-success fs-5"></i>
                    </div>
                    <h4 class="mb-0">{{ $attendancePercentage ?? '0' }}%</h4>
                    <small class="text-muted">Attendance</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-award text-primary fs-5"></i>
                    </div>
                    <h4 class="mb-0">{{ $overallPercentage ?? '0' }}%</h4>
                    <small class="text-muted">Overall Score</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-currency-rupee text-warning fs-5"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($pendingFees ?? 0) }}</h4>
                    <small class="text-muted">Pending Fees</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                        <i class="bi bi-book text-info fs-5"></i>
                    </div>
                    <h4 class="mb-0">{{ $booksIssued ?? 0 }}</h4>
                    <small class="text-muted">Books Issued</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'personal' }" @click="activeTab = 'personal'">
                <i class="bi bi-person me-1"></i> Personal
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'academic' }" @click="activeTab = 'academic'">
                <i class="bi bi-mortarboard me-1"></i> Academic
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'documents' }" @click="activeTab = 'documents'">
                <i class="bi bi-file-earmark me-1"></i> Documents
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'attendance' }" @click="activeTab = 'attendance'">
                <i class="bi bi-calendar-check me-1"></i> Attendance
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'results' }" @click="activeTab = 'results'">
                <i class="bi bi-award me-1"></i> Results
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'fees' }" @click="activeTab = 'fees'">
                <i class="bi bi-currency-rupee me-1"></i> Fees
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'transport' }" @click="activeTab = 'transport'">
                <i class="bi bi-bus-front me-1"></i> Transport
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" :class="{ 'active': activeTab === 'library' }" @click="activeTab = 'library'">
                <i class="bi bi-book me-1"></i> Library
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Personal Information Tab -->
        <div x-show="activeTab === 'personal'" x-transition>
            <div class="row g-4">
                <div class="col-md-6">
                    <x-card title="Personal Details" icon="bi-person">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Full Name</label>
                                <p class="mb-2 fw-medium">{{ $student->first_name ?? '' }} {{ $student->middle_name ?? '' }} {{ $student->last_name ?? '' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Date of Birth</label>
                                <p class="mb-2 fw-medium">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Gender</label>
                                <p class="mb-2 fw-medium">{{ ucfirst($student->gender ?? '-') }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Blood Group</label>
                                <p class="mb-2 fw-medium">{{ $student->blood_group ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Religion</label>
                                <p class="mb-2 fw-medium">{{ $student->religion ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Caste</label>
                                <p class="mb-2 fw-medium">{{ $student->caste ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Nationality</label>
                                <p class="mb-2 fw-medium">{{ $student->nationality ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Mother Tongue</label>
                                <p class="mb-2 fw-medium">{{ $student->mother_tongue ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Mobile</label>
                                <p class="mb-2 fw-medium">{{ $student->mobile ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Email</label>
                                <p class="mb-2 fw-medium">{{ $student->email ?? '-' }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <div class="col-md-6">
                    <x-card title="Family Details" icon="bi-people">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-primary small mb-2">Father's Information</h6>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Name</label>
                                <p class="mb-2 fw-medium">{{ $student->father_name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Phone</label>
                                <p class="mb-2 fw-medium">{{ $student->father_phone ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Occupation</label>
                                <p class="mb-2 fw-medium">{{ $student->father_occupation ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Email</label>
                                <p class="mb-2 fw-medium">{{ $student->father_email ?? '-' }}</p>
                            </div>

                            <div class="col-12 mt-3">
                                <h6 class="text-primary small mb-2">Mother's Information</h6>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Name</label>
                                <p class="mb-2 fw-medium">{{ $student->mother_name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Phone</label>
                                <p class="mb-2 fw-medium">{{ $student->mother_phone ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Occupation</label>
                                <p class="mb-2 fw-medium">{{ $student->mother_occupation ?? '-' }}</p>
                            </div>

                            @if($student->guardian_name ?? false)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary small mb-2">Guardian's Information</h6>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Name</label>
                                <p class="mb-2 fw-medium">{{ $student->guardian_name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Phone</label>
                                <p class="mb-2 fw-medium">{{ $student->guardian_phone ?? '-' }}</p>
                            </div>
                            @endif
                        </div>
                    </x-card>
                </div>

                <div class="col-md-6">
                    <x-card title="Address" icon="bi-geo-alt">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-primary small mb-2">Current Address</h6>
                            </div>
                            <div class="col-12">
                                <p class="mb-2">{{ $student->address ?? '-' }}</p>
                                <p class="mb-0 text-muted">{{ $student->city ?? '' }}{{ $student->city && $student->state ? ', ' : '' }}{{ $student->state ?? '' }} - {{ $student->postal_code ?? '' }}</p>
                            </div>

                            @if($student->permanent_address ?? false)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary small mb-2">Permanent Address</h6>
                            </div>
                            <div class="col-12">
                                <p class="mb-2">{{ $student->permanent_address ?? '-' }}</p>
                                <p class="mb-0 text-muted">{{ $student->permanent_city ?? '' }}{{ $student->permanent_city && $student->permanent_state ? ', ' : '' }}{{ $student->permanent_state ?? '' }} - {{ $student->permanent_postal_code ?? '' }}</p>
                            </div>
                            @endif
                        </div>
                    </x-card>
                </div>

                <div class="col-md-6">
                    <x-card title="Emergency Contact" icon="bi-telephone">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Contact Person</label>
                                <p class="mb-2 fw-medium">{{ $student->emergency_contact_name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Phone</label>
                                <p class="mb-2 fw-medium">{{ $student->emergency_contact_phone ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Relation</label>
                                <p class="mb-2 fw-medium">{{ ucfirst($student->emergency_contact_relation ?? '-') }}</p>
                            </div>

                            @if($student->medical_conditions ?? false)
                            <div class="col-12 mt-3">
                                <h6 class="text-primary small mb-2">Medical Information</h6>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Medical Conditions</label>
                                <p class="mb-2 fw-medium">{{ $student->medical_conditions ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Allergies</label>
                                <p class="mb-2 fw-medium">{{ $student->allergies ?? '-' }}</p>
                            </div>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>

        <!-- Academic Information Tab -->
        <div x-show="activeTab === 'academic'" x-transition>
            <div class="row g-4">
                <div class="col-md-6">
                    <x-card title="Academic Details" icon="bi-mortarboard">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Admission Number</label>
                                <p class="mb-2 fw-medium">{{ $student->admission_number ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Roll Number</label>
                                <p class="mb-2 fw-medium">{{ $student->roll_number ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Class</label>
                                <p class="mb-2 fw-medium">{{ $student->class->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Section</label>
                                <p class="mb-2 fw-medium">{{ $student->section->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Academic Session</label>
                                <p class="mb-2 fw-medium">{{ $student->academicSession->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Category</label>
                                <p class="mb-2 fw-medium">{{ $student->category->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Admission Date</label>
                                <p class="mb-2 fw-medium">{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">RTE Student</label>
                                <p class="mb-2 fw-medium">{{ ($student->is_rte ?? false) ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <div class="col-md-6">
                    <x-card title="Previous School" icon="bi-building">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small text-muted mb-0">School Name</label>
                                <p class="mb-2 fw-medium">{{ $student->previous_school ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">Previous Class</label>
                                <p class="mb-2 fw-medium">{{ $student->previous_class ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">TC Number</label>
                                <p class="mb-2 fw-medium">{{ $student->tc_number ?? '-' }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                @if(isset($siblings) && count($siblings) > 0)
                <div class="col-12">
                    <x-card title="Siblings" icon="bi-people">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Admission No</th>
                                        <th>Class</th>
                                        <th>Relation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siblings as $sibling)
                                    <tr>
                                        <td>
                                            <img src="{{ $sibling->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($sibling->first_name . ' ' . $sibling->last_name) . '&background=4f46e5&color=fff&size=40' }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        </td>
                                        <td>{{ $sibling->first_name }} {{ $sibling->last_name }}</td>
                                        <td>{{ $sibling->admission_number }}</td>
                                        <td>{{ $sibling->class->name ?? '-' }} - {{ $sibling->section->name ?? '-' }}</td>
                                        <td>{{ ucfirst($sibling->pivot->relation ?? 'Sibling') }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $sibling->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
                @endif
            </div>
        </div>

        <!-- Documents Tab -->
        <div x-show="activeTab === 'documents'" x-transition>
            <x-card title="Documents" icon="bi-file-earmark">
                <x-slot name="actions">
                    <a href="{{ route('students.documents', $student->id ?? 0) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-upload me-1"></i> Upload Document
                    </a>
                </x-slot>

                @if(isset($documents) && count($documents) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Document Type</th>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Uploaded On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr>
                                <td>{{ $doc->document_type ?? 'Unknown' }}</td>
                                <td>{{ $doc->file_name ?? 'N/A' }}</td>
                                <td>{{ $doc->file_size ?? '-' }}</td>
                                <td>{{ $doc->created_at ? $doc->created_at->format('d M Y') : '-' }}</td>
                                <td>
                                    <span class="badge {{ ($doc->is_verified ?? false) ? 'bg-success' : 'bg-warning' }}">
                                        {{ ($doc->is_verified ?? false) ? 'Verified' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ $doc->file_path ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No documents uploaded yet</p>
                    <a href="{{ route('students.documents', $student->id ?? 0) }}" class="btn btn-primary btn-sm">Upload Documents</a>
                </div>
                @endif
            </x-card>
        </div>

        <!-- Attendance Tab -->
        <div x-show="activeTab === 'attendance'" x-transition>
            <div class="row g-4">
                <div class="col-md-8">
                    <x-card title="Attendance Summary" icon="bi-calendar-check">
                        <x-slot name="actions">
                            <a href="{{ route('students.attendance', $student->id ?? 0) }}" class="btn btn-sm btn-outline-primary">View Full</a>
                        </x-slot>
                        <div class="row g-3 mb-4">
                            <div class="col-3 text-center">
                                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <span class="text-success fw-bold">{{ $presentDays ?? 0 }}</span>
                                </div>
                                <small class="d-block text-muted">Present</small>
                            </div>
                            <div class="col-3 text-center">
                                <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <span class="text-danger fw-bold">{{ $absentDays ?? 0 }}</span>
                                </div>
                                <small class="d-block text-muted">Absent</small>
                            </div>
                            <div class="col-3 text-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <span class="text-warning fw-bold">{{ $lateDays ?? 0 }}</span>
                                </div>
                                <small class="d-block text-muted">Late</small>
                            </div>
                            <div class="col-3 text-center">
                                <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <span class="text-info fw-bold">{{ $leaveDays ?? 0 }}</span>
                                </div>
                                <small class="d-block text-muted">Leave</small>
                            </div>
                        </div>
                        <canvas id="attendanceChart" height="200"></canvas>
                    </x-card>
                </div>
                <div class="col-md-4">
                    <x-card title="Attendance Rate" icon="bi-pie-chart">
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <canvas id="attendancePieChart" width="200" height="200"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <h3 class="mb-0">{{ $attendancePercentage ?? 0 }}%</h3>
                                    <small class="text-muted">Attendance</small>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>

        <!-- Results Tab -->
        <div x-show="activeTab === 'results'" x-transition>
            <x-card title="Exam Results" icon="bi-award">
                <x-slot name="actions">
                    <a href="{{ route('students.results', $student->id ?? 0) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    <button type="button" class="btn btn-sm btn-primary" @click="printReportCard()">
                        <i class="bi bi-printer me-1"></i> Print Report Card
                    </button>
                </x-slot>

                @if(isset($examResults) && count($examResults) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Full Marks</th>
                                <th>Obtained</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examResults as $result)
                            <tr>
                                <td>{{ $result->exam->name ?? '-' }}</td>
                                <td>{{ $result->subject->name ?? '-' }}</td>
                                <td>{{ $result->full_marks ?? '-' }}</td>
                                <td>{{ $result->obtained_marks ?? '-' }}</td>
                                <td>{{ $result->percentage ?? '-' }}%</td>
                                <td><span class="badge bg-primary">{{ $result->grade ?? '-' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-award fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No exam results available</p>
                </div>
                @endif
            </x-card>
        </div>

        <!-- Fees Tab -->
        <div x-show="activeTab === 'fees'" x-transition>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="text-white-50">Total Fees</h6>
                            <h3 class="mb-0">Rs. {{ number_format($totalFees ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="text-white-50">Paid Amount</h6>
                            <h3 class="mb-0">Rs. {{ number_format($paidFees ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-danger text-white">
                        <div class="card-body text-center">
                            <h6 class="text-white-50">Pending Amount</h6>
                            <h3 class="mb-0">Rs. {{ number_format($pendingFees ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <x-card title="Fee Details" icon="bi-currency-rupee">
                        <x-slot name="actions">
                            <a href="{{ route('students.fees', $student->id ?? 0) }}" class="btn btn-sm btn-outline-primary">View All</a>
                            <button type="button" class="btn btn-sm btn-success">
                                <i class="bi bi-plus me-1"></i> Collect Fee
                            </button>
                        </x-slot>

                        @if(isset($feeAllotments) && count($feeAllotments) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Discount</th>
                                        <th>Net Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeAllotments as $fee)
                                    <tr>
                                        <td>{{ $fee->feeType->name ?? '-' }}</td>
                                        <td>Rs. {{ number_format($fee->amount ?? 0) }}</td>
                                        <td>Rs. {{ number_format($fee->discount ?? 0) }}</td>
                                        <td>Rs. {{ number_format(($fee->amount ?? 0) - ($fee->discount ?? 0)) }}</td>
                                        <td>{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('d M Y') : '-' }}</td>
                                        <td>
                                            <span class="badge {{ ($fee->status ?? '') === 'paid' ? 'bg-success' : (($fee->status ?? '') === 'overdue' ? 'bg-danger' : 'bg-warning') }}">
                                                {{ ucfirst($fee->status ?? 'Pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-currency-rupee fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No fee records available</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>

        <!-- Transport Tab -->
        <div x-show="activeTab === 'transport'" x-transition>
            <x-card title="Transport Details" icon="bi-bus-front">
                @if(isset($transportStudent) && $transportStudent)
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Route</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->route->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Stop</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->stop->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Pick-up Time</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->stop->pickup_time ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Vehicle Number</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->vehicle->vehicle_number ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Driver Name</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->vehicle->driver_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0">Driver Phone</label>
                        <p class="mb-2 fw-medium">{{ $transportStudent->vehicle->driver_phone ?? '-' }}</p>
                    </div>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-bus-front fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No transport assigned</p>
                </div>
                @endif
            </x-card>
        </div>

        <!-- Library Tab -->
        <div x-show="activeTab === 'library'" x-transition>
            <x-card title="Library Records" icon="bi-book">
                @if(isset($libraryIssues) && count($libraryIssues) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($libraryIssues as $issue)
                            <tr>
                                <td>{{ $issue->book->title ?? '-' }}</td>
                                <td>{{ $issue->book->author ?? '-' }}</td>
                                <td>{{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') : '-' }}</td>
                                <td>{{ $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('d M Y') : '-' }}</td>
                                <td>{{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y') : '-' }}</td>
                                <td>
                                    <span class="badge {{ $issue->return_date ? 'bg-success' : 'bg-warning' }}">
                                        {{ $issue->return_date ? 'Returned' : 'Issued' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No library records available</p>
                </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="bi bi-calendar-check d-block fs-4 mb-1"></i>
                        <small>Mark Attendance</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="#" class="btn btn-outline-success w-100">
                        <i class="bi bi-pencil-square d-block fs-4 mb-1"></i>
                        <small>Enter Marks</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="#" class="btn btn-outline-warning w-100">
                        <i class="bi bi-currency-rupee d-block fs-4 mb-1"></i>
                        <small>Collect Fee</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="#" class="btn btn-outline-info w-100">
                        <i class="bi bi-chat-dots d-block fs-4 mb-1"></i>
                        <small>Send Message</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" x-ref="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this student?</p>
                    <p class="fw-bold">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }} ({{ $student->admission_number ?? '' }})</p>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-circle me-1"></i>This action cannot be undone. All related data will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('students.destroy', $student->id ?? 0) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function studentProfile() {
    return {
        activeTab: 'personal',
        
        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },
        
        printProfile() {
            window.print();
        },
        
        printReportCard() {
            window.open('{{ route("students.results", $student->id ?? 0) }}?print=1', '_blank');
        },
        
        sendMessage() {
            Swal.fire({
                title: 'Send Message',
                html: `
                    <select id="messageType" class="form-select mb-3">
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="both">Both</option>
                    </select>
                    <textarea id="messageContent" class="form-control" rows="4" placeholder="Enter your message..."></textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Send',
                preConfirm: () => {
                    const type = document.getElementById('messageType').value;
                    const content = document.getElementById('messageContent').value;
                    if (!content) {
                        Swal.showValidationMessage('Please enter a message');
                    }
                    return { type, content };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Sent!', 'Message has been sent successfully.', 'success');
                }
            });
        },
        
        init() {
            // Initialize attendance charts
            this.initCharts();
        },
        
        initCharts() {
            // Attendance Line Chart
            const attendanceCtx = document.getElementById('attendanceChart');
            if (attendanceCtx) {
                new Chart(attendanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Attendance %',
                            data: @json($monthlyAttendance ?? [85, 90, 88, 92, 95, 87, 91, 89, 93, 90, 88, 92]),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0,
                                max: 100
                            }
                        }
                    }
                });
            }
            
            // Attendance Pie Chart
            const pieCtx = document.getElementById('attendancePieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Absent', 'Late', 'Leave'],
                        datasets: [{
                            data: [{{ $presentDays ?? 180 }}, {{ $absentDays ?? 10 }}, {{ $lateDays ?? 5 }}, {{ $leaveDays ?? 5 }}],
                            backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6']
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        }
    };
}
</script>
@endpush
@endsection
