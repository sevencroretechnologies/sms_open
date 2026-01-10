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
            <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-info">
                <i class="bi bi-key me-1"></i> Permissions
            </a>
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
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
                    <h4 class="mb-1">{{ $role->display_name ?? ucfirst($role->name) }}</h4>
                    <p class="text-muted mb-2"><code>{{ $role->name }}</code></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Role Information
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th>ID</th>
                            <td>{{ $role->id }}</td>
                        </tr>
                        <tr>
                            <th>Guard</th>
                            <td>{{ $role->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $role->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated</th>
                            <td>{{ $role->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
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
                    <i class="bi bi-file-text me-2"></i>Description
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $role->description ?? 'No description available.' }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-key me-2"></i>Assigned Permissions</span>
                    <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Permissions
                    </a>
                </div>
                <div class="card-body">
                    @if($permissions->count() > 0)
                        <div class="row">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="col-md-4 mb-3">
                                    <h6 class="text-primary mb-2">{{ ucfirst($module) }}</h6>
                                    @foreach($modulePermissions as $permission)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ explode('.', $permission->name)[1] ?? $permission->name }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No permissions assigned to this role.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people me-2"></i>Users with this Role</span>
                    <span class="badge bg-primary">{{ $users->total() }} users</span>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($users->hasPages())
                            <div class="mt-3">
                                {{ $users->links() }}
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No users assigned to this role yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
