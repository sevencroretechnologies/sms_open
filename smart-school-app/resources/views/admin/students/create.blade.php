@extends('layouts.app')

@section('title', isset($student) ? 'Edit Student' : 'Add Student')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($student) ? 'Edit Student' : 'Add Student' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ isset($student) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($student) ? route('admin.students.update', $student->id) : route('admin.students.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($student))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person me-2"></i>Basic Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name ?? '') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->last_name ?? '') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                                <input type="text" name="admission_no" class="form-control @error('admission_no') is-invalid @enderror" value="{{ old('admission_no', $student->admission_no ?? '') }}" required>
                                @error('admission_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Roll Number</label>
                                <input type="text" name="roll_no" class="form-control" value="{{ old('roll_no', $student->roll_no ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $student->dob ?? '') }}" required>
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $student->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
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
                                        <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group ?? '') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Religion</label>
                                <input type="text" name="religion" class="form-control" value="{{ old('religion', $student->religion ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-mortarboard me-2"></i>Academic Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select name="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicSessions ?? [] as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id', $student->academic_session_id ?? '') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes ?? [] as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Section <span class="text-danger">*</span></label>
                                <select name="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                    <option value="">Select Section</option>
                                </select>
                                @error('section_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $student->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" name="admission_date" class="form-control @error('admission_date') is-invalid @enderror" value="{{ old('admission_date', $student->admission_date ?? date('Y-m-d')) }}" required>
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $student->address ?? '') }}</textarea>
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
                        <i class="bi bi-people me-2"></i>Parent/Guardian
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Father's Phone</label>
                            <input type="text" name="father_phone" class="form-control" value="{{ old('father_phone', $student->father_phone ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mother's Phone</label>
                            <input type="text" name="mother_phone" class="form-control" value="{{ old('mother_phone', $student->mother_phone ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>Status
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $student->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Student</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($student) ? 'Update Student' : 'Add Student' }}
            </button>
        </div>
    </form>
</div>
@endsection
