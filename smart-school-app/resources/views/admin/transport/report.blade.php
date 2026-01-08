{{-- Transport Report View --}}
{{-- Prompt 231: Transport reports with statistics, charts, and route-wise data --}}

@extends('layouts.app')

@section('title', 'Transport Reports')

@section('content')
<div x-data="transportReports()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Transport Reports</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transport</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-success" @click="exportReport()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-outline-secondary" @click="printReport()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>
            Report Filters
        </x-slot>

        <form method="GET" action="{{ route('transport.reports') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Academic Session</label>
                    <select name="session_id" class="form-select" x-model="filters.session_id">
                        <option value="">All Sessions</option>
                        @foreach($sessions ?? [] as $session)
                            <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Route</label>
                    <select name="route_id" class="form-select" x-model="filters.route_id">
                        <option value="">All Routes</option>
                        @foreach($routes ?? [] as $route)
                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Vehicle</label>
                    <select name="vehicle_id" class="form-select" x-model="filters.vehicle_id">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles ?? [] as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->vehicle_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Generate
                    </button>
                    <button type="button" class="btn btn-outline-secondary" @click="resetFilters()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-signpost-2 fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total_routes'] ?? 0 }}</h3>
                    <small class="text-muted">Total Routes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-bus-front fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total_vehicles'] ?? 0 }}</h3>
                    <small class="text-muted">Total Vehicles</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-people fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total_students'] ?? 0 }}</h3>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-currency-dollar fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ config('app.currency_symbol', '$') }}{{ number_format($stats['total_fees'] ?? 0, 0) }}</h3>
                    <small class="text-muted">Total Fees</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ config('app.currency_symbol', '$') }}{{ number_format($stats['collected_fees'] ?? 0, 0) }}</h3>
                    <small class="text-muted">Collected</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-danger bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-exclamation-circle fs-3 text-danger mb-2 d-block"></i>
                    <h3 class="mb-0">{{ config('app.currency_symbol', '$') }}{{ number_format($stats['pending_fees'] ?? 0, 0) }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Students by Route Chart -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-pie-chart me-2"></i>
                    Students by Route
                </x-slot>

                <div style="height: 300px;">
                    <canvas id="studentsByRouteChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Students by Stop Chart -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-bar-chart me-2"></i>
                    Students by Stop
                </x-slot>

                <div style="height: 300px;">
                    <canvas id="studentsByStopChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Vehicle Capacity Utilization -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-bar-chart-fill me-2"></i>
                    Vehicle Capacity Utilization
                </x-slot>

                <div style="height: 300px;">
                    <canvas id="vehicleCapacityChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Fee Collection Trend -->
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-graph-up me-2"></i>
                    Fee Collection Trend
                </x-slot>

                <div style="height: 300px;">
                    <canvas id="feeCollectionChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Route-wise Summary Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-table me-2"></i>
                    Route-wise Summary
                </span>
                <button type="button" class="btn btn-outline-success btn-sm" @click="exportRouteData()">
                    <i class="bi bi-download me-1"></i> Export
                </button>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Route Name</th>
                        <th class="text-center">Stops</th>
                        <th class="text-center">Students</th>
                        <th class="text-center">Vehicles</th>
                        <th class="text-end">Total Fees</th>
                        <th class="text-end">Collected</th>
                        <th class="text-end">Pending</th>
                        <th class="text-center">Collection %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routeStats ?? [] as $index => $rs)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('transport.routes.show', $rs['id']) }}" class="text-decoration-none fw-medium">
                                    {{ $rs['name'] }}
                                </a>
                                <br><small class="text-muted">{{ $rs['route_number'] }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $rs['stops_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $rs['students_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $rs['vehicles_count'] }}</span>
                            </td>
                            <td class="text-end">
                                {{ config('app.currency_symbol', '$') }}{{ number_format($rs['total_fees'], 2) }}
                            </td>
                            <td class="text-end text-success">
                                {{ config('app.currency_symbol', '$') }}{{ number_format($rs['collected_fees'], 2) }}
                            </td>
                            <td class="text-end text-danger">
                                {{ config('app.currency_symbol', '$') }}{{ number_format($rs['pending_fees'], 2) }}
                            </td>
                            <td class="text-center">
                                @php
                                    $collectionPercent = $rs['total_fees'] > 0 ? round(($rs['collected_fees'] / $rs['total_fees']) * 100) : 0;
                                    $badgeClass = $collectionPercent >= 80 ? 'bg-success' : ($collectionPercent >= 50 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $collectionPercent }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No route data available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($routeStats ?? []) > 0)
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="2">Total</td>
                        <td class="text-center">{{ collect($routeStats ?? [])->sum('stops_count') }}</td>
                        <td class="text-center">{{ collect($routeStats ?? [])->sum('students_count') }}</td>
                        <td class="text-center">{{ collect($routeStats ?? [])->sum('vehicles_count') }}</td>
                        <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($routeStats ?? [])->sum('total_fees'), 2) }}</td>
                        <td class="text-end text-success">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($routeStats ?? [])->sum('collected_fees'), 2) }}</td>
                        <td class="text-end text-danger">{{ config('app.currency_symbol', '$') }}{{ number_format(collect($routeStats ?? [])->sum('pending_fees'), 2) }}</td>
                        <td class="text-center">
                            @php
                                $totalFees = collect($routeStats ?? [])->sum('total_fees');
                                $totalCollected = collect($routeStats ?? [])->sum('collected_fees');
                                $overallPercent = $totalFees > 0 ? round(($totalCollected / $totalFees) * 100) : 0;
                            @endphp
                            <span class="badge bg-primary">{{ $overallPercent }}%</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-card>

    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-3">
            <a href="{{ route('transport.routes.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-signpost-2 fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="mb-0">Routes</h6>
                    <small class="text-muted">Manage routes</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('transport.vehicles.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bus-front fs-1 text-success mb-2 d-block"></i>
                    <h6 class="mb-0">Vehicles</h6>
                    <small class="text-muted">Manage vehicles</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('transport.students.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-info mb-2 d-block"></i>
                    <h6 class="mb-0">Students</h6>
                    <small class="text-muted">Transport assignments</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('transport.assign') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus fs-1 text-warning mb-2 d-block"></i>
                    <h6 class="mb-0">Assign</h6>
                    <small class="text-muted">Assign transport</small>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function transportReports() {
    return {
        filters: {
            session_id: '{{ request('session_id', '') }}',
            route_id: '{{ request('route_id', '') }}',
            vehicle_id: '{{ request('vehicle_id', '') }}'
        },

        init() {
            this.initCharts();
        },

        initCharts() {
            // Students by Route - Pie Chart
            const routeCtx = document.getElementById('studentsByRouteChart');
            if (routeCtx) {
                new Chart(routeCtx, {
                    type: 'pie',
                    data: {
                        labels: @json(collect($routeStats ?? [])->pluck('name')),
                        datasets: [{
                            data: @json(collect($routeStats ?? [])->pluck('students_count')),
                            backgroundColor: [
                                '#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#dc3545',
                                '#6f42c1', '#fd7e14', '#20c997', '#6c757d', '#d63384'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }

            // Students by Stop - Bar Chart
            const stopCtx = document.getElementById('studentsByStopChart');
            if (stopCtx) {
                new Chart(stopCtx, {
                    type: 'bar',
                    data: {
                        labels: @json(collect($stopStats ?? [])->pluck('stop_name')->take(10)),
                        datasets: [{
                            label: 'Students',
                            data: @json(collect($stopStats ?? [])->pluck('students_count')->take(10)),
                            backgroundColor: '#0dcaf0'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Vehicle Capacity Utilization - Bar Chart
            const vehicleCtx = document.getElementById('vehicleCapacityChart');
            if (vehicleCtx) {
                new Chart(vehicleCtx, {
                    type: 'bar',
                    data: {
                        labels: @json(collect($vehicleStats ?? [])->pluck('vehicle_number')),
                        datasets: [
                            {
                                label: 'Assigned',
                                data: @json(collect($vehicleStats ?? [])->pluck('students_count')),
                                backgroundColor: '#198754'
                            },
                            {
                                label: 'Available',
                                data: @json(collect($vehicleStats ?? [])->map(fn($v) => $v['capacity'] - $v['students_count'])),
                                backgroundColor: '#e9ecef'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Fee Collection Trend - Line Chart
            const feeCtx = document.getElementById('feeCollectionChart');
            if (feeCtx) {
                new Chart(feeCtx, {
                    type: 'line',
                    data: {
                        labels: @json($feeCollectionTrend['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
                        datasets: [{
                            label: 'Collected Fees',
                            data: @json($feeCollectionTrend['data'] ?? [0, 0, 0, 0, 0, 0]),
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        },

        resetFilters() {
            this.filters = {
                session_id: '',
                route_id: '',
                vehicle_id: ''
            };
            window.location.href = '{{ route('transport.reports') }}';
        },

        exportReport() {
            const params = new URLSearchParams(this.filters).toString();
            window.location.href = `{{ route('transport.reports.export') }}?${params}`;
        },

        exportRouteData() {
            const params = new URLSearchParams(this.filters).toString();
            window.location.href = `{{ route('transport.reports.export') }}?type=routes&${params}`;
        },

        printReport() {
            window.print();
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

[dir="rtl"] .text-end {
    text-align: left !important;
}

@media print {
    .btn, .breadcrumb, form {
        display: none !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
@endpush
