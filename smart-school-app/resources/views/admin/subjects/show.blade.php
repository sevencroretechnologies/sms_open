@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Subject Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.subjects.edit', $subject->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-book fs-1 text-info"></i>
                    </div>
                    <h4 class="mb-1">{{ $subject->name ?? 'Subject Name' }}</h4>
                    <p class="text-muted mb-2">{{ $subject->code ?? 'No Code' }}</p>
                    <span class="badge {{ ($subject->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($subject->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['classes_count'] ?? 0 }}</h3>
                            <small class="text-muted">Classes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['teachers_count'] ?? 0 }}</h3>
                            <small class="text-muted">Teachers</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ ucfirst($subject->type ?? 'theory') }}</h3>
                            <small class="text-muted">Type</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Subject Information
                </div>
                <div class="card-body">
                    <p>{{ $subject->description ?? 'No description available.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
