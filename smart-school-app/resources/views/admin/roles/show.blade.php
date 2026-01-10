@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Role Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.roles.permissions', $role->id ?? 0) }}" class="btn btn-info">
                <i class="bi bi-key me-1"></i> Permissions
            </a>
            <a href="{{ route('admin.roles.edit', $role->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield fs-1 text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $role->name ?? 'Role Name' }}</h4>
                    <p class="text-muted mb-2">{{ $role->slug ?? 'role-slug' }}</p>
                    <span class="badge {{ ($role->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($role->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['users_count'] ?? 0 }}</h3>
                            <small class="text-muted">Users with this Role</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $statistics['permissions_count'] ?? 0 }}</h3>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Role Information
                </div>
                <div class="card-body">
                    <p>{{ $role->description ?? 'No description available.' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Users with this Role
                </div>
                <div class="card-body">
                    <p class="text-muted">No users assigned to this role yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
