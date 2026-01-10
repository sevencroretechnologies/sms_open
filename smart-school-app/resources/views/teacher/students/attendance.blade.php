@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Attendance</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.students.show', $student->id) }}">{{ $student->user->name ?? 'Student' }}</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <x-card class="mb-4">
                <div class="text-center">
                    <img src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name ?? 'S') . '&background=4f46e5&color=fff&size=100' }}" 
                         class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 class="mb-1">{{ $student->user->name ?? 'N/A' }}</h5>
                    <p class="text-muted mb-0">{{ $student->schoolClass->display_name ?? '' }} - {{ $student->section->display_name ?? '' }}</p>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <i class="bi bi-bar-chart me-2"></i>Attendance Summary
                </x-slot>
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary mb-0">{{ $summary['total'] }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success mb-0">{{ $summary['present'] }}</h4>
                        <small class="text-muted">Present</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger mb-0">{{ $summary['absent'] }}</h4>
                        <small class="text-muted">Absent</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    @php
                        $percentage = $summary['total'] > 0 ? round(($summary['present'] / $summary['total']) * 100, 1) : 0;
                    @endphp
                    <h3 class="{{ $percentage >= 75 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                        {{ $percentage }}%
                    </h3>
                    <small class="text-muted">Attendance Rate</small>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-calendar-check me-2"></i>Attendance History
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
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date->format('d M Y (l)') }}</td>
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
                                    <td colspan="3" class="text-center py-4">
                                        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">No attendance records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attendances->hasPages())
                    <div class="card-footer">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
