@extends('layouts.app')

@section('title', 'Parent Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Parent Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.parents.index') }}">Parents</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.parents.edit', $parent->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.parents.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                        <i class="bi bi-person fs-1 text-info"></i>
                    </div>
                    <h4 class="mb-1">{{ $parent->father_name ?? 'Parent Name' }}</h4>
                    <p class="text-muted mb-2">{{ $parent->email ?? 'email@example.com' }}</p>
                    <span class="badge {{ ($parent->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($parent->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-envelope me-2"></i>Contact Info
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $parent->email ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <strong>{{ $parent->phone ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Address</small>
                        <strong>{{ $parent->address ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Family Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Father's Name</small>
                            <strong>{{ $parent->father_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Father's Occupation</small>
                            <strong>{{ $parent->father_occupation ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Mother's Name</small>
                            <strong>{{ $parent->mother_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Mother's Occupation</small>
                            <strong>{{ $parent->mother_occupation ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Guardian Name</small>
                            <strong>{{ $parent->guardian_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Relation with Guardian</small>
                            <strong>{{ $parent->guardian_relation ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-mortarboard me-2"></i>Children
                </div>
                <div class="card-body">
                    <p class="text-muted">No children linked to this parent yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
