@extends('layouts.app')

@section('title', 'Attendance - ' . ($child->user->name ?? 'Child'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.index') }}">My Children</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.show', $child->id) }}">{{ $child->user->name ?? 'Child' }}</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-primary">{{ $summary['total'] }}</h3>
                    <small class="text-muted">Total Days</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-success">{{ $summary['present'] }}</h3>
                    <small class="text-muted">Present</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-danger">{{ $summary['absent'] }}</h3>
                    <small class="text-muted">Absent</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 {{ $summary['percentage'] >= 75 ? 'text-success' : 'text-warning' }}">{{ $summary['percentage'] }}%</h3>
                    <small class="text-muted">Attendance Rate</small>
                </div>
            </x-card>
        </div>
    </div>

    <x-card>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">{{ $child->user->name ?? 'Child' }}'s Attendance</h5>
            <span class="badge bg-primary">{{ $child->schoolClass->name ?? '' }} - {{ $child->section->name ?? '' }}</span>
        </div>

        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <label for="month" class="form-label">Select Month</label>
                <input type="month" class="form-control" id="month" name="month" value="{{ $month }}" onchange="this.form.submit()">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>{{ $attendance->attendance_date->format('l') }}</td>
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
                            <td colspan="4" class="text-center py-4">
                                <p class="text-muted mb-0">No attendance records found for this month.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">
        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>
@endsection
