{{-- Transport Students List View --}}
{{-- Prompt 228: Transport students listing page with search and filter --}}

@extends('layouts.app')

@section('title', 'Transport Students')

@section('content')
<div x-data="transportStudentsManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Transport Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transport</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-success" @click="exportStudents()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-outline-secondary" @click="printReport()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
            <a href="{{ route('transport.assign') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Assign Transport
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
                    <i class="bi bi-people fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ count($transportStudents ?? []) }}</h3>
                    <small class="text-muted">Total Assigned</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-signpost-2 fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ count($routes ?? []) }}</h3>
                    <small class="text-muted">Active Routes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-bus-front fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ count($vehicles ?? []) }}</h3>
                    <small class="text-muted">Active Vehicles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-currency-dollar fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($transportStudents ?? [])->sum('transport_fees'), 2) }}</h3>
                    <small class="text-muted">Total Fees</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Search by name, admission no..."
                        x-model="filters.search"
                    >
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Route</label>
                <select class="form-select" x-model="filters.route">
                    <option value="">All Routes</option>
                    @foreach($routes ?? [] as $route)
                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Vehicle</label>
                <select class="form-select" x-model="filters.vehicle">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles ?? [] as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Class</label>
                <select class="form-select" x-model="filters.class">
                    <option value="">All Classes</option>
                    @foreach($classes ?? [] as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="button" class="btn btn-outline-secondary flex-grow-1" @click="resetFilters()">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-card>

    <!-- Students Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-people me-2"></i>
                    Transport Students
                    <span class="badge bg-primary ms-2">{{ count($transportStudents ?? []) }}</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAll" x-model="selectAll" @change="toggleSelectAll()">
                        <label class="form-check-label small" for="selectAll">Select All</label>
                    </div>
                    <button 
                        type="button" 
                        class="btn btn-outline-success btn-sm"
                        x-show="selectedStudents.length > 0"
                        @click="exportSelected()"
                    >
                        <i class="bi bi-download me-1"></i> Export (<span x-text="selectedStudents.length"></span>)
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleSelectAll()">
                        </th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Route</th>
                        <th>Stop</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th class="text-center">Fees</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transportStudents ?? [] as $index => $ts)
                        <tr x-show="matchesFilters({{ json_encode([
                            'student_name' => strtolower(($ts->student->first_name ?? '') . ' ' . ($ts->student->last_name ?? '')),
                            'admission_number' => strtolower($ts->student->admission_number ?? ''),
                            'route_id' => $ts->route_id ?? '',
                            'vehicle_id' => $ts->vehicle_id ?? '',
                            'class_id' => $ts->student->class_id ?? ''
                        ]) }})">
                            <td>
                                <input type="checkbox" class="form-check-input" :value="{{ $ts->id }}" x-model="selectedStudents">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($ts->student->photo ?? false)
                                        <img src="{{ asset('storage/' . $ts->student->photo) }}" alt="{{ $ts->student->first_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
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
                                @if($ts->route)
                                    <a href="{{ route('transport.routes.show', $ts->route_id) }}" class="text-decoration-none">
                                        {{ $ts->route->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
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
                            <td>
                                @if($ts->vehicle)
                                    <span class="badge bg-light text-dark font-monospace">
                                        {{ $ts->vehicle->vehicle_number }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($ts->vehicle && $ts->vehicle->driver_name)
                                    <div class="small">
                                        <span class="fw-medium">{{ $ts->vehicle->driver_name }}</span>
                                        @if($ts->vehicle->driver_phone)
                                            <br>
                                            <a href="tel:{{ $ts->vehicle->driver_phone }}" class="text-muted">
                                                <i class="bi bi-telephone me-1"></i>{{ $ts->vehicle->driver_phone }}
                                            </a>
                                        @endif
                                    </div>
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
                                    <a 
                                        href="{{ route('transport.students.edit', $ts->id) }}" 
                                        class="btn btn-outline-warning" 
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Remove"
                                        @click="confirmRemove({{ $ts->id }}, '{{ ($ts->student->first_name ?? '') . ' ' . ($ts->student->last_name ?? '') }}')"
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
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No transport assignments found</p>
                                    <a href="{{ route('transport.assign') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Assign Transport
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($transportStudents) && $transportStudents instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $transportStudents->firstItem() ?? 0 }} to {{ $transportStudents->lastItem() ?? 0 }} of {{ $transportStudents->total() }} entries
                </div>
                {{ $transportStudents->links() }}
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
            <a href="{{ route('transport.vehicles.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bus-front fs-1 text-success mb-2 d-block"></i>
                    <h6 class="mb-0">Vehicles</h6>
                    <small class="text-muted">Manage vehicles</small>
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

    <!-- Remove Confirmation Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" x-ref="removeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Confirm Remove
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove transport assignment for "<strong x-text="removeStudentName"></strong>"?</p>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        The student will no longer have transport assigned.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form :action="removeUrl" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Remove
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
function transportStudentsManager() {
    return {
        filters: {
            search: '',
            route: '',
            vehicle: '',
            class: ''
        },
        selectedStudents: [],
        selectAll: false,
        removeStudentId: null,
        removeStudentName: '',
        removeUrl: '',

        matchesFilters(student) {
            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!student.student_name.includes(searchLower) && 
                    !student.admission_number.includes(searchLower)) {
                    return false;
                }
            }

            // Route filter
            if (this.filters.route && student.route_id != this.filters.route) {
                return false;
            }

            // Vehicle filter
            if (this.filters.vehicle && student.vehicle_id != this.filters.vehicle) {
                return false;
            }

            // Class filter
            if (this.filters.class && student.class_id != this.filters.class) {
                return false;
            }

            return true;
        },

        resetFilters() {
            this.filters = {
                search: '',
                route: '',
                vehicle: '',
                class: ''
            };
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedStudents = @json(collect($transportStudents ?? [])->pluck('id'));
            } else {
                this.selectedStudents = [];
            }
        },

        exportStudents() {
            window.location.href = '{{ route('transport.students.export') }}';
        },

        exportSelected() {
            const ids = this.selectedStudents.join(',');
            window.location.href = `{{ route('transport.students.export') }}?ids=${ids}`;
        },

        printReport() {
            window.print();
        },

        confirmRemove(id, name) {
            this.removeStudentId = id;
            this.removeStudentName = name;
            this.removeUrl = `/transport/students/${id}`;
            const modal = new bootstrap.Modal(this.$refs.removeModal);
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

@media print {
    .btn, .form-check, .breadcrumb, nav {
        display: none !important;
    }
}
</style>
@endpush
