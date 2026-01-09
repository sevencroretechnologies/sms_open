{{-- Student Promotions View --}}
{{-- Admin student promotion management --}}

@extends('layouts.app')

@section('title', 'Student Promotions')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Promotions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Promotions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.promotions.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> History
            </a>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                <i class="bi bi-arrow-up-circle me-1"></i> Promote Students
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-25 rounded p-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">{{ $totalStudents ?? 0 }}</h3>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-25 rounded p-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">{{ $promotedCount ?? 0 }}</h3>
                            <small class="text-muted">Promoted</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-25 rounded p-3">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">{{ $pendingCount ?? 0 }}</h3>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-25 rounded p-3">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">{{ $failedCount ?? 0 }}</h3>
                            <small class="text-muted">Not Promoted</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promotion Info -->
    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-info-circle me-2"></i>
            Promotion Information
        </x-slot>
        
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Current Session</h6>
                <p class="mb-0 fs-5">{{ $currentSession->name ?? 'Not Set' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Next Session</h6>
                <p class="mb-0 fs-5">{{ $nextSession->name ?? 'Not Set' }}</p>
            </div>
        </div>
    </x-card>

    <!-- Class-wise Promotion Status -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-grid me-2"></i>
            Class-wise Promotion Status
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Class</th>
                        <th>Total Students</th>
                        <th>Promoted</th>
                        <th>Pending</th>
                        <th>Not Promoted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes ?? [] as $class)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                        <i class="bi bi-mortarboard"></i>
                                    </span>
                                    <span class="fw-medium">{{ $class->name }}</span>
                                </div>
                            </td>
                            <td>{{ $class->students_count ?? 0 }}</td>
                            <td><span class="badge bg-success">{{ $class->promoted_count ?? 0 }}</span></td>
                            <td><span class="badge bg-warning">{{ $class->pending_count ?? 0 }}</span></td>
                            <td><span class="badge bg-danger">{{ $class->failed_count ?? 0 }}</span></td>
                            <td>
                                <a href="{{ route('admin.promotions.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-up-circle me-1"></i> Promote
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No classes found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
