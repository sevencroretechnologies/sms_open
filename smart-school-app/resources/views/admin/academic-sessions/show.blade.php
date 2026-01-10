@extends('layouts.app')

@section('title', 'Academic Session Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Academic Session Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.academic-sessions.edit', $academicSession->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-calendar-range fs-1 text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $academicSession->name ?? 'Session Name' }}</h4>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <span class="badge {{ ($academicSession->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                            {{ ($academicSession->is_active ?? true) ? 'Active' : 'Inactive' }}
                        </span>
                        @if($academicSession->is_current ?? false)
                            <span class="badge bg-primary">Current</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar me-2"></i>Duration
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Start Date</small>
                        <strong>{{ $academicSession->start_date ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">End Date</small>
                        <strong>{{ $academicSession->end_date ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['total_students'] ?? 0 }}</h3>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['total_classes'] ?? 0 }}</h3>
                            <small class="text-muted">Classes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['total_exams'] ?? 0 }}</h3>
                            <small class="text-muted">Exams</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-info bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['total_fees_collected'] ?? 0 }}</h3>
                            <small class="text-muted">Fees Collected</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Session Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <small class="text-muted d-block">Description</small>
                            <p>{{ $academicSession->description ?? 'No description available.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
