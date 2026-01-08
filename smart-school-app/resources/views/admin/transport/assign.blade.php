{{-- Transport Student Assignment View --}}
{{-- Prompt 229: Transport student assignment view with route and stop selection --}}

@extends('layouts.app')

@section('title', 'Assign Transport')

@section('content')
<div x-data="transportAssignManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Assign Transport to Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.students.index') }}">Transport Students</a></li>
                    <li class="breadcrumb-item active">Assign Transport</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
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

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>
            Filter Students
        </x-slot>

        <form method="GET" action="{{ route('transport.assign') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Academic Session</label>
                    <select name="session_id" class="form-select" x-model="filters.session_id" @change="loadStudents()">
                        <option value="">Select Session</option>
                        @foreach($sessions ?? [] as $session)
                            <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Class</label>
                    <select name="class_id" class="form-select" x-model="filters.class_id" @change="loadSections(); loadStudents()">
                        <option value="">All Classes</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Section</label>
                    <select name="section_id" class="form-select" x-model="filters.section_id" @change="loadStudents()">
                        <option value="">All Sections</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <div class="row">
        <div class="col-lg-8">
            <!-- Students List -->
            <form action="{{ route('transport.assign.store') }}" method="POST" x-ref="assignForm">
                @csrf
                <input type="hidden" name="session_id" :value="filters.session_id">
                
                <x-card :noPadding="true">
                    <x-slot name="header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <span>
                                <i class="bi bi-people me-2"></i>
                                Students
                                <span class="badge bg-primary ms-2">{{ count($students ?? []) }}</span>
                            </span>
                            <div class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="selectAll" x-model="selectAll" @change="toggleSelectAll()">
                                <label class="form-check-label small" for="selectAll">Select All</label>
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
                                    <th>Current Transport</th>
                                    <th>Route</th>
                                    <th>Stop</th>
                                    <th>Vehicle</th>
                                    <th>Fees</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students ?? [] as $student)
                                    <tr>
                                        <td>
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input student-checkbox" 
                                                name="students[]" 
                                                value="{{ $student->id }}"
                                                x-model="selectedStudents"
                                                :value="{{ $student->id }}"
                                            >
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($student->photo)
                                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                                @else
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                                    </span>
                                                @endif
                                                <div>
                                                    <span class="fw-medium">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                    <br><small class="text-muted">{{ $student->admission_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $student->class->name ?? '-' }} - {{ $student->section->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($student->transportAssignment)
                                                <span class="badge bg-success">Assigned</span>
                                            @else
                                                <span class="badge bg-secondary">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select 
                                                name="route_id[{{ $student->id }}]" 
                                                class="form-select form-select-sm"
                                                x-model="assignments[{{ $student->id }}].route_id"
                                                @change="loadStops({{ $student->id }})"
                                            >
                                                <option value="">Select Route</option>
                                                @foreach($routes ?? [] as $route)
                                                    <option value="{{ $route->id }}" {{ ($student->transportAssignment->route_id ?? '') == $route->id ? 'selected' : '' }}>
                                                        {{ $route->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select 
                                                name="stop_id[{{ $student->id }}]" 
                                                class="form-select form-select-sm"
                                                x-model="assignments[{{ $student->id }}].stop_id"
                                                @change="updateFees({{ $student->id }})"
                                            >
                                                <option value="">Select Stop</option>
                                                @foreach($stops ?? [] as $stop)
                                                    @if(($student->transportAssignment->route_id ?? '') == $stop->route_id)
                                                        <option value="{{ $stop->id }}" data-fare="{{ $stop->fare }}" {{ ($student->transportAssignment->stop_id ?? '') == $stop->id ? 'selected' : '' }}>
                                                            {{ $stop->stop_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select 
                                                name="vehicle_id[{{ $student->id }}]" 
                                                class="form-select form-select-sm"
                                                x-model="assignments[{{ $student->id }}].vehicle_id"
                                            >
                                                <option value="">Select Vehicle</option>
                                                @foreach($vehicles ?? [] as $vehicle)
                                                    <option value="{{ $vehicle->id }}" {{ ($student->transportAssignment->vehicle_id ?? '') == $vehicle->id ? 'selected' : '' }}>
                                                        {{ $vehicle->vehicle_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <span class="input-group-text">{{ config('app.currency_symbol', '$') }}</span>
                                                <input 
                                                    type="number" 
                                                    name="transport_fees[{{ $student->id }}]" 
                                                    class="form-control"
                                                    x-model="assignments[{{ $student->id }}].transport_fees"
                                                    step="0.01"
                                                    min="0"
                                                    value="{{ $student->transportAssignment->transport_fees ?? '' }}"
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                                <p class="mb-2">No students found</p>
                                                <small>Select a class and section to view students</small>
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
                                <span x-text="selectedStudents.length"></span> students selected
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger" @click="removeTransport()" :disabled="selectedStudents.length === 0">
                                    <i class="bi bi-x-lg me-1"></i> Remove Transport
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="selectedStudents.length === 0">
                                    <i class="bi bi-check-lg me-1"></i> Assign Transport
                                </button>
                            </div>
                        </div>
                    </x-slot>
                    @endif
                </x-card>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Assignment Summary -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Assignment Summary
                </x-slot>

                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="mb-0 text-primary" x-text="totalStudents">{{ count($students ?? []) }}</h4>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="mb-0 text-success" x-text="selectedStudents.length">0</h4>
                            <small class="text-muted">Selected</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="mb-0 text-info">{{ collect($students ?? [])->filter(fn($s) => $s->transportAssignment)->count() }}</h4>
                            <small class="text-muted">Already Assigned</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="mb-0 text-warning">{{ collect($students ?? [])->filter(fn($s) => !$s->transportAssignment)->count() }}</h4>
                            <small class="text-muted">Not Assigned</small>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Fees:</span>
                        <span class="fw-bold text-success fs-5">
                            {{ config('app.currency_symbol', '$') }}<span x-text="totalFees.toFixed(2)">0.00</span>
                        </span>
                    </div>
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </x-slot>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" @click="applyToSelected('route')">
                        <i class="bi bi-signpost-2 me-1"></i> Apply Route to Selected
                    </button>
                    <button type="button" class="btn btn-outline-info" @click="applyToSelected('vehicle')">
                        <i class="bi bi-bus-front me-1"></i> Apply Vehicle to Selected
                    </button>
                    <button type="button" class="btn btn-outline-success" @click="applyToSelected('fees')">
                        <i class="bi bi-currency-dollar me-1"></i> Apply Fees to Selected
                    </button>
                </div>
            </x-card>

            <!-- Routes Overview -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-signpost-2 me-2"></i>
                    Available Routes
                </x-slot>

                <div class="list-group list-group-flush">
                    @forelse($routes ?? [] as $route)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="fw-medium">{{ $route->name }}</span>
                                <br><small class="text-muted">{{ $route->stops_count ?? 0 }} stops</small>
                            </div>
                            <span class="badge bg-primary">{{ $route->students_count ?? 0 }} students</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-signpost-2 d-block mb-2"></i>
                            No routes available
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    <!-- Apply to Selected Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" x-ref="applyModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-lightning me-2"></i>
                        Apply to Selected Students
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div x-show="applyType === 'route'">
                        <label class="form-label">Select Route</label>
                        <select class="form-select" x-model="applyValue">
                            <option value="">Select Route</option>
                            @foreach($routes ?? [] as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="applyType === 'vehicle'">
                        <label class="form-label">Select Vehicle</label>
                        <select class="form-select" x-model="applyValue">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="applyType === 'fees'">
                        <label class="form-label">Transport Fees</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ config('app.currency_symbol', '$') }}</span>
                            <input type="number" class="form-control" x-model="applyValue" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="confirmApply()">
                        <i class="bi bi-check-lg me-1"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transportAssignManager() {
    return {
        filters: {
            session_id: '{{ request('session_id', '') }}',
            class_id: '{{ request('class_id', '') }}',
            section_id: '{{ request('section_id', '') }}'
        },
        selectedStudents: [],
        selectAll: false,
        assignments: @json(collect($students ?? [])->mapWithKeys(fn($s) => [$s->id => [
            'route_id' => $s->transportAssignment->route_id ?? '',
            'stop_id' => $s->transportAssignment->stop_id ?? '',
            'vehicle_id' => $s->transportAssignment->vehicle_id ?? '',
            'transport_fees' => $s->transportAssignment->transport_fees ?? ''
        ]])),
        totalStudents: {{ count($students ?? []) }},
        applyType: '',
        applyValue: '',

        get totalFees() {
            let total = 0;
            this.selectedStudents.forEach(studentId => {
                const fees = parseFloat(this.assignments[studentId]?.transport_fees) || 0;
                total += fees;
            });
            return total;
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedStudents = @json(collect($students ?? [])->pluck('id'));
            } else {
                this.selectedStudents = [];
            }
        },

        loadStudents() {
            // This would typically be an AJAX call
            // For now, we'll submit the form
        },

        loadSections() {
            // This would typically be an AJAX call to load sections for selected class
        },

        loadStops(studentId) {
            // This would typically be an AJAX call to load stops for selected route
        },

        updateFees(studentId) {
            // Auto-update fees based on selected stop's fare
        },

        removeTransport() {
            if (confirm('Are you sure you want to remove transport assignment for selected students?')) {
                // Submit form with remove action
                const form = this.$refs.assignForm;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action';
                input.value = 'remove';
                form.appendChild(input);
                form.submit();
            }
        },

        applyToSelected(type) {
            this.applyType = type;
            this.applyValue = '';
            const modal = new bootstrap.Modal(this.$refs.applyModal);
            modal.show();
        },

        confirmApply() {
            if (!this.applyValue) return;
            
            this.selectedStudents.forEach(studentId => {
                if (this.applyType === 'route') {
                    this.assignments[studentId].route_id = this.applyValue;
                } else if (this.applyType === 'vehicle') {
                    this.assignments[studentId].vehicle_id = this.applyValue;
                } else if (this.applyType === 'fees') {
                    this.assignments[studentId].transport_fees = this.applyValue;
                }
            });
            
            bootstrap.Modal.getInstance(this.$refs.applyModal).hide();
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
</style>
@endpush
