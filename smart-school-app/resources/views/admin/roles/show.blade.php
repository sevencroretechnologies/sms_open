{{-- Role Show View --}}
{{-- Admin role details --}}

@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Role Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.roles.permissions', $role ?? 1) }}" class="btn btn-info">
                <i class="bi bi-key me-1"></i> Permissions
            </a>
            <a href="{{ route('admin.roles.edit', $role ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Role Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $role->name ?? 'Role Name' }}</h4>
                    <span class="badge bg-light text-dark">{{ $role->guard_name ?? 'web' }}</span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Permissions</span>
                        <span class="badge bg-info">{{ $role->permissions_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Users</span>
                        <span>{{ $role->users_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($role->created_at) ? $role->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>

            <!-- Description -->
            @if($role->description ?? false)
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Description
                </x-slot>
                <p class="mb-0">{{ $role->description }}</p>
            </x-card>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Permissions -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-key me-2"></i>Assigned Permissions</span>
                        <a href="{{ route('admin.roles.permissions', $role ?? 1) }}" class="btn btn-sm btn-outline-primary">
                            Manage
                        </a>
                    </div>
                </x-slot>

                <div class="d-flex flex-wrap gap-2">
                    @forelse($role->permissions ?? [] as $permission)
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $permission->name }}</span>
                    @empty
                        <p class="text-muted mb-0">No permissions assigned</p>
                    @endforelse
                </div>
            </x-card>

            <!-- Users with this Role -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-people me-2"></i>
                    Users with this Role
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($role->users ?? [] as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No users have this role
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
