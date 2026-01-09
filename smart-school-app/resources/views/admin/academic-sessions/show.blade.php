{{-- Academic Session Show View --}}
{{-- Admin academic session details --}}

@extends('layouts.app')

@section('title', 'Session Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Session Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.academic-sessions.edit', $academicSession ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-outline-secondary">
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
            <!-- Session Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-calendar-range fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $academicSession->name ?? 'Session Name' }}</h4>
                    <div class="mb-2">
                        @if($academicSession->is_current ?? false)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Current Session</span>
                        @endif
                        <span class="badge {{ ($academicSession->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                            {{ ($academicSession->is_active ?? true) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Start Date</span>
                        <span>{{ isset($academicSession->start_date) ? $academicSession->start_date->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">End Date</span>
                        <span>{{ isset($academicSession->end_date) ? $academicSession->end_date->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Duration</span>
                        <span>{{ isset($academicSession->start_date) && isset($academicSession->end_date) ? $academicSession->start_date->diffInDays($academicSession->end_date) . ' days' : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $studentsCount ?? 0 }}</h3>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $classesCount ?? 0 }}</h3>
                            <small class="text-muted">Classes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-info bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $examsCount ?? 0 }}</h3>
                            <small class="text-muted">Exams</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Description
                </x-slot>
                
                <p class="mb-0">{{ $academicSession->description ?? 'No description available.' }}</p>
            </x-card>

            <!-- Actions -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>
                
                <div class="d-flex flex-wrap gap-2">
                    @if(!($academicSession->is_current ?? false))
                        <form action="{{ route('admin.academic-sessions.set-current', $academicSession ?? 1) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-check-circle me-1"></i> Set as Current
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.students.index', ['session_id' => $academicSession->id ?? 1]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-people me-1"></i> View Students
                    </a>
                    <a href="{{ route('admin.exams.index', ['session_id' => $academicSession->id ?? 1]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-journal-bookmark me-1"></i> View Exams
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
