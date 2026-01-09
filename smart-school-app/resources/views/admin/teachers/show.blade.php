{{-- Teacher Show View --}}
{{-- Admin teacher details --}}

@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Teacher Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.teachers.edit', $teacher ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
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
            <!-- Teacher Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person-badge fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $teacher->user->name ?? 'Teacher Name' }}</h4>
                    <p class="text-muted mb-2">{{ $teacher->designation ?? 'Teacher' }}</p>
                    <span class="badge {{ ($teacher->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($teacher->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-person-vcard text-muted me-3"></i>
                        <span>{{ $teacher->employee_id ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-envelope text-muted me-3"></i>
                        <span>{{ $teacher->user->email ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-telephone text-muted me-3"></i>
                        <span>{{ $teacher->phone ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-mortarboard text-muted me-3"></i>
                        <span>{{ $teacher->qualification ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2">
                        <i class="bi bi-calendar text-muted me-3"></i>
                        <span>Joined: {{ isset($teacher->joining_date) ? $teacher->joining_date->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>

            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $teacher->subjects_count ?? 0 }}</h3>
                            <small class="text-muted">Subjects</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $teacher->classes_count ?? 0 }}</h3>
                            <small class="text-muted">Classes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Assigned Subjects -->
            <x-card class="mb-4" :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-book me-2"></i>
                    Assigned Subjects
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacher->subjectAssignments ?? [] as $index => $assignment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->schoolClass->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->section->name ?? 'All' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No subjects assigned
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Timetable -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-calendar-week me-2"></i>
                    Today's Schedule
                </x-slot>

                @if(count($todaySchedule ?? []) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($todaySchedule ?? [] as $slot)
                            <div class="p-2 rounded bg-primary bg-opacity-10 text-center" style="min-width: 100px;">
                                <small class="d-block text-muted">{{ $slot->period->start_time ?? '' }}</small>
                                <span class="fw-medium">{{ $slot->subject->name ?? 'N/A' }}</span>
                                <small class="d-block">{{ $slot->schoolClass->name ?? '' }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No classes scheduled for today</p>
                @endif
            </x-card>

            <!-- Quick Actions -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>
                
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.communication.compose-email', ['teacher_id' => $teacher->id ?? 1]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-1"></i> Send Email
                    </a>
                    <a href="{{ route('admin.timetable.index', ['teacher_id' => $teacher->id ?? 1]) }}" class="btn btn-outline-info">
                        <i class="bi bi-calendar-week me-1"></i> View Timetable
                    </a>
                    <a href="{{ route('admin.teachers.edit', $teacher ?? 1) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
