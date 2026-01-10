@extends('layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Add Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($role) ? 'Edit Role' : 'Add Role' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">{{ isset($role) ? 'Edit' : 'Add' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}">
        @csrf
        @if(isset($role))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-shield me-2"></i>Role Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name ?? '') }}" required {{ isset($role) && in_array($role->name, ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian']) ? 'readonly' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($role) && in_array($role->name, ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian']))
                                <small class="text-muted">System roles cannot be renamed.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Display Name</label>
                            <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name', $role->display_name ?? '') }}" placeholder="Human-readable name">
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $role->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ isset($role) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-key me-2"></i>Permissions</span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">Select All</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($permissions ?? [] as $module => $modulePermissions)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border">
                                        <div class="card-header bg-light py-2">
                                            <div class="form-check">
                                                <input class="form-check-input module-checkbox" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                                <label class="form-check-label fw-bold" for="module_{{ $module }}">{{ ucfirst($module) }}</label>
                                            </div>
                                        </div>
                                        <div class="card-body py-2">
                                            @foreach($modulePermissions as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" data-module="{{ $module }}" {{ in_array($permission->name, $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">{{ ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle me-2"></i>No permissions available. Please create permissions first.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    // Select All functionality
    selectAll.addEventListener('change', function() {
        moduleCheckboxes.forEach(cb => cb.checked = this.checked);
        permissionCheckboxes.forEach(cb => cb.checked = this.checked);
    });

    // Module checkbox functionality
    moduleCheckboxes.forEach(moduleCheckbox => {
        moduleCheckbox.addEventListener('change', function() {
            const module = this.dataset.module;
            document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => cb.checked = this.checked);
            updateSelectAllState();
        });
    });

    // Permission checkbox functionality
    permissionCheckboxes.forEach(permissionCheckbox => {
        permissionCheckbox.addEventListener('change', function() {
            const module = this.dataset.module;
            const modulePerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            const checkedPerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
            document.getElementById(`module_${module}`).checked = modulePerms.length === checkedPerms.length;
            updateSelectAllState();
        });
    });

    function updateSelectAllState() {
        const allChecked = document.querySelectorAll('.permission-checkbox:checked').length === permissionCheckboxes.length;
        selectAll.checked = allChecked;
    }

    // Initialize module checkbox states
    moduleCheckboxes.forEach(moduleCheckbox => {
        const module = moduleCheckbox.dataset.module;
        const modulePerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
        const checkedPerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
        moduleCheckbox.checked = modulePerms.length === checkedPerms.length && modulePerms.length > 0;
    });

    updateSelectAllState();
});
</script>
@endpush
@endsection
