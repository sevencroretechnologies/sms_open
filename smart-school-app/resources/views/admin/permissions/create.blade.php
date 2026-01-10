@extends('layouts.app')

@section('title', isset($permission) ? 'Edit Permission' : 'Add Permission')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($permission) ? 'Edit Permission' : 'Add Permission' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                    <li class="breadcrumb-item active">{{ isset($permission) ? 'Edit' : 'Add' }}</li>
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

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-key me-2"></i>Permission Details
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($permission) ? route('admin.permissions.update', $permission->id) : route('admin.permissions.store') }}">
                        @csrf
                        @if(isset($permission))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" style="max-width: 150px;" id="moduleSelect">
                                    <option value="">Module</option>
                                    @foreach($modules ?? [] as $module)
                                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                                    @endforeach
                                    <option value="custom">Custom...</option>
                                </select>
                                <span class="input-group-text">.</span>
                                <input type="text" id="actionInput" class="form-control" placeholder="action (e.g., view, create)">
                            </div>
                            <input type="hidden" name="name" id="permissionName" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: module.action (e.g., students.view, fees.collect)</small>
                        </div>

                        <div class="mb-3" id="customModuleGroup" style="display: none;">
                            <label class="form-label">Custom Module Name</label>
                            <input type="text" id="customModule" class="form-control" placeholder="Enter custom module name">
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Permission Naming Convention:</strong>
                            <ul class="mb-0 mt-2">
                                <li><code>module.view</code> - View records</li>
                                <li><code>module.create</code> - Create new records</li>
                                <li><code>module.edit</code> - Edit existing records</li>
                                <li><code>module.delete</code> - Delete records</li>
                                <li><code>module.export</code> - Export data</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ isset($permission) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('moduleSelect');
    const actionInput = document.getElementById('actionInput');
    const permissionName = document.getElementById('permissionName');
    const customModuleGroup = document.getElementById('customModuleGroup');
    const customModule = document.getElementById('customModule');

    function updatePermissionName() {
        let module = moduleSelect.value;
        if (module === 'custom') {
            module = customModule.value.toLowerCase().replace(/[^a-z]/g, '');
        }
        const action = actionInput.value.toLowerCase().replace(/[^a-z]/g, '');
        
        if (module && action) {
            permissionName.value = module + '.' + action;
        } else if (module) {
            permissionName.value = module + '.';
        } else if (action) {
            permissionName.value = '.' + action;
        } else {
            permissionName.value = '';
        }
    }

    moduleSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customModuleGroup.style.display = 'block';
        } else {
            customModuleGroup.style.display = 'none';
        }
        updatePermissionName();
    });

    actionInput.addEventListener('input', updatePermissionName);
    customModule.addEventListener('input', updatePermissionName);

    // Pre-populate if editing
    const existingValue = permissionName.value;
    if (existingValue) {
        const parts = existingValue.split('.');
        if (parts.length === 2) {
            const moduleOptions = Array.from(moduleSelect.options).map(o => o.value);
            if (moduleOptions.includes(parts[0])) {
                moduleSelect.value = parts[0];
            } else {
                moduleSelect.value = 'custom';
                customModuleGroup.style.display = 'block';
                customModule.value = parts[0];
            }
            actionInput.value = parts[1];
        }
    }
});
</script>
@endpush
@endsection
