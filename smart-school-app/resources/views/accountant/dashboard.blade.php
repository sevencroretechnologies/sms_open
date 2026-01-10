@extends('layouts.app')

@section('title', 'Accountant Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Accountant Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'Accountant' }}!</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success fs-6 me-2">
                <i class="bi bi-cash-coin me-1"></i>Today: {{ number_format($todayCollection['total_amount'] ?? 0) }} ({{ $todayCollection['transaction_count'] ?? 0 }} txns)
            </span>
            <span class="badge bg-primary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Collection</p>
                            <h3 class="mb-0 text-success">{{ number_format($statistics['total_collection'] ?? 0) }}</h3>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> This month</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Fees</p>
                            <h3 class="mb-0 text-warning">{{ number_format($statistics['pending_fees'] ?? 0) }}</h3>
                            <small class="text-warning"><i class="bi bi-exclamation-circle"></i> To collect</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Expenses</p>
                            <h3 class="mb-0 text-danger">{{ number_format($statistics['total_expenses'] ?? 0) }}</h3>
                            <small class="text-muted">This month</small>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Net Balance</p>
                            <h3 class="mb-0 text-primary">{{ number_format($statistics['net_balance'] ?? 0) }}</h3>
                            <small class="text-{{ ($statistics['collection_change'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                                <i class="bi bi-arrow-{{ ($statistics['collection_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i> 
                                {{ ($statistics['collection_change'] ?? 0) >= 0 ? '+' : '' }}{{ $statistics['collection_change'] ?? 0 }}% from last month
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-auto">
                            <a href="#" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Collect Fee
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-success">
                                <i class="bi bi-receipt me-2"></i>Generate Invoice
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-info text-white">
                                <i class="bi bi-file-earmark-text me-2"></i>Fee Report
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-warning">
                                <i class="bi bi-cash me-2"></i>Add Expense
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary">
                                <i class="bi bi-printer me-2"></i>Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Fee Collection Trend</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active">Monthly</button>
                        <button class="btn btn-outline-primary">Weekly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="feeCollectionChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Fee Type Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="feeTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Overdue Fees -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Transactions</h6>
                    <a href="#" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $txn)
                                    <tr>
                                        <td><code>{{ $txn['transaction_id'] }}</code></td>
                                        <td>{{ $txn['student_name'] }}</td>
                                        <td><span class="badge bg-secondary">{{ $txn['fee_type'] }}</span></td>
                                        <td><strong>{{ number_format($txn['amount']) }}</strong></td>
                                        <td>{{ $txn['payment_date'] ? \Carbon\Carbon::parse($txn['payment_date'])->diffForHumans() : 'N/A' }}</td>
                                        <td>
                                            @if($txn['status'] == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($txn['status'] == 'refund')
                                                <span class="badge bg-info">Refund</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                                            No recent transactions
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Overdue Fees</h6>
                    <span class="badge bg-danger">{{ count($overdueFees ?? []) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($overdueFees ?? [] as $overdue)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-1">{{ $overdue['student_name'] }} - Class {{ $overdue['class_name'] }}{{ $overdue['section_name'] ? '-' . $overdue['section_name'] : '' }}</h6>
                                    <small class="text-{{ $overdue['urgency'] }}">Overdue by {{ $overdue['days_overdue'] }} days</small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-{{ $overdue['urgency'] }}">{{ number_format($overdue['amount']) }}</strong>
                                    <a href="#" class="btn btn-sm btn-outline-primary d-block mt-1">Send Reminder</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4 text-muted">
                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                No overdue fees
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Fees by Class -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Pending Fees by Class</h6>
                    <a href="#" class="btn btn-sm btn-link">View Report</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                    <th>Pending Count</th>
                                    <th>Pending Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingFeesByClass ?? [] as $classData)
                                    <tr>
                                        <td><strong>{{ $classData['class_name'] }}</strong></td>
                                        <td>{{ $classData['student_count'] }}</td>
                                        <td><span class="badge bg-warning">{{ $classData['pending_count'] }}</span></td>
                                        <td><strong class="text-danger">{{ number_format($classData['pending_amount']) }}</strong></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                            No pending fees
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fee Collection Chart
    const feeCtx = document.getElementById('feeCollectionChart');
    if (feeCtx) {
        new Chart(feeCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartData['collectionTrend']['labels'] ?? []),
                datasets: [{
                    label: 'Collection',
                    data: @json($chartData['collectionTrend']['collection'] ?? []),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Target',
                    data: @json($chartData['collectionTrend']['target'] ?? []),
                    borderColor: '#6366f1',
                    borderDash: [5, 5],
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Fee Type Distribution Chart
    const typeCtx = document.getElementById('feeTypeChart');
    if (typeCtx) {
        const feeTypeLabels = @json($chartData['feeTypeDistribution']['labels'] ?? []);
        const feeTypeData = @json($chartData['feeTypeDistribution']['data'] ?? []);
        const defaultColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        const feeTypeColors = @json($chartData['feeTypeDistribution']['colors'] ?? []).length > 0 ? @json($chartData['feeTypeDistribution']['colors'] ?? []) : defaultColors;
        
        new Chart(typeCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: feeTypeLabels.length > 0 ? feeTypeLabels : ['No Data'],
                datasets: [{
                    data: feeTypeData.length > 0 ? feeTypeData : [1],
                    backgroundColor: feeTypeData.length > 0 ? feeTypeColors : ['#e5e7eb']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
