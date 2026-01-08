{{-- Transport Route Details View --}}
{{-- Prompt 128: Transport route details view with stops, vehicles, and students --}}

@extends('layouts.app')

@section('title', 'Route Details - ' . ($route->name ?? 'Route'))

@section('content')
<div x-data="transportRouteDetails()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $route->name ?? 'Route Details' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.routes.index') }}">Transport Routes</a></li>
                    <li class="breadcrumb-item active">{{ $route->name ?? 'Route' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="printRoute()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            <a href="{{ route('transport.routes.stops', $route->id ?? 1) }}" class="btn btn-outline-info">
                <i class="bi bi-geo-alt me-1"></i> Manage Stops
            </a>
            <a href="{{ route('transport.routes.edit', $route->id ?? 1) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-outline-danger" @click="confirmDelete()">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
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

    <div class="row">
        <div class="col-lg-4">
            <!-- Route Details Card -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-signpost-2 me-2"></i>
                    Route Information
                </x-slot>

                <div class="text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-signpost-2 fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $route->name ?? 'Route Name' }}</h4>
                    <p class="mb-2">
                        <span class="badge bg-light text-dark font-monospace fs-6">{{ $route->route_number ?? 'R001' }}</span>
                    </p>
                    @if($route->is_active ?? true)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>

                @if($route->description)
                <div class="border-top pt-3 mt-3">
                    <h6 class="small text-muted mb-2">Description</h6>
                    <p class="mb-0">{{ $route->description }}</p>
                </div>
                @endif

                <div class="border-top pt-3 mt-3">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-primary">{{ count($stops ?? []) }}</h4>
                                <small class="text-muted">Stops</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-success">{{ count($vehicles ?? []) }}</h4>
                                <small class="text-muted">Vehicles</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-info">{{ count($students ?? []) }}</h4>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Fees:</span>
                        <span class="fw-bold text-success">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($students ?? [])->sum('transport_fees'), 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Created:</span>
                        <span>{{ $route->created_at ? $route->created_at->format('M d, Y') : '-' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>

                <div class="d-grid gap-2">
                    <a href="{{ route('transport.routes.stops', $route->id ?? 1) }}" class="btn btn-outline-info">
                        <i class="bi bi-plus-lg me-1"></i> Add Stop
                    </a>
                    <a href="{{ route('transport.vehicles.create', ['route_id' => $route->id ?? 1]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-bus-front me-1"></i> Add Vehicle
                    </a>
                    <a href="{{ route('transport.assign', ['route_id' => $route->id ?? 1]) }}" class="btn btn-outline-success">
                        <i class="bi bi-people me-1"></i> Assign Students
                    </a>
                    <button type="button" class="btn btn-outline-secondary" @click="exportRoute()">
                        <i class="bi bi-download me-1"></i> Export Route
                    </button>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="stops-tab" data-bs-toggle="tab" data-bs-target="#stops" type="button" role="tab">
                        <i class="bi bi-geo-alt me-1"></i> Stops ({{ count($stops ?? []) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehicles" type="button" role="tab">
                        <i class="bi bi-bus-front me-1"></i> Vehicles ({{ count($vehicles ?? []) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> Students ({{ count($students ?? []) }})
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Stops Tab -->
                <div class="tab-pane fade show active" id="stops" role="tabpanel">
                    <x-card :noPadding="true">
                        <x-slot name="header">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>Route Stops</span>
                                <a href="{{ route('transport.routes.stops', $route->id ?? 1) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Add Stop
                                </a>
                            </div>
                        </x-slot>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">Order</th>
                                        <th>Stop Name</th>
                                        <th class="text-center">Time</th>
                                        <th class="text-center">Fare</th>
                                        <th class="text-center">Students</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stops ?? [] as $stop)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary rounded-pill">{{ $stop->stop_order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-info bg-opacity-10 text-info" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-geo-alt"></i>
                                                    </span>
                                                    <span class="fw-medium">{{ $stop->stop_name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($stop->stop_time)
                                                    <span class="badge bg-light text-dark">
                                                        {{ \Carbon\Carbon::parse($stop->stop_time)->format('h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">
                                                    {{ config('app.currency_symbol', '$') }}{{ number_format($stop->fare ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $stop->students_count ?? 0 }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-geo-alt d-block mb-2 fs-4"></i>
                                                    No stops added yet
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>

                <!-- Vehicles Tab -->
                <div class="tab-pane fade" id="vehicles" role="tabpanel">
                    <x-card :noPadding="true">
                        <x-slot name="header">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>Assigned Vehicles</span>
                                <a href="{{ route('transport.vehicles.create', ['route_id' => $route->id ?? 1]) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Add Vehicle
                                </a>
                            </div>
                        </x-slot>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th class="text-center">Capacity</th>
                                        <th>Driver</th>
                                        <th class="text-center">Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles ?? [] as $vehicle)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                                        <i class="bi bi-bus-front"></i>
                                                    </span>
                                                    <a href="{{ route('transport.vehicles.show', $vehicle->id) }}" class="text-decoration-none fw-medium">
                                                        {{ $vehicle->vehicle_number }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $vehicle->vehicle_type ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $vehicle->capacity ?? 0 }}</span>
                                            </td>
                                            <td>
                                                @if($vehicle->driver_name)
                                                    <span class="fw-medium">{{ $vehicle->driver_name }}</span>
                                                    @if($vehicle->driver_phone)
                                                        <br><small class="text-muted">{{ $vehicle->driver_phone }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No driver</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $vehicle->students_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('transport.vehicles.show', $vehicle->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-bus-front d-block mb-2 fs-4"></i>
                                                    No vehicles assigned yet
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>

                <!-- Students Tab -->
                <div class="tab-pane fade" id="students" role="tabpanel">
                    <x-card :noPadding="true">
                        <x-slot name="header">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <span>Assigned Students</span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm" @click="exportStudents()">
                                        <i class="bi bi-download me-1"></i> Export
                                    </button>
                                    <a href="{{ route('transport.assign', ['route_id' => $route->id ?? 1]) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Assign Students
                                    </a>
                                </div>
                            </div>
                        </x-slot>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Stop</th>
                                        <th class="text-center">Fees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students ?? [] as $ts)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($ts->student->photo ?? false)
                                                        <img src="{{ asset('storage/' . $ts->student->photo) }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                                    @else
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                                            {{ strtoupper(substr($ts->student->first_name ?? 'S', 0, 1)) }}
                                                        </span>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('students.show', $ts->student_id) }}" class="text-decoration-none fw-medium">
                                                            {{ $ts->student->first_name ?? '' }} {{ $ts->student->last_name ?? '' }}
                                                        </a>
                                                        <br><small class="text-muted">{{ $ts->student->admission_number ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $ts->student->class->name ?? '-' }} - {{ $ts->student->section->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($ts->stop)
                                                    <span class="badge bg-info">{{ $ts->stop->stop_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">
                                                    {{ config('app.currency_symbol', '$') }}{{ number_format($ts->transport_fees ?? 0, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-people d-block mb-2 fs-4"></i>
                                                    No students assigned yet
                                                </div>
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
                    <p>Are you sure you want to delete the route "<strong>{{ $route->name ?? 'Route' }}</strong>"?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This will also remove all stops and student assignments for this route.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('transport.routes.destroy', $route->id ?? 1) }}" method="POST" class="d-inline">
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
function transportRouteDetails() {
    return {
        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        printRoute() {
            window.print();
        },

        exportRoute() {
            window.location.href = '{{ route('transport.routes.export', $route->id ?? 1) }}';
        },

        exportStudents() {
            window.location.href = '{{ route('transport.students.export') }}?route_id={{ $route->id ?? 1 }}';
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

@media print {
    .btn, .nav-tabs, .breadcrumb {
        display: none !important;
    }
    .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
}
</style>
@endpush
