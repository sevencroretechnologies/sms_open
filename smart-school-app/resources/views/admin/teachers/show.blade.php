@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Teacher Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.teachers.edit', $teacher->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                        <i class="bi bi-person fs-1 text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $teacher->name ?? 'Teacher Name' }}</h4>
                    <p class="text-muted mb-2">{{ $teacher->employee_id ?? 'EMP-XXXX' }}</p>
                    <span class="badge {{ ($teacher->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($teacher->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-briefcase me-2"></i>Professional Info
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Department</small>
                        <strong>{{ $teacher->department ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Designation</small>
                        <strong>{{ $teacher->designation ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Qualification</small>
                        <strong>{{ $teacher->qualification ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Joining Date</small>
                        <strong>{{ $teacher->joining_date ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Personal Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Date of Birth</small>
                            <strong>{{ $teacher->dob ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Gender</small>
                            <strong>{{ ucfirst($teacher->gender ?? 'N/A') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Blood Group</small>
                            <strong>{{ $teacher->blood_group ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Experience</small>
                            <strong>{{ $teacher->experience ?? 'N/A' }} years</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-envelope me-2"></i>Contact Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $teacher->email ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $teacher->phone ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Address</small>
                            <strong>{{ $teacher->address ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Assigned Subjects & Classes
                </div>
                <div class="card-body">
                    <p class="text-muted">No subjects or classes assigned yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
