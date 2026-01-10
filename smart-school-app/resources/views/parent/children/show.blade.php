@extends('layouts.app')

@section('title', $child->user->name ?? 'Child Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.index') }}">My Children</a></li>
                    <li class="breadcrumb-item active">{{ $child->user->name ?? 'Profile' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <x-card>
                <div class="text-center">
                    @if($child->photo)
                        <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->user->name ?? 'Student' }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span class="text-white fs-1">{{ substr($child->user->name ?? 'S', 0, 1) }}</span>
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $child->user->name ?? 'N/A' }}</h4>
                    <p class="text-muted mb-2">{{ $child->admission_no ?? 'N/A' }}</p>
                    <span class="badge bg-primary">{{ $child->schoolClass->name ?? 'N/A' }} - {{ $child->section->name ?? 'N/A' }}</span>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <a href="{{ route('parent.children.attendance', $child->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-check me-2"></i>View Attendance
                    </a>
                    <a href="{{ route('parent.children.exams', $child->id) }}" class="btn btn-outline-success">
                        <i class="fas fa-chart-bar me-2"></i>View Exam Results
                    </a>
                    <a href="{{ route('parent.children.fees', $child->id) }}" class="btn btn-outline-warning">
                        <i class="fas fa-money-bill me-2"></i>View Fees
                    </a>
                </div>
            </x-card>

            @if($feesSummary)
                <x-card title="Fees Summary" class="mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="mb-0 text-primary">{{ number_format($feesSummary->total ?? 0, 0) }}</h6>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0 text-success">{{ number_format($feesSummary->paid ?? 0, 0) }}</h6>
                            <small class="text-muted">Paid</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-0 {{ ($feesSummary->balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($feesSummary->balance ?? 0, 0) }}</h6>
                            <small class="text-muted">Balance</small>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>

        <div class="col-lg-8">
            <x-card title="Personal Information">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Full Name</label>
                        <p class="mb-0 fw-medium">{{ $child->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Roll Number</label>
                        <p class="mb-0 fw-medium">{{ $child->roll_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Date of Birth</label>
                        <p class="mb-0 fw-medium">{{ $child->dob ? $child->dob->format('d M Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Gender</label>
                        <p class="mb-0 fw-medium">{{ ucfirst($child->gender ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Blood Group</label>
                        <p class="mb-0 fw-medium">{{ $child->blood_group ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Admission Date</label>
                        <p class="mb-0 fw-medium">{{ $child->admission_date ? $child->admission_date->format('d M Y') : 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Recent Attendance" class="mt-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                                    <td>
                                        @if($attendance->attendanceType)
                                            <span class="badge bg-{{ $attendance->attendanceType->is_present ? 'success' : 'danger' }}">
                                                {{ $attendance->attendanceType->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No recent attendance records</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('parent.children.attendance', $child->id) }}" class="btn btn-outline-primary btn-sm">View All</a>
            </x-card>

            <x-card title="Recent Exam Results" class="mt-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMarks as $mark)
                                <tr>
                                    <td>{{ $mark->examSchedule->exam->name ?? 'N/A' }}</td>
                                    <td>{{ $mark->examSchedule->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $mark->obtained_marks }} / {{ $mark->examSchedule->full_marks ?? '-' }}</td>
                                    <td>
                                        @if($mark->grade)
                                            <span class="badge bg-info">{{ $mark->grade }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent exam results</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('parent.children.exams', $child->id) }}" class="btn btn-outline-primary btn-sm">View All</a>
            </x-card>
        </div>
    </div>
</div>
@endsection
