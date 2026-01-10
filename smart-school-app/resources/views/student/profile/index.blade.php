@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row">
        <div class="col-lg-4">
            <x-card>
                <div class="text-center">
                    @if($student && $student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Profile Photo" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span class="text-white fs-1">{{ substr($user->name ?? 'S', 0, 1) }}</span>
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $user->name ?? 'N/A' }}</h4>
                    <p class="text-muted mb-3">{{ $student->admission_no ?? 'N/A' }}</p>
                    
                    @if($student)
                        <span class="badge bg-primary">{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="mt-3">
                    <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card title="Personal Information">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Full Name</label>
                        <p class="mb-0 fw-medium">{{ $user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0 fw-medium">{{ $user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Phone</label>
                        <p class="mb-0 fw-medium">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Date of Birth</label>
                        <p class="mb-0 fw-medium">{{ $student && $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Gender</label>
                        <p class="mb-0 fw-medium">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Blood Group</label>
                        <p class="mb-0 fw-medium">{{ $student->blood_group ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Academic Information" class="mt-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Admission Number</label>
                        <p class="mb-0 fw-medium">{{ $student->admission_no ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Roll Number</label>
                        <p class="mb-0 fw-medium">{{ $student->roll_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Class</label>
                        <p class="mb-0 fw-medium">{{ $student->schoolClass->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Section</label>
                        <p class="mb-0 fw-medium">{{ $student->section->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Admission Date</label>
                        <p class="mb-0 fw-medium">{{ $student && $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Category</label>
                        <p class="mb-0 fw-medium">{{ $student->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Contact Information" class="mt-4">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Current Address</label>
                        <p class="mb-0 fw-medium">{{ $student->current_address ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Permanent Address</label>
                        <p class="mb-0 fw-medium">{{ $student->permanent_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
