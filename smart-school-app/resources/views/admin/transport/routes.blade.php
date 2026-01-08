{{-- Transport Routes List View --}}
{{-- Prompt 222: Transport routes listing page with CRUD operations --}}

@extends('layouts.app')

@section('title', 'Transport Routes')

@section('content')
<div x-data="transportRoutesManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Transport Routes</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transport</a></li>
                    <li class="breadcrumb-item active">Routes</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('transport.routes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Route
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
                    <i class="bi bi-signpost-2 fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ count($routes ?? []) }}</h3>
                    <small class="text-muted">Total Routes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($routes ?? [])->where('is_active', true)->count() }}</h3>
                    <small class="text-muted">Active Routes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-geo-alt fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($routes ?? [])->sum('stops_count') }}</h3>
                    <small class="text-muted">Total Stops</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-people fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ collect($routes ?? [])->sum('students_count') }}</h3>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Routes Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-signpost-2 me-2"></i>
                    Transport Routes
                    <span class="badge bg-primary ms-2">{{ count($routes ?? []) }}</span>
                </span>
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control border-start-0" 
                        placeholder="Search routes..."
                        x-model="search"
                    >
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Route Name</th>
                        <th>Route Number</th>
                        <th>Description</th>
                        <th class="text-center">Stops</th>
                        <th class="text-center">Students</th>
                        <th class="text-center">Vehicles</th>
                        <th class="text-center">Status</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes ?? [] as $index => $route)
                        <tr x-show="matchesSearch('{{ strtolower($route->name ?? '') }}', '{{ strtolower($route->route_number ?? '') }}')">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px;">
                                        <i class="bi bi-signpost-2"></i>
                                    </span>
                                    <div>
                                        <a href="{{ route('transport.routes.show', $route->id) }}" class="text-decoration-none fw-medium">
                                            {{ $route->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace">{{ $route->route_number }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($route->description ?? '-', 40) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $route->stops_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $route->students_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">{{ $route->vehicles_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if($route->is_active ?? true)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a 
                                        href="{{ route('transport.routes.show', $route->id) }}" 
                                        class="btn btn-outline-primary" 
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a 
                                        href="{{ route('transport.routes.stops', $route->id) }}" 
                                        class="btn btn-outline-info" 
                                        title="Manage Stops"
                                    >
                                        <i class="bi bi-geo-alt"></i>
                                    </a>
                                    <a 
                                        href="{{ route('transport.routes.edit', $route->id) }}" 
                                        class="btn btn-outline-warning" 
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Delete"
                                        @click="confirmDelete({{ $route->id }}, '{{ $route->name }}')"
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
                                    <i class="bi bi-signpost-2 fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No transport routes found</p>
                                    <a href="{{ route('transport.routes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Route
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($routes) && $routes instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $routes->firstItem() ?? 0 }} to {{ $routes->lastItem() ?? 0 }} of {{ $routes->total() }} entries
                </div>
                {{ $routes->links() }}
            </div>
        </x-slot>
        @endif
    </x-card>

    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('transport.vehicles.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bus-front fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="mb-0">Vehicles</h6>
                    <small class="text-muted">Manage vehicles</small>
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
                    <p>Are you sure you want to delete the route "<strong x-text="deleteRouteName"></strong>"?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. All stops and student assignments for this route will also be removed.
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
function transportRoutesManager() {
    return {
        search: '',
        deleteRouteId: null,
        deleteRouteName: '',
        deleteUrl: '',

        matchesSearch(name, routeNumber) {
            if (!this.search) return true;
            const searchLower = this.search.toLowerCase();
            return name.includes(searchLower) || routeNumber.includes(searchLower);
        },

        confirmDelete(id, name) {
            this.deleteRouteId = id;
            this.deleteRouteName = name;
            this.deleteUrl = `/transport/routes/${id}`;
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
