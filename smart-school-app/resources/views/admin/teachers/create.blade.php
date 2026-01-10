@extends('layouts.app')

@section('title', isset($teacher) ? 'Edit Teacher' : 'Add Teacher')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($teacher) ? 'Edit Teacher' : 'Add Teacher' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
                    <li class="breadcrumb-item active">{{ isset($teacher) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($teacher) ? route('admin.teachers.update', $teacher->id) : route('admin.teachers.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($teacher))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person me-2"></i>Personal Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $teacher->first_name ?? '') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $teacher->last_name ?? '') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $teacher->employee_id ?? '') }}" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $teacher->dob ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $teacher->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $teacher->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $teacher->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Blood Group</label>
                                <select name="blood_group" class="form-select">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $teacher->blood_group ?? '') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-briefcase me-2"></i>Professional Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control" value="{{ old('department', $teacher->department ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation', $teacher->designation ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $teacher->qualification ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Experience (Years)</label>
                                <input type="number" name="experience" class="form-control" value="{{ old('experience', $teacher->experience ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Joining Date</label>
                                <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Salary</label>
                                <input type="number" name="salary" class="form-control" value="{{ old('salary', $teacher->salary ?? '') }}" step="0.01">
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
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $teacher->phone ?? '') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $teacher->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-image me-2"></i>Photo
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="avatar bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <i class="bi bi-person fs-1 text-muted"></i>
                            </div>
                        </div>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-key me-2"></i>Login Credentials
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $teacher->username ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password {{ isset($teacher) ? '(leave blank to keep current)' : '' }}</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>Status
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $teacher->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Teacher</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($teacher) ? 'Update Teacher' : 'Add Teacher' }}
            </button>
        </div>
    </form>
</div>
@endsection
