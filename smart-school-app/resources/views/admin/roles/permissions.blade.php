@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Role Permissions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.show', $role->id) }}">{{ $role->display_name ?? ucfirst($role->name) }}</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-secondary">
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

    <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-shield me-2"></i>
                    <strong>{{ $role->display_name ?? ucfirst($role->name) }}</strong> - Permissions
                </span>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Select All</label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($allPermissions ?? [] as $module => $modulePermissions)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border">
                                <div class="card-header bg-light py-2">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                        <label class="form-check-label fw-bold" for="module_{{ $module }}">{{ ucfirst($module) }}</label>
                                        <span class="badge bg-secondary ms-1">{{ $modulePermissions->count() }}</span>
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

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Permissions
            </button>
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
