@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.students.edit', $student->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
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
                    <h4 class="mb-1">{{ $student->name ?? 'Student Name' }}</h4>
                    <p class="text-muted mb-2">{{ $student->admission_no ?? 'ADM-XXXX' }}</p>
                    <span class="badge {{ ($student->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($student->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-mortarboard me-2"></i>Academic Info
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Class</small>
                        <strong>{{ $student->class->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Section</small>
                        <strong>{{ $student->section->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Roll Number</small>
                        <strong>{{ $student->roll_no ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Admission Date</small>
                        <strong>{{ $student->admission_date ?? 'N/A' }}</strong>
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
                            <strong>{{ $student->dob ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Gender</small>
                            <strong>{{ ucfirst($student->gender ?? 'N/A') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Blood Group</small>
                            <strong>{{ $student->blood_group ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Religion</small>
                            <strong>{{ $student->religion ?? 'N/A' }}</strong>
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
                            <strong>{{ $student->email ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $student->phone ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Address</small>
                            <strong>{{ $student->address ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Parent/Guardian Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Father's Name</small>
                            <strong>{{ $student->father_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Father's Phone</small>
                            <strong>{{ $student->father_phone ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Mother's Name</small>
                            <strong>{{ $student->mother_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Mother's Phone</small>
                            <strong>{{ $student->mother_phone ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
