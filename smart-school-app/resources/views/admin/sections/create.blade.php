@extends('layouts.app')

@section('title', isset($section) ? 'Edit Section' : 'Add Section')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($section) ? 'Edit Section' : 'Add Section' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
                    <li class="breadcrumb-item active">{{ isset($section) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-grid me-2"></i>Section Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($section) ? route('admin.sections.update', $section->id) : route('admin.sections.store') }}">
                        @csrf
                        @if(isset($section))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $section->name ?? '') }}" placeholder="e.g., A, B, C" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $section->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class Teacher</label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">Select Teacher</option>
                                @foreach($teachers ?? [] as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('class_teacher_id', $section->class_teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $section->capacity ?? '') }}" placeholder="Maximum students">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $section->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ isset($section) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
