{{-- Transport Vehicles List View --}}
{{-- Prompt 225: Transport vehicles listing page with CRUD operations --}}

@extends('layouts.app')

@section('title', 'Transport Vehicles')

@section('content')
<div x-data="transportVehiclesManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Transport Vehicles</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transport</a></li>
                    <li class="breadcrumb-item active">Vehicles</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.vehicles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Vehicle
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

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-bus-front fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ count($vehicles ?? []) }}</h3>
                    <small class="text-muted">Total Vehicles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($vehicles ?? [])->where('is_active', true)->count() }}</h3>
                    <small class="text-muted">Active Vehicles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-people fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($vehicles ?? [])->sum('capacity') }}</h3>
                    <small class="text-muted">Total Capacity</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-person-check fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($vehicles ?? [])->sum('students_count') }}</h3>
                    <small class="text-muted">Assigned Students</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Search by vehicle number, driver..."
                        x-model="filters.search"
                    >
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Route</label>
                <select class="form-select" x-model="filters.route">
                    <option value="">All Routes</option>
                    @foreach($routes ?? [] as $route)
                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select" x-model="filters.status">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="resetFilters()">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-card>

    <!-- Vehicles Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-bus-front me-2"></i>
                    Transport Vehicles
                    <span class="badge bg-primary ms-2">{{ count($vehicles ?? []) }}</span>
                </span>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Vehicle Number</th>
                        <th>Type / Model</th>
                        <th class="text-center">Capacity</th>
                        <th>Driver</th>
                        <th>Route</th>
                        <th class="text-center">Students</th>
                        <th class="text-center">Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles ?? [] as $index => $vehicle)
                        <tr x-show="matchesFilters({{ json_encode([
                            'vehicle_number' => strtolower($vehicle->vehicle_number ?? ''),
                            'driver_name' => strtolower($vehicle->driver_name ?? ''),
                            'route_id' => $vehicle->route_id ?? '',
                            'is_active' => $vehicle->is_active ?? true
                        ]) }})">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px;">
                                        <i class="bi bi-bus-front"></i>
                                    </span>
                                    <div>
                                        <a href="{{ route('transport.vehicles.show', $vehicle->id) }}" class="text-decoration-none fw-medium">
                                            {{ $vehicle->vehicle_number }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $vehicle->vehicle_type ?? '-' }}</span>
                                @if($vehicle->vehicle_model)
                                    <br><small class="text-muted">{{ $vehicle->vehicle_model }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $vehicle->capacity ?? 0 }}</span>
                            </td>
                            <td>
                                @if($vehicle->driver_name)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <div>
                                            <span class="fw-medium">{{ $vehicle->driver_name }}</span>
                                            @if($vehicle->driver_phone)
                                                <br><small class="text-muted">
                                                    <i class="bi bi-telephone me-1"></i>{{ $vehicle->driver_phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No driver assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->route)
                                    <a href="{{ route('transport.routes.show', $vehicle->route_id) }}" class="badge bg-light text-dark text-decoration-none">
                                        {{ $vehicle->route->name ?? 'Route' }}
                                    </a>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $vehicle->students_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if($vehicle->is_active ?? true)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a 
                                        href="{{ route('transport.vehicles.show', $vehicle->id) }}" 
                                        class="btn btn-outline-primary" 
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a 
                                        href="{{ route('transport.vehicles.edit', $vehicle->id) }}" 
                                        class="btn btn-outline-warning" 
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Delete"
                                        @click="confirmDelete({{ $vehicle->id }}, '{{ $vehicle->vehicle_number }}')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-bus-front fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No vehicles found</p>
                                    <a href="{{ route('transport.vehicles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Vehicle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($vehicles) && $vehicles instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries
                </div>
                {{ $vehicles->links() }}
            </div>
        </x-slot>
        @endif
    </x-card>

    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('transport.routes.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-signpost-2 fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="mb-0">Routes</h6>
                    <small class="text-muted">Manage routes</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('transport.students.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-success mb-2 d-block"></i>
                    <h6 class="mb-0">Students</h6>
                    <small class="text-muted">Transport assignments</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('transport.reports') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart fs-1 text-warning mb-2 d-block"></i>
                    <h6 class="mb-0">Reports</h6>
                    <small class="text-muted">Transport analytics</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" x-ref="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the vehicle "<strong x-text="deleteVehicleNumber"></strong>"?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Students assigned to this vehicle will need to be reassigned.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form :action="deleteUrl" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transportVehiclesManager() {
    return {
        filters: {
            search: '',
            route: '',
            status: ''
        },
        deleteVehicleId: null,
        deleteVehicleNumber: '',
        deleteUrl: '',

        matchesFilters(vehicle) {
            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!vehicle.vehicle_number.includes(searchLower) && 
                    !vehicle.driver_name.includes(searchLower)) {
                    return false;
                }
            }

            // Route filter
            if (this.filters.route && vehicle.route_id != this.filters.route) {
                return false;
            }

            // Status filter
            if (this.filters.status === 'active' && !vehicle.is_active) {
                return false;
            }
            if (this.filters.status === 'inactive' && vehicle.is_active) {
                return false;
            }

            return true;
        },

        resetFilters() {
            this.filters = {
                search: '',
                route: '',
                status: ''
            };
        },

        confirmDelete(id, vehicleNumber) {
            this.deleteVehicleId = id;
            this.deleteVehicleNumber = vehicleNumber;
            this.deleteUrl = `/transport/vehicles/${id}`;
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .table th,
[dir="rtl"] .table td {
    text-align: right;
}

[dir="rtl"] .text-center {
    text-align: center !important;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}
</style>
@endpush
