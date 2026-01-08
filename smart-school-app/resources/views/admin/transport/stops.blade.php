{{-- Transport Route Stops View --}}
{{-- Prompt 224: Route stops management view with add/remove functionality --}}

@extends('layouts.app')

@section('title', 'Route Stops - ' . ($route->name ?? 'Route'))

@section('content')
<div x-data="transportStopsManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Manage Route Stops</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.routes.index') }}">Transport Routes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transport.routes.show', $route->id ?? 1) }}">{{ $route->name ?? 'Route' }}</a></li>
                    <li class="breadcrumb-item active">Stops</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.routes.show', $route->id ?? 1) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Route
            </a>
            <button type="button" class="btn btn-primary" @click="showAddModal = true">
                <i class="bi bi-plus-lg me-1"></i> Add Stop
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
                    Route Details
                </x-slot>

                <div class="text-center py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-signpost-2 fs-3"></i>
                    </div>
                    <h5 class="mb-1">{{ $route->name ?? 'Route Name' }}</h5>
                    <p class="mb-2">
                        <span class="badge bg-light text-dark font-monospace">{{ $route->route_number ?? 'R001' }}</span>
                    </p>
                    <p class="text-muted small mb-3">{{ $route->description ?? 'No description' }}</p>
                    @if($route->is_active ?? true)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="text-muted small">Stops</div>
                            <div class="fw-bold">{{ count($stops ?? []) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Vehicles</div>
                            <div class="fw-bold">{{ $route->vehicles_count ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Students</div>
                            <div class="fw-bold">{{ $route->students_count ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Route Actions -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-gear me-2"></i>
                    Actions
                </x-slot>

                <div class="d-grid gap-2">
                    <a href="{{ route('transport.routes.edit', $route->id ?? 1) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Route
                    </a>
                    <a href="{{ route('transport.vehicles.index', ['route_id' => $route->id ?? 1]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-bus-front me-1"></i> View Vehicles
                    </a>
                    <a href="{{ route('transport.students.index', ['route_id' => $route->id ?? 1]) }}" class="btn btn-outline-success">
                        <i class="bi bi-people me-1"></i> View Students
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Stops List -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span>
                            <i class="bi bi-geo-alt me-2"></i>
                            Route Stops
                            <span class="badge bg-primary ms-2">{{ count($stops ?? []) }}</span>
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" @click="toggleReorder()" x-show="stops.length > 1">
                            <i class="bi bi-arrows-move me-1"></i>
                            <span x-text="reorderMode ? 'Done' : 'Reorder'"></span>
                        </button>
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
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stops ?? [] as $index => $stop)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">{{ $stop->stop_order ?? ($index + 1) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-info bg-opacity-10 text-info" style="width: 36px; height: 36px;">
                                                <i class="bi bi-geo-alt"></i>
                                            </span>
                                            <span class="fw-medium">{{ $stop->stop_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($stop->stop_time)
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-clock me-1"></i>
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
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(($stop->stop_order ?? ($index + 1)) > 1)
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Move Up"
                                                @click="moveStop({{ $stop->id }}, 'up')"
                                            >
                                                <i class="bi bi-arrow-up"></i>
                                            </button>
                                            @endif
                                            @if(($stop->stop_order ?? ($index + 1)) < count($stops ?? []))
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-secondary" 
                                                title="Move Down"
                                                @click="moveStop({{ $stop->id }}, 'down')"
                                            >
                                                <i class="bi bi-arrow-down"></i>
                                            </button>
                                            @endif
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-warning" 
                                                title="Edit"
                                                @click="editStop({{ json_encode($stop) }})"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Delete"
                                                @click="confirmDelete({{ $stop->id }}, '{{ $stop->stop_name }}')"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                                            <p class="mb-2">No stops added to this route</p>
                                            <button type="button" class="btn btn-primary btn-sm" @click="showAddModal = true">
                                                <i class="bi bi-plus-lg me-1"></i> Add First Stop
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Route Timeline -->
            @if(count($stops ?? []) > 0)
            <x-card class="mt-4">
                <x-slot name="header">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Route Timeline
                </x-slot>

                <div class="route-timeline">
                    @foreach($stops ?? [] as $index => $stop)
                        <div class="timeline-item d-flex align-items-start mb-3">
                            <div class="timeline-marker me-3">
                                <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    {{ $stop->stop_order ?? ($index + 1) }}
                                </span>
                                @if($index < count($stops) - 1)
                                    <div class="timeline-line"></div>
                                @endif
                            </div>
                            <div class="timeline-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $stop->stop_name }}</h6>
                                        <small class="text-muted">
                                            @if($stop->stop_time)
                                                <i class="bi bi-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($stop->stop_time)->format('h:i A') }}
                                            @endif
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-people me-1"></i>
                                            {{ $stop->students_count ?? 0 }} students
                                        </small>
                                    </div>
                                    <span class="badge bg-success">
                                        {{ config('app.currency_symbol', '$') }}{{ number_format($stop->fare ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>
    </div>

    <!-- Add/Edit Stop Modal -->
    <div class="modal fade" id="stopModal" tabindex="-1" x-ref="stopModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-geo-alt me-2"></i>
                        <span x-text="editingStop ? 'Edit Stop' : 'Add Stop'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form :action="editingStop ? `/transport/routes/{{ $route->id ?? 1 }}/stops/${editingStop.id}` : '{{ route('transport.routes.stops.store', $route->id ?? 1) }}'" method="POST">
                    @csrf
                    <template x-if="editingStop">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Stop Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="stop_name"
                                class="form-control"
                                x-model="stopForm.stop_name"
                                required
                                placeholder="e.g., Main Street, City Center"
                            >
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Stop Time</label>
                                <input 
                                    type="time" 
                                    name="stop_time"
                                    class="form-control"
                                    x-model="stopForm.stop_time"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fare <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ config('app.currency_symbol', '$') }}</span>
                                    <input 
                                        type="number" 
                                        name="fare"
                                        class="form-control"
                                        x-model="stopForm.fare"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" x-show="!editingStop">
                            <label class="form-label">Stop Order</label>
                            <input 
                                type="number" 
                                name="stop_order"
                                class="form-control"
                                x-model="stopForm.stop_order"
                                min="1"
                                placeholder="Auto-assigned if empty"
                            >
                            <div class="form-text">Leave empty to add at the end</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <span x-text="editingStop ? 'Update Stop' : 'Add Stop'"></span>
                        </button>
                    </div>
                </form>
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
                    <p>Are you sure you want to delete the stop "<strong x-text="deleteStopName"></strong>"?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Students assigned to this stop will need to be reassigned.
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
function transportStopsManager() {
    return {
        showAddModal: false,
        reorderMode: false,
        editingStop: null,
        stopForm: {
            stop_name: '',
            stop_time: '',
            fare: '',
            stop_order: ''
        },
        deleteStopId: null,
        deleteStopName: '',
        deleteUrl: '',
        stops: @json($stops ?? []),

        init() {
            this.$watch('showAddModal', (value) => {
                if (value) {
                    this.editingStop = null;
                    this.resetForm();
                    const modal = new bootstrap.Modal(this.$refs.stopModal);
                    modal.show();
                }
            });
        },

        resetForm() {
            this.stopForm = {
                stop_name: '',
                stop_time: '',
                fare: '',
                stop_order: ''
            };
        },

        editStop(stop) {
            this.editingStop = stop;
            this.stopForm = {
                stop_name: stop.stop_name || '',
                stop_time: stop.stop_time || '',
                fare: stop.fare || '',
                stop_order: stop.stop_order || ''
            };
            const modal = new bootstrap.Modal(this.$refs.stopModal);
            modal.show();
        },

        toggleReorder() {
            this.reorderMode = !this.reorderMode;
        },

        moveStop(stopId, direction) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/transport/routes/{{ $route->id ?? 1 }}/stops/${stopId}/move`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const directionInput = document.createElement('input');
            directionInput.type = 'hidden';
            directionInput.name = 'direction';
            directionInput.value = direction;
            form.appendChild(directionInput);
            
            document.body.appendChild(form);
            form.submit();
        },

        confirmDelete(id, name) {
            this.deleteStopId = id;
            this.deleteStopName = name;
            this.deleteUrl = `/transport/routes/{{ $route->id ?? 1 }}/stops/${id}`;
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.route-timeline .timeline-marker {
    position: relative;
}

.route-timeline .timeline-line {
    position: absolute;
    left: 50%;
    top: 32px;
    width: 2px;
    height: calc(100% + 12px);
    background-color: var(--bs-primary);
    transform: translateX(-50%);
}

.route-timeline .timeline-item:last-child .timeline-line {
    display: none;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .me-3 {
    margin-right: 0 !important;
    margin-left: 1rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}
</style>
@endpush
