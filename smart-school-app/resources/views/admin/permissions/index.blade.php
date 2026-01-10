@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Permissions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Permission
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
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search permissions..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="module" class="form-select">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($groupedPermissions as $module => $modulePermissions)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold">
                            <i class="bi bi-folder me-2"></i>{{ ucfirst($module) }}
                        </span>
                        <span class="badge bg-primary">{{ $modulePermissions->count() }}</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($modulePermissions as $permission)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-key text-muted me-2"></i>
                                        {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-key fs-1 d-block mb-2"></i>
                            <p class="mb-2">No permissions found</p>
                            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> Add First Permission
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
