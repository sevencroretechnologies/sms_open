{{-- Transport Routes Create View --}}
{{-- Prompt 223: Transport route creation form --}}

@extends('layouts.app')

@section('title', 'Add Transport Route')

@section('content')
<div x-data="transportRouteCreate()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Add Transport Route</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.routes.index') }}">Transport Routes</a></li>
                    <li class="breadcrumb-item active">Add Route</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.routes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Route Form -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-signpost-2 me-2"></i>
                    Route Information
                </x-slot>

                <form action="{{ route('transport.routes.store') }}" method="POST" @submit="submitting = true" x-ref="routeForm">
                    @csrf
                    <input type="hidden" name="save_and_add_stops" x-model="saveAndAddStops">
                    
                    <div class="row g-3">
                        <!-- Route Name -->
                        <div class="col-md-6">
                            <label class="form-label">Route Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                x-model="form.name"
                                value="{{ old('name') }}"
                                required
                                placeholder="e.g., North City Route, Downtown Express"
                                maxlength="100"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter a descriptive name for this route</div>
                        </div>

                        <!-- Route Number -->
                        <div class="col-md-6">
                            <label class="form-label">Route Number <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="route_number"
                                class="form-control font-monospace @error('route_number') is-invalid @enderror"
                                x-model="form.route_number"
                                value="{{ old('route_number') }}"
                                required
                                placeholder="e.g., R001, RT-101"
                                maxlength="20"
                                style="text-transform: uppercase;"
                            >
                            @error('route_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique identifier for this route</div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea 
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                x-model="form.description"
                                rows="3"
                                placeholder="Enter a brief description of this route, including areas covered..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional description of the route coverage area</div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select 
                                name="is_active" 
                                class="form-select @error('is_active') is-invalid @enderror"
                                x-model="form.is_active"
                            >
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Only active routes can have students assigned</div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('transport.routes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-outline-primary" :disabled="submitting" @click="saveAndAddStops = '0'">
                            <span x-show="!submitting || saveAndAddStops === '1'">
                                <i class="bi bi-check-lg me-1"></i> Save Route
                            </span>
                            <span x-show="submitting && saveAndAddStops === '0'">
                                <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="submitting" @click="saveAndAddStops = '1'">
                            <span x-show="!submitting || saveAndAddStops === '0'">
                                <i class="bi bi-geo-alt me-1"></i> Save & Add Stops
                            </span>
                            <span x-show="submitting && saveAndAddStops === '1'">
                                <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-lg-4">
            <!-- Preview Card -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-eye me-2"></i>
                    Preview
                </x-slot>

                <div class="text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-signpost-2 fs-3"></i>
                    </div>
                    <h5 class="mb-1" x-text="form.name || 'Route Name'"></h5>
                    <p class="mb-2">
                        <span class="badge bg-light text-dark font-monospace" x-text="form.route_number || 'ROUTE#'"></span>
                    </p>
                    <p class="text-muted small mb-3" x-text="form.description || 'No description'"></p>
                    <div class="d-flex justify-content-center gap-2">
                        <span 
                            class="badge"
                            :class="form.is_active == '1' ? 'bg-success' : 'bg-danger'"
                            x-text="form.is_active == '1' ? 'Active' : 'Inactive'"
                        ></span>
                    </div>
                </div>

                <!-- Route Stats Preview -->
                <div class="border-top pt-3 mt-3">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="text-muted small">Stops</div>
                            <div class="fw-bold">0</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Vehicles</div>
                            <div class="fw-bold">0</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Students</div>
                            <div class="fw-bold">0</div>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Tips Card -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips
                </x-slot>

                <div class="small">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <span>Use descriptive route names that indicate the area covered</span>
                        </li>
                        <li class="mb-2 d-flex">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <span>Route numbers should be unique and easy to remember</span>
                        </li>
                        <li class="mb-2 d-flex">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <span>Add stops after creating the route to define pickup points</span>
                        </li>
                        <li class="mb-0 d-flex">
                            <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                            <span>Assign vehicles to routes for efficient transport management</span>
                        </li>
                    </ul>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transportRouteCreate() {
    return {
        submitting: false,
        saveAndAddStops: '0',
        form: {
            name: '{{ old('name', '') }}',
            route_number: '{{ old('route_number', '') }}',
            description: '{{ old('description', '') }}',
            is_active: '{{ old('is_active', '1') }}'
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .form-check {
    padding-right: 1.5em;
    padding-left: 0;
}

[dir="rtl"] .form-check-input {
    float: right;
    margin-right: -1.5em;
    margin-left: 0;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}
</style>
@endpush
