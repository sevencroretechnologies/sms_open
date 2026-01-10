@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ $student->user->name ?? 'Student' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.students.attendance', $student->id) }}" class="btn btn-outline-info">
                <i class="bi bi-calendar-check me-1"></i> Attendance
            </a>
            <a href="{{ route('teacher.students.marks', $student->id) }}" class="btn btn-outline-success">
                <i class="bi bi-journal-text me-1"></i> Marks
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <x-card class="mb-4">
                <div class="text-center">
                    <img src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name ?? 'S') . '&background=4f46e5&color=fff&size=150' }}" 
                         class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h4 class="mb-1">{{ $student->user->name ?? 'N/A' }}</h4>
                    <p class="text-muted mb-2">{{ $student->admission_number ?? '' }}</p>
                    <span class="badge bg-primary">{{ $student->schoolClass->display_name ?? '' }} - {{ $student->section->display_name ?? '' }}</span>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0">{{ $student->roll_number ?? '-' }}</h5>
                        <small class="text-muted">Roll No</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $recentAttendance->where('attendanceType.is_present', true)->count() }}</h5>
                        <small class="text-muted">Present</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0">{{ $recentMarks->count() }}</h5>
                        <small class="text-muted">Exams</small>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <i class="bi bi-person me-2"></i>Contact Information
                </x-slot>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 100px;">Email</td>
                        <td>{{ $student->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td>{{ $student->user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gender</td>
                        <td class="text-capitalize">{{ $student->gender ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">DOB</td>
                        <td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Category</td>
                        <td>{{ $student->category->name ?? '-' }}</td>
                    </tr>
                </table>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card class="mb-4" :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-calendar-check me-2"></i>Recent Attendance
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                            <span class="badge" style="background-color: {{ $attendance->attendanceType->color }}">
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
                                    <td colspan="3" class="text-center py-3 text-muted">No attendance records</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-journal-text me-2"></i>Recent Exam Results
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                    <td>
                                        @if($mark->is_absent)
                                            <span class="text-danger">Absent</span>
                                        @else
                                            {{ $mark->marks_obtained ?? '-' }} / {{ $mark->examSchedule->full_marks ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $mark->grade ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No exam results</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
