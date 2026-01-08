{{-- Transport Vehicles Create View --}}
{{-- Prompt 226: Transport vehicle creation form --}}

@extends('layouts.app')

@section('title', 'Add Transport Vehicle')

@section('content')
<div x-data="transportVehicleCreate()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Add Transport Vehicle</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.vehicles.index') }}">Transport Vehicles</a></li>
                    <li class="breadcrumb-item active">Add Vehicle</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.vehicles.index') }}" class="btn btn-outline-secondary">
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
            <!-- Vehicle Form -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-bus-front me-2"></i>
                    Vehicle Information
                </x-slot>

                <form action="{{ route('transport.vehicles.store') }}" method="POST" @submit="submitting = true" x-ref="vehicleForm">
                    @csrf
                    <input type="hidden" name="save_and_assign" x-model="saveAndAssign">
                    
                    <div class="row g-3">
                        <!-- Vehicle Number -->
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="vehicle_number"
                                class="form-control font-monospace @error('vehicle_number') is-invalid @enderror"
                                x-model="form.vehicle_number"
                                value="{{ old('vehicle_number') }}"
                                required
                                placeholder="e.g., KA-01-AB-1234"
                                maxlength="20"
                                style="text-transform: uppercase;"
                            >
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique registration number of the vehicle</div>
                        </div>

                        <!-- Vehicle Type -->
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Type</label>
                            <select 
                                name="vehicle_type" 
                                class="form-select @error('vehicle_type') is-invalid @enderror"
                                x-model="form.vehicle_type"
                            >
                                <option value="">Select Type</option>
                                <option value="Bus" {{ old('vehicle_type') == 'Bus' ? 'selected' : '' }}>Bus</option>
                                <option value="Mini Bus" {{ old('vehicle_type') == 'Mini Bus' ? 'selected' : '' }}>Mini Bus</option>
                                <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van</option>
                                <option value="Car" {{ old('vehicle_type') == 'Car' ? 'selected' : '' }}>Car</option>
                                <option value="Other" {{ old('vehicle_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vehicle Model -->
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Model</label>
                            <input 
                                type="text" 
                                name="vehicle_model"
                                class="form-control @error('vehicle_model') is-invalid @enderror"
                                x-model="form.vehicle_model"
                                value="{{ old('vehicle_model') }}"
                                placeholder="e.g., Tata Starbus, Force Traveller"
                                maxlength="100"
                            >
                            @error('vehicle_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div class="col-md-6">
                            <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="capacity"
                                class="form-control @error('capacity') is-invalid @enderror"
                                x-model="form.capacity"
                                value="{{ old('capacity') }}"
                                required
                                min="1"
                                max="100"
                                placeholder="e.g., 40"
                            >
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum number of students this vehicle can carry</div>
                        </div>

                        <!-- Route -->
                        <div class="col-md-6">
                            <label class="form-label">Assigned Route</label>
                            <select 
                                name="route_id" 
                                class="form-select @error('route_id') is-invalid @enderror"
                                x-model="form.route_id"
                            >
                                <option value="">Select Route (Optional)</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }} ({{ $route->route_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        </div>
                    </div>

                    <!-- Driver Information Section -->
                    <h6 class="mt-4 mb-3 border-bottom pb-2">
                        <i class="bi bi-person me-2"></i>
                        Driver Information
                    </h6>

                    <div class="row g-3">
                        <!-- Driver Name -->
                        <div class="col-md-6">
                            <label class="form-label">Driver Name</label>
                            <input 
                                type="text" 
                                name="driver_name"
                                class="form-control @error('driver_name') is-invalid @enderror"
                                x-model="form.driver_name"
                                value="{{ old('driver_name') }}"
                                placeholder="Enter driver's full name"
                                maxlength="100"
                            >
                            @error('driver_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Driver Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Driver Phone</label>
                            <input 
                                type="tel" 
                                name="driver_phone"
                                class="form-control @error('driver_phone') is-invalid @enderror"
                                x-model="form.driver_phone"
                                value="{{ old('driver_phone') }}"
                                placeholder="e.g., +91 9876543210"
                                maxlength="20"
                            >
                            @error('driver_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Driver License -->
                        <div class="col-md-6">
                            <label class="form-label">Driver License Number</label>
                            <input 
                                type="text" 
                                name="driver_license"
                                class="form-control font-monospace @error('driver_license') is-invalid @enderror"
                                x-model="form.driver_license"
                                value="{{ old('driver_license') }}"
                                placeholder="e.g., KA0120210012345"
                                maxlength="30"
                                style="text-transform: uppercase;"
                            >
                            @error('driver_license')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('transport.vehicles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-outline-primary" :disabled="submitting" @click="saveAndAssign = '0'">
                            <span x-show="!submitting || saveAndAssign === '1'">
                                <i class="bi bi-check-lg me-1"></i> Save Vehicle
                            </span>
                            <span x-show="submitting && saveAndAssign === '0'">
                                <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                            </span>
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="submitting" @click="saveAndAssign = '1'">
                            <span x-show="!submitting || saveAndAssign === '0'">
                                <i class="bi bi-people me-1"></i> Save & Assign Students
                            </span>
                            <span x-show="submitting && saveAndAssign === '1'">
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
                        <i class="bi bi-bus-front fs-3"></i>
                    </div>
                    <h5 class="mb-1 font-monospace" x-text="form.vehicle_number || 'VEHICLE#'"></h5>
                    <p class="mb-2">
                        <span class="badge bg-light text-dark" x-text="form.vehicle_type || 'Type'"></span>
                        <span class="badge bg-light text-dark" x-text="form.vehicle_model || 'Model'"></span>
                    </p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span 
                            class="badge"
                            :class="form.is_active == '1' ? 'bg-success' : 'bg-danger'"
                            x-text="form.is_active == '1' ? 'Active' : 'Inactive'"
                        ></span>
                        <span class="badge bg-info">
                            <i class="bi bi-people me-1"></i>
                            <span x-text="form.capacity || '0'"></span> seats
                        </span>
                    </div>
                </div>

                <!-- Driver Preview -->
                <div class="border-top pt-3 mt-3" x-show="form.driver_name">
                    <h6 class="small text-muted mb-2">Driver</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary" style="width: 40px; height: 40px;">
                            <i class="bi bi-person"></i>
                        </span>
                        <div>
                            <span class="fw-medium" x-text="form.driver_name"></span>
                            <br>
                            <small class="text-muted" x-text="form.driver_phone || 'No phone'"></small>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Vehicle Types Reference -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Vehicle Types
                </x-slot>

                <div class="small">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-bus-front text-primary me-2"></i>
                            <div>
                                <span class="fw-medium">Bus</span>
                                <br><small class="text-muted">40-60 seats</small>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-bus-front text-info me-2"></i>
                            <div>
                                <span class="fw-medium">Mini Bus</span>
                                <br><small class="text-muted">20-30 seats</small>
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-truck me-2 text-success"></i>
                            <div>
                                <span class="fw-medium">Van</span>
                                <br><small class="text-muted">10-15 seats</small>
                            </div>
                        </li>
                        <li class="mb-0 d-flex align-items-center">
                            <i class="bi bi-car-front me-2 text-warning"></i>
                            <div>
                                <span class="fw-medium">Car</span>
                                <br><small class="text-muted">4-7 seats</small>
                            </div>
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
function transportVehicleCreate() {
    return {
        submitting: false,
        saveAndAssign: '0',
        form: {
            vehicle_number: '{{ old('vehicle_number', '') }}',
            vehicle_type: '{{ old('vehicle_type', '') }}',
            vehicle_model: '{{ old('vehicle_model', '') }}',
            capacity: '{{ old('capacity', '') }}',
            route_id: '{{ old('route_id', '') }}',
            driver_name: '{{ old('driver_name', '') }}',
            driver_phone: '{{ old('driver_phone', '') }}',
            driver_license: '{{ old('driver_license', '') }}',
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
