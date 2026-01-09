{{-- Class Edit View --}}
{{-- Admin class edit form --}}

@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Class</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.classes.show', $class ?? 1) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i> View
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
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
                    <i class="bi bi-mortarboard me-2"></i>
                    Class Details
                </x-slot>
                
                <form action="{{ route('admin.classes.update', $class ?? 1) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $class->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numeric Name</label>
                            <input type="number" name="numeric_name" class="form-control @error('numeric_name') is-invalid @enderror" value="{{ old('numeric_name', $class->numeric_name ?? '') }}">
                            @error('numeric_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $class->order ?? 0) }}" min="0">
                            @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $class->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $class->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $class->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Class
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Class Info
                </x-slot>
                
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sections</span>
                        <span>{{ $class->sections_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Students</span>
                        <span>{{ $class->students_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Subjects</span>
                        <span>{{ $class->subjects_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($class->created_at) ? $class->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.sections.create', ['class_id' => $class->id ?? 1]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Add Section
                    </a>
                    <a href="{{ route('admin.classes.assign-subjects', $class ?? 1) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-book me-1"></i> Assign Subjects
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
