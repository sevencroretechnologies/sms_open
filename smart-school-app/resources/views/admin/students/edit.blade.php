{{-- Student Edit View --}}
{{-- Admin student edit form --}}

@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Student</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.students.show', $student ?? 1) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i> View Profile
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Edit Form -->
    <form action="{{ route('admin.students.update', $student ?? 1) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-person me-2"></i>
                        Basic Information
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->user->first_name ?? '') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->user->last_name ?? '') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->user->email ?? '') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->user->phone ?? '') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', isset($student->date_of_birth) ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $student->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                <option value="">Select Blood Group</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group ?? '') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                            @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control @error('religion') is-invalid @enderror" value="{{ old('religion', $student->religion ?? '') }}">
                            @error('religion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                <!-- Academic Information -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-mortarboard me-2"></i>
                        Academic Information
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                            <input type="text" name="admission_number" class="form-control @error('admission_number') is-invalid @enderror" value="{{ old('admission_number', $student->admission_number ?? '') }}" required>
                            @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control @error('roll_number') is-invalid @enderror" value="{{ old('roll_number', $student->roll_number ?? '') }}">
                            @error('roll_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach($academicSessions ?? [] as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id', $student->academic_session_id ?? '') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Admission <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_admission" class="form-control @error('date_of_admission') is-invalid @enderror" value="{{ old('date_of_admission', isset($student->date_of_admission) ? $student->date_of_admission->format('Y-m-d') : '') }}" required>
                            @error('date_of_admission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                <option value="">Select Section</option>
                                @foreach($sections ?? [] as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id', $student->section_id ?? '') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                            @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $student->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $student->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Type <span class="text-danger">*</span></label>
                            <select name="admission_type" class="form-select @error('admission_type') is-invalid @enderror" required>
                                <option value="new" {{ old('admission_type', $student->admission_type ?? '') == 'new' ? 'selected' : '' }}>New Admission</option>
                                <option value="transfer" {{ old('admission_type', $student->admission_type ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            @error('admission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                <!-- Parent/Guardian Information -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-people me-2"></i>
                        Parent/Guardian Information
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $student->father_name ?? '') }}">
                            @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Phone</label>
                            <input type="text" name="father_phone" class="form-control @error('father_phone') is-invalid @enderror" value="{{ old('father_phone', $student->father_phone ?? '') }}">
                            @error('father_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" value="{{ old('mother_name', $student->mother_name ?? '') }}">
                            @error('mother_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Phone</label>
                            <input type="text" name="mother_phone" class="form-control @error('mother_phone') is-invalid @enderror" value="{{ old('mother_phone', $student->mother_phone ?? '') }}">
                            @error('mother_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                <!-- Address Information -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-geo-alt me-2"></i>
                        Address Information
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $student->address ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $student->city ?? '') }}">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $student->state ?? '') }}">
                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $student->country ?? 'India') }}" required>
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $student->postal_code ?? '') }}">
                            @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="col-lg-4">
                <!-- Photo Upload -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-camera me-2"></i>
                        Student Photo
                    </x-slot>
                    
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded bg-light" style="width: 150px; height: 150px;">
                                <i class="bi bi-person fs-1 text-muted"></i>
                            </div>
                        </div>
                        <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-2">Max size: 2MB. Formats: JPG, PNG</small>
                    </div>
                </x-card>

                <!-- Student Info -->
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-info-circle me-2"></i>
                        Student Info
                    </x-slot>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Admission No</span>
                            <span>{{ $student->admission_number ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Class</span>
                            <span>{{ $student->schoolClass->name ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Section</span>
                            <span>{{ $student->section->name ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </x-card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Update Student
            </button>
        </div>
    </form>
</div>
@endsection
