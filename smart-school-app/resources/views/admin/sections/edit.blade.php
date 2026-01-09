{{-- Section Edit View --}}
{{-- Admin section edit form --}}

@extends('layouts.app')

@section('title', 'Edit Section')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Section</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.sections.show', $section ?? 1) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i> View
            </a>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
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

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-grid me-2"></i>
                    Section Details
                </x-slot>
                
                <form action="{{ route('admin.sections.update', $section ?? 1) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $section->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="school_class_id" class="form-select @error('school_class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('school_class_id', $section->school_class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $section->capacity ?? '') }}" min="1">
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $section->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $section->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $section->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Section
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Section Info
                </x-slot>
                
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Students</span>
                        <span>{{ $section->students_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($section->created_at) ? $section->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Updated</span>
                        <span>{{ isset($section->updated_at) ? $section->updated_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
