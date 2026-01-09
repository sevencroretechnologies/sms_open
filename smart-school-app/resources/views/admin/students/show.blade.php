{{-- Student Show View --}}
{{-- Admin student profile view --}}

@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.students.edit', $student ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 100px; height: 100px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $student->user->name ?? 'Student Name' }}</h4>
                    <p class="text-muted mb-2">{{ $student->admission_number ?? 'ADM-001' }}</p>
                    <span class="badge {{ ($student->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($student->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->schoolClass->name ?? 'Class' }}</h5>
                        <small class="text-muted">Class</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->section->name ?? 'Section' }}</h5>
                        <small class="text-muted">Section</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->roll_number ?? '-' }}</h5>
                        <small class="text-muted">Roll No</small>
                    </div>
                </div>
            </x-card>

            <!-- Quick Info -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Quick Info
                </x-slot>
                
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Gender</span>
                        <span>{{ ucfirst($student->gender ?? 'N/A') }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Date of Birth</span>
                        <span>{{ isset($student->date_of_birth) ? $student->date_of_birth->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Blood Group</span>
                        <span>{{ $student->blood_group ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Religion</span>
                        <span>{{ $student->religion ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Admission Date</span>
                        <span>{{ isset($student->date_of_admission) ? $student->date_of_admission->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details">Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#parents">Parents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#fees">Fees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#attendance">Attendance</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details">
                    <x-card class="mb-4">
                        <x-slot name="header">
                            <i class="bi bi-person-badge me-2"></i>
                            Personal Information
                        </x-slot>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Full Name</label>
                                <p class="mb-0">{{ $student->user->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <p class="mb-0">{{ $student->user->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Phone</label>
                                <p class="mb-0">{{ $student->user->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nationality</label>
                                <p class="mb-0">{{ $student->nationality ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <x-slot name="header">
                            <i class="bi bi-geo-alt me-2"></i>
                            Address Information
                        </x-slot>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small">Address</label>
                                <p class="mb-0">{{ $student->address ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">City</label>
                                <p class="mb-0">{{ $student->city ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">State</label>
                                <p class="mb-0">{{ $student->state ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Country</label>
                                <p class="mb-0">{{ $student->country ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Parents Tab -->
                <div class="tab-pane fade" id="parents">
                    <x-card class="mb-4">
                        <x-slot name="header">
                            <i class="bi bi-person me-2"></i>
                            Father's Information
                        </x-slot>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Name</label>
                                <p class="mb-0">{{ $student->father_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Phone</label>
                                <p class="mb-0">{{ $student->father_phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Occupation</label>
                                <p class="mb-0">{{ $student->father_occupation ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <p class="mb-0">{{ $student->father_email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <x-slot name="header">
                            <i class="bi bi-person me-2"></i>
                            Mother's Information
                        </x-slot>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Name</label>
                                <p class="mb-0">{{ $student->mother_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Phone</label>
                                <p class="mb-0">{{ $student->mother_phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Occupation</label>
                                <p class="mb-0">{{ $student->mother_occupation ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <p class="mb-0">{{ $student->mother_email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Fees Tab -->
                <div class="tab-pane fade" id="fees">
                    <x-card>
                        <x-slot name="header">
                            <i class="bi bi-currency-dollar me-2"></i>
                            Fee Transactions
                        </x-slot>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->feesTransactions ?? [] as $transaction)
                                        <tr>
                                            <td>{{ $transaction->payment_date->format('d M Y') }}</td>
                                            <td>{{ $transaction->feesMaster->feesType->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($transaction->amount, 2) }}</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No fee transactions found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>

                <!-- Attendance Tab -->
                <div class="tab-pane fade" id="attendance">
                    <x-card>
                        <x-slot name="header">
                            <i class="bi bi-calendar-check me-2"></i>
                            Recent Attendance
                        </x-slot>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->attendances ?? [] as $attendance)
                                        <tr>
                                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge {{ $attendance->status === 'present' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $attendance->remarks ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No attendance records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
