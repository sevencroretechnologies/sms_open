{{-- Parent Show View --}}
{{-- Admin parent details --}}

@extends('layouts.app')

@section('title', 'Parent Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Parent Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.parents.index') }}">Parents</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.parents.edit', $parent ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.parents.index') }}" class="btn btn-outline-secondary">
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
            <!-- Parent Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $parent->user->name ?? 'Parent Name' }}</h4>
                    <p class="text-muted mb-2">{{ ucfirst($parent->relation ?? 'Guardian') }}</p>
                    <span class="badge {{ ($parent->user->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($parent->user->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-envelope text-muted me-3"></i>
                        <span>{{ $parent->user->email ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-telephone text-muted me-3"></i>
                        <span>{{ $parent->phone ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-briefcase text-muted me-3"></i>
                        <span>{{ $parent->occupation ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex align-items-center py-2">
                        <i class="bi bi-geo-alt text-muted me-3"></i>
                        <span>{{ $parent->address ?? 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Children -->
            <x-card :noPadding="true" class="mb-4">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-people me-2"></i>Children</span>
                        <span class="badge bg-primary">{{ $parent->students_count ?? 0 }}</span>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Admission No</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parent->students ?? [] as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-info bg-opacity-10 text-info" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <span class="fw-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $student->admission_number ?? '-' }}</td>
                                    <td>{{ $student->schoolClass->name ?? '-' }}</td>
                                    <td>{{ $student->section->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No children linked
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>
                
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.communication.compose-email', ['parent_id' => $parent->id ?? 1]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-1"></i> Send Email
                    </a>
                    <a href="{{ route('admin.communication.compose-sms', ['parent_id' => $parent->id ?? 1]) }}" class="btn btn-outline-info">
                        <i class="bi bi-chat-dots me-1"></i> Send SMS
                    </a>
                    <a href="{{ route('admin.parents.edit', $parent ?? 1) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
