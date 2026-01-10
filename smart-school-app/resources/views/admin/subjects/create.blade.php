@extends('layouts.app')

@section('title', isset($subject) ? 'Edit Subject' : 'Add Subject')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($subject) ? 'Edit Subject' : 'Add Subject' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item active">{{ isset($subject) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Subject Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($subject) ? route('admin.subjects.update', $subject->id) : route('admin.subjects.store') }}">
                        @csrf
                        @if(isset($subject))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject Code</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code ?? '') }}" placeholder="e.g., MATH, ENG, SCI">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="theory" {{ old('type', $subject->type ?? '') == 'theory' ? 'selected' : '' }}>Theory</option>
                                <option value="practical" {{ old('type', $subject->type ?? '') == 'practical' ? 'selected' : '' }}>Practical</option>
                                <option value="both" {{ old('type', $subject->type ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $subject->description ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $subject->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ isset($subject) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
