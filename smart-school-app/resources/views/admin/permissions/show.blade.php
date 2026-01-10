@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Permission Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-key fs-1 text-info"></i>
                    </div>
                    <h4 class="mb-1">{{ $permission->name }}</h4>
                    <p class="text-muted mb-2">
                        Module: <span class="badge bg-primary">{{ ucfirst(explode('.', $permission->name)[0]) }}</span>
                        Action: <span class="badge bg-secondary">{{ ucfirst(explode('.', $permission->name)[1] ?? '') }}</span>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Permission Info
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th>ID</th>
                            <td>{{ $permission->id }}</td>
                        </tr>
                        <tr>
                            <th>Guard</th>
                            <td>{{ $permission->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $permission->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated</th>
                            <td>{{ $permission->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-shield me-2"></i>Roles with this Permission</span>
                    <span class="badge bg-primary">{{ $roles->count() }} roles</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.permissions.assign-roles', $permission->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            @php
                                $allRoles = \Spatie\Permission\Models\Role::where('guard_name', 'web')->get();
                                $assignedRoleIds = $roles->pluck('id')->toArray();
                            @endphp
                            
                            @foreach($allRoles as $role)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ in_array($role->id, $assignedRoleIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->display_name ?? ucfirst($role->name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
