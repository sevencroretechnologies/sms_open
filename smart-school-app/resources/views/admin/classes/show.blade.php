@extends('layouts.app')

@section('title', 'Class Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Class Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.classes.edit', $class->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-building fs-1 text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $class->name ?? 'Class Name' }}</h4>
                    <span class="badge {{ ($class->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($class->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['sections_count'] ?? 0 }}</h3>
                            <small class="text-muted">Sections</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['students_count'] ?? 0 }}</h3>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['subjects_count'] ?? 0 }}</h3>
                            <small class="text-muted">Subjects</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-grid me-2"></i>Sections
                </div>
                <div class="card-body">
                    <p class="text-muted">No sections assigned to this class yet.</p>
                    <a href="{{ route('admin.sections.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Section
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
