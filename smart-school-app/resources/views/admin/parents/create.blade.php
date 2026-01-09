@extends('layouts.app')

@section('title', isset($parent) ? 'Edit Parent' : 'Add Parent')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($parent) ? 'Edit Parent' : 'Add Parent' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.parents.index') }}">Parents</a></li>
                    <li class="breadcrumb-item active">{{ isset($parent) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($parent) ? route('admin.parents.update', $parent->id) : route('admin.parents.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($parent))
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
                                <label class="form-label">Father's Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name', $parent->father_name ?? '') }}" required>
                                @error('father_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Occupation</label>
                                <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation', $parent->father_occupation ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Name</label>
                                <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $parent->mother_name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Occupation</label>
                                <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $parent->mother_occupation ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $parent->guardian_name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relation with Guardian</label>
                                <input type="text" name="guardian_relation" class="form-control" value="{{ old('guardian_relation', $parent->guardian_relation ?? '') }}">
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
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $parent->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $parent->phone ?? '') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $parent->address ?? '') }}</textarea>
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
                            <input type="text" name="username" class="form-control" value="{{ old('username', $parent->username ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password {{ isset($parent) ? '(leave blank to keep current)' : '' }}</label>
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
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $parent->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.parents.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($parent) ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>
@endsection
