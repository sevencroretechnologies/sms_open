@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Roles & Permissions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-info">
                <i class="bi bi-key me-1"></i> Manage Permissions
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Role
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

    <div class="card mb-4">
        <div class="card-header">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search roles..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-shield-lock me-2"></i>Roles List
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                        <tr>
                            <td>{{ $roles->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 rounded p-2">
                                        <i class="bi bi-shield text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $role->display_name ?? ucfirst($role->name) }}</span>
                                        <br><small class="text-muted">{{ $role->name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($role->description ?? '-', 50) }}</td>
                            <td><span class="badge bg-info">{{ $role->users_count ?? 0 }}</span></td>
                            <td><span class="badge bg-secondary">{{ $role->permissions_count ?? 0 }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-outline-info" title="Permissions">
                                        <i class="bi bi-key"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(!in_array($role->name, ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian']))
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-shield-lock fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No roles found</p>
                                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Role
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
            <div class="card-footer">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
