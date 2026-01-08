{{-- Transport Vehicle Details View --}}
{{-- Prompt 227: Transport vehicle details view with driver and students --}}

@extends('layouts.app')

@section('title', 'Vehicle Details - ' . ($vehicle->vehicle_number ?? 'Vehicle'))

@section('content')
<div x-data="transportVehicleDetails()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $vehicle->vehicle_number ?? 'Vehicle Details' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.vehicles.index') }}">Transport Vehicles</a></li>
                    <li class="breadcrumb-item active">{{ $vehicle->vehicle_number ?? 'Vehicle' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="printVehicleCard()">
                <i class="bi bi-printer me-1"></i> Print Card
            </button>
            <button type="button" class="btn btn-outline-info" @click="sendRouteToDriver()">
                <i class="bi bi-send me-1"></i> Send Route
            </button>
            <a href="{{ route('transport.vehicles.edit', $vehicle->id ?? 1) }}" class="btn btn-outline-warning">
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
            <!-- Vehicle Details Card -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-bus-front me-2"></i>
                    Vehicle Information
                </x-slot>

                <div class="text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-bus-front fs-1"></i>
                    </div>
                    <h4 class="mb-1 font-monospace">{{ $vehicle->vehicle_number ?? 'KA-01-AB-1234' }}</h4>
                    <p class="mb-2">
                        <span class="badge bg-light text-dark">{{ $vehicle->vehicle_type ?? 'Bus' }}</span>
                        @if($vehicle->vehicle_model)
                            <span class="badge bg-light text-dark">{{ $vehicle->vehicle_model }}</span>
                        @endif
                    </p>
                    @if($vehicle->is_active ?? true)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-info">{{ $vehicle->capacity ?? 40 }}</h4>
                                <small class="text-muted">Capacity</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-success">{{ count($students ?? []) }}</h4>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-warning">{{ ($vehicle->capacity ?? 40) - count($students ?? []) }}</h4>
                                <small class="text-muted">Available</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Capacity Progress -->
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Capacity Utilization</span>
                        <span class="small fw-medium">{{ round((count($students ?? []) / max($vehicle->capacity ?? 40, 1)) * 100) }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        @php
                            $utilization = round((count($students ?? []) / max($vehicle->capacity ?? 40, 1)) * 100);
                            $progressClass = $utilization > 90 ? 'bg-danger' : ($utilization > 70 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="progress-bar {{ $progressClass }}" style="width: {{ $utilization }}%"></div>
                    </div>
                </div>

                @if($vehicle->route)
                <div class="border-top pt-3 mt-3">
                    <h6 class="small text-muted mb-2">Assigned Route</h6>
                    <a href="{{ route('transport.routes.show', $vehicle->route_id) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <span class="d-inline-flex align-items-center justify-content-center rounded bg-info bg-opacity-10 text-info" style="width: 40px; height: 40px;">
                            <i class="bi bi-signpost-2"></i>
                        </span>
                        <div>
                            <span class="fw-medium">{{ $vehicle->route->name }}</span>
                            <br><small class="text-muted">{{ $vehicle->route->route_number }}</small>
                        </div>
                    </a>
                </div>
                @endif
            </x-card>

            <!-- Driver Details Card -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-person me-2"></i>
                    Driver Information
                </x-slot>

                @if($vehicle->driver_name)
                <div class="text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-10 text-secondary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person fs-1"></i>
                    </div>
                    <h5 class="mb-1">{{ $vehicle->driver_name }}</h5>
                    @if($vehicle->driver_license)
                        <p class="mb-2">
                            <span class="badge bg-light text-dark font-monospace">{{ $vehicle->driver_license }}</span>
                        </p>
                    @endif
                </div>

                <div class="border-top pt-3 mt-3">
                    @if($vehicle->driver_phone)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px;">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <div>
                            <small class="text-muted d-block">Phone</small>
                            <a href="tel:{{ $vehicle->driver_phone }}" class="text-decoration-none fw-medium">
                                {{ $vehicle->driver_phone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="tel:{{ $vehicle->driver_phone }}" class="btn btn-outline-success">
                            <i class="bi bi-telephone me-1"></i> Call Driver
                        </a>
                        <button type="button" class="btn btn-outline-info" @click="sendRouteToDriver()">
                            <i class="bi bi-send me-1"></i> Send Route Details
                        </button>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                        <p class="mb-2">No driver assigned</p>
                        <a href="{{ route('transport.vehicles.edit', $vehicle->id ?? 1) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Assign Driver
                        </a>
                    </div>
                </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Students Table -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span>
                            <i class="bi bi-people me-2"></i>
                            Assigned Students
                            <span class="badge bg-primary ms-2">{{ count($students ?? []) }}</span>
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm" @click="exportStudents()">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <a href="{{ route('transport.assign', ['vehicle_id' => $vehicle->id ?? 1]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> Assign Students
                            </a>
                        </div>
                    </div>
                </x-slot>

                <!-- Search -->
                <div class="p-3 border-bottom">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Search students..."
                            x-model="search"
                        >
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Stop</th>
                                <th class="text-center">Fees</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students ?? [] as $index => $ts)
                                <tr x-show="matchesSearch('{{ strtolower(($ts->student->first_name ?? '') . ' ' . ($ts->student->last_name ?? '') . ' ' . ($ts->student->admission_number ?? '')) }}')">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($ts->student->photo ?? false)
                                                <img src="{{ asset('storage/' . $ts->student->photo) }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px;">
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
                                            <span class="badge bg-info">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $ts->stop->stop_name }}
                                            </span>
                                            @if($ts->stop->stop_time)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($ts->stop->stop_time)->format('h:i A') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            {{ config('app.currency_symbol', '$') }}{{ number_format($ts->transport_fees ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('students.show', $ts->student_id) }}" class="btn btn-outline-primary" title="View Student">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" title="Remove" @click="confirmRemove({{ $ts->id }}, '{{ ($ts->student->first_name ?? '') . ' ' . ($ts->student->last_name ?? '') }}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                                            <p class="mb-2">No students assigned to this vehicle</p>
                                            <a href="{{ route('transport.assign', ['vehicle_id' => $vehicle->id ?? 1]) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i> Assign Students
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($students ?? []) > 0)
                <x-slot name="footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Total: {{ count($students ?? []) }} students
                        </div>
                        <div class="text-muted small">
                            Total Fees: <span class="fw-bold text-success">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($students ?? [])->sum('transport_fees'), 2) }}</span>
                        </div>
                    </div>
                </x-slot>
                @endif
            </x-card>

            <!-- Quick Links -->
            <div class="row g-3 mt-3">
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
                    <p>Are you sure you want to delete the vehicle "<strong>{{ $vehicle->vehicle_number ?? 'Vehicle' }}</strong>"?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Students assigned to this vehicle will need to be reassigned.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('transport.vehicles.destroy', $vehicle->id ?? 1) }}" method="POST" class="d-inline">
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

    <!-- Remove Student Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" x-ref="removeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Remove Student
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove "<strong x-text="removeStudentName"></strong>" from this vehicle?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form :action="removeUrl" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg me-1"></i> Remove
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
function transportVehicleDetails() {
    return {
        search: '',
        removeStudentId: null,
        removeStudentName: '',
        removeUrl: '',

        matchesSearch(text) {
            if (!this.search) return true;
            return text.includes(this.search.toLowerCase());
        },

        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        confirmRemove(id, name) {
            this.removeStudentId = id;
            this.removeStudentName = name;
            this.removeUrl = `/transport/students/${id}`;
            const modal = new bootstrap.Modal(this.$refs.removeModal);
            modal.show();
        },

        printVehicleCard() {
            window.print();
        },

        sendRouteToDriver() {
            alert('Route details will be sent to the driver via SMS/WhatsApp.');
        },

        exportStudents() {
            window.location.href = '{{ route('transport.students.export') }}?vehicle_id={{ $vehicle->id ?? 1 }}';
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

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

@media print {
    .btn, .breadcrumb, .input-group {
        display: none !important;
    }
}
</style>
@endpush
