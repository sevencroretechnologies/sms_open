@extends('layouts.app')

@section('title', isset($class) ? 'Edit Class' : 'Add Class')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($class) ? 'Edit Class' : 'Add Class' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">{{ isset($class) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-building me-2"></i>Class Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($class) ? route('admin.classes.update', $class->id) : route('admin.classes.store') }}">
                        @csrf
                        @if(isset($class))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $class->name ?? '') }}" placeholder="e.g., Class 1, Grade 10" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach($academicSessions ?? [] as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id', $class->academic_session_id ?? ($currentSession->id ?? '')) == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Numeric Name</label>
                            <input type="number" name="numeric_name" class="form-control" value="{{ old('numeric_name', $class->numeric_name ?? '') }}" placeholder="e.g., 1, 2, 10">
                            <small class="text-muted">Used for sorting and ordering classes</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Order Index</label>
                            <input type="number" name="order_index" class="form-control" value="{{ old('order_index', $class->order_index ?? ($maxOrderIndex ?? 0) + 1) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $class->description ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $class->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ isset($class) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
