{{-- Role Permissions View --}}
{{-- Admin role permissions management --}}

@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Role Permissions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.show', $role ?? 1) }}">{{ $role->name ?? 'Role' }}</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.roles.show', $role ?? 1) }}" class="btn btn-outline-secondary">
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

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.roles.permissions.update', $role ?? 1) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    <x-slot name="header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <span>
                                <i class="bi bi-key me-2"></i>
                                Manage Permissions for {{ $role->name ?? 'Role' }}
                            </span>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleAll()">
                                <label class="form-check-label" for="selectAll">Select All</label>
                            </div>
                        </div>
                    </x-slot>
                    
                    @php
                        $groupedPermissions = collect($permissions ?? [])->groupBy(function($permission) {
                            return explode('.', $permission->name)[0] ?? 'general';
                        });
                    @endphp

                    @forelse($groupedPermissions as $group => $perms)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted mb-3">
                                <i class="bi bi-folder me-2"></i>{{ ucfirst($group) }}
                            </h6>
                            <div class="row g-2">
                                @foreach($perms as $permission)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}" 
                                                   class="form-check-input permission-checkbox"
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, $rolePermissions ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-key fs-1 d-block mb-2"></i>
                            <p class="mb-0">No permissions available</p>
                        </div>
                    @endforelse

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.show', $role ?? 1) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Permissions
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </div>

            <div class="col-lg-4">
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-shield me-2"></i>
                        Role Info
                    </x-slot>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 50px; height: 50px;">
                            <i class="bi bi-shield fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $role->name ?? 'Role Name' }}</h6>
                            <small class="text-muted">{{ $role->guard_name ?? 'web' }}</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Current Permissions</span>
                            <span class="badge bg-info">{{ count($rolePermissions ?? []) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Users</span>
                            <span>{{ $role->users_count ?? 0 }}</span>
                        </li>
                    </ul>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-lightbulb me-2"></i>
                        Quick Tips
                    </x-slot>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Select permissions for this role
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Changes apply to all users with this role
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use "Select All" for full access
                        </li>
                    </ul>
                </x-card>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleAll() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const selectAll = document.getElementById('selectAll');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            selectAll.checked = allChecked;
        });
    });
});
</script>
@endpush
