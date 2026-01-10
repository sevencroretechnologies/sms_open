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
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id ?? 0) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield me-2"></i>{{ $role->name ?? 'Role' }} - Permissions</span>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Select All</label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $modules = [
                            'students' => ['view', 'create', 'edit', 'delete', 'export'],
                            'teachers' => ['view', 'create', 'edit', 'delete', 'export'],
                            'parents' => ['view', 'create', 'edit', 'delete'],
                            'classes' => ['view', 'create', 'edit', 'delete'],
                            'sections' => ['view', 'create', 'edit', 'delete'],
                            'subjects' => ['view', 'create', 'edit', 'delete'],
                            'exams' => ['view', 'create', 'edit', 'delete', 'marks'],
                            'attendance' => ['view', 'create', 'edit', 'report'],
                            'fees' => ['view', 'create', 'edit', 'delete', 'collect'],
                            'library' => ['view', 'create', 'edit', 'delete', 'issue'],
                            'transport' => ['view', 'create', 'edit', 'delete'],
                            'hostel' => ['view', 'create', 'edit', 'delete'],
                            'reports' => ['view', 'export'],
                            'settings' => ['view', 'edit'],
                            'users' => ['view', 'create', 'edit', 'delete'],
                            'roles' => ['view', 'create', 'edit', 'delete'],
                        ];
                    @endphp

                    @foreach($modules as $module => $actions)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                        <label class="form-check-label fw-bold" for="module_{{ $module }}">{{ ucfirst($module) }}</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach($actions as $action)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $module }}.{{ $action }}" id="perm_{{ $module }}_{{ $action }}" data-module="{{ $module }}">
                                            <label class="form-check-label" for="perm_{{ $module }}_{{ $action }}">{{ ucfirst($action) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Permissions
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.permission-checkbox, .module-checkbox').forEach(cb => cb.checked = this.checked);
});

document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
    moduleCheckbox.addEventListener('change', function() {
        const module = this.dataset.module;
        document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => cb.checked = this.checked);
    });
});
</script>
@endpush
@endsection
