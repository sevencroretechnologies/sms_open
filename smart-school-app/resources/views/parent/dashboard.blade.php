@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Parent Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ $parent->user->name ?? 'Parent' }}!</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Children Tabs -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="childrenTabs" role="tablist">
                @forelse($children as $index => $child)
                    @php $childData = $childrenData[$child->id] ?? null; @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                id="child-{{ $child->id }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#child-{{ $child->id }}" 
                                type="button" 
                                role="tab">
                            <i class="bi bi-person-circle me-2"></i>{{ $childData['profile']['name'] ?? $child->first_name }}
                            <span class="badge bg-secondary ms-1">{{ $childData['profile']['class_name'] ?? 'N/A' }}-{{ $childData['profile']['section_name'] ?? '' }}</span>
                        </button>
                    </li>
                @empty
                    <li class="nav-item">
                        <span class="nav-link text-muted">No children registered</span>
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="childrenTabContent">
                @forelse($children as $index => $child)
                    @php $childData = $childrenData[$child->id] ?? null; @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                         id="child-{{ $child->id }}" 
                         role="tabpanel">
                        
                        <!-- Child Profile -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="bg-light rounded p-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @if($childData['profile']['photo'] ?? null)
                                                <img src="{{ asset('storage/' . $childData['profile']['photo']) }}" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-mortarboard fs-3"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h5 class="mb-1">{{ $childData['profile']['name'] ?? 'N/A' }}</h5>
                                            <p class="mb-0 text-muted">
                                                <span class="me-3"><i class="bi bi-building me-1"></i>Class: {{ $childData['profile']['class_name'] ?? 'N/A' }}-{{ $childData['profile']['section_name'] ?? '' }}</span>
                                                <span><i class="bi bi-hash me-1"></i>Roll: {{ $childData['profile']['roll_number'] ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Full Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card stat-card h-100 border-{{ ($childData['attendance']['percentage'] ?? 0) >= 75 ? 'success' : (($childData['attendance']['percentage'] ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="text-muted small mb-1">Attendance</p>
                                                <h3 class="mb-0 text-{{ ($childData['attendance']['percentage'] ?? 0) >= 75 ? 'success' : (($childData['attendance']['percentage'] ?? 0) >= 50 ? 'warning' : 'danger') }}">{{ $childData['attendance']['percentage'] ?? 0 }}%</h3>
                                                <small class="text-{{ ($childData['attendance']['percentage'] ?? 0) >= 75 ? 'success' : (($childData['attendance']['percentage'] ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                                    <i class="bi bi-{{ ($childData['attendance']['percentage'] ?? 0) >= 75 ? 'check-circle' : 'exclamation-circle' }}"></i> {{ $childData['attendance']['status'] ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                                <i class="bi bi-calendar-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card h-100 border-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="text-muted small mb-1">Recent Results</p>
                                                <h3 class="mb-0 text-primary">{{ count($childData['recentResults'] ?? []) }}</h3>
                                                <small class="text-muted">Exams taken</small>
                                            </div>
                                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                                <i class="bi bi-award"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card h-100 border-warning">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="text-muted small mb-1">Pending Fees</p>
                                                <h3 class="mb-0 text-warning">{{ number_format($childData['pendingFees']['pending_amount'] ?? 0) }}</h3>
                                                <small class="text-warning"><i class="bi bi-exclamation-circle"></i> {{ $childData['pendingFees']['pending_count'] ?? 0 }} pending</small>
                                            </div>
                                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                                <i class="bi bi-currency-rupee"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card h-100 border-info">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="text-muted small mb-1">Homework</p>
                                                <h3 class="mb-0 text-info">{{ count($childData['pendingHomework'] ?? []) }}</h3>
                                                <small class="text-muted">Pending tasks</small>
                                            </div>
                                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                                <i class="bi bi-journal-bookmark"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions for Child -->
                        <div class="row g-2 mb-4">
                            <div class="col-auto">
                                <a href="{{ route('parent.children.attendance', $child->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-calendar-check me-1"></i>Attendance
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('parent.children.exams', $child->id) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-graph-up me-1"></i>Results
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('parent.fees.pay', $child->id) }}" class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-credit-card me-1"></i>Pay Fees
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('parent.messages.compose') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-envelope me-1"></i>Message Teacher
                                </a>
                            </div>
                        </div>

                        <!-- Attendance & Results -->
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>This Month's Attendance</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Present Days</span>
                                            <strong class="text-success">{{ $childData['attendance']['this_month']['present'] ?? 0 }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Absent Days</span>
                                            <strong class="text-danger">{{ $childData['attendance']['this_month']['absent'] ?? 0 }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Late Arrivals</span>
                                            <strong class="text-warning">{{ $childData['attendance']['this_month']['late'] ?? 0 }}</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span>Attendance Rate</span>
                                            <strong class="text-primary">{{ $childData['attendance']['this_month']['percentage'] ?? 0 }}%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Recent Exam Results</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @forelse($childData['recentResults'] ?? [] as $result)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $result['exam_name'] }}</strong>
                                                        <small class="d-block text-muted">{{ $result['subject_name'] }}</small>
                                                    </div>
                                                    <span class="badge bg-{{ $result['percentage'] >= 80 ? 'success' : ($result['percentage'] >= 60 ? 'primary' : ($result['percentage'] >= 40 ? 'warning' : 'danger')) }}">{{ $result['percentage'] }}%</span>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center text-muted py-4">
                                                    <i class="bi bi-clipboard-x fs-3 d-block mb-2"></i>
                                                    No exam results yet
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No children registered under your account.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Fee Summary & Notices -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Fee Summary</h6>
                    <a href="{{ route('parent.fees.index') }}" class="btn btn-sm btn-primary">Pay Now</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($feeSummary['children_fees'] ?? [] as $childFee)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong>{{ $childFee['child_name'] }}</strong>
                                    <small class="d-block text-muted">Paid: {{ number_format($childFee['paid']) }}</small>
                                </div>
                                <div class="text-end">
                                    @if($childFee['pending'] > 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                    <strong class="d-block">{{ number_format($childFee['pending']) }}</strong>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                                No fee records found
                            </div>
                        @endforelse
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                            <strong>Total Pending</strong>
                            <strong class="text-danger fs-5">{{ number_format($feeSummary['total_pending'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-megaphone me-2"></i>School Notices</h6>
                    <a href="{{ route('parent.notices.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notices ?? [] as $notice)
                            <a href="{{ route('parent.notices.show', $notice->id) }}" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $notice->title }}</strong>
                                    <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted small mb-0">{{ Str::limit($notice->content, 60) }}</p>
                            </a>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
                                No notices available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Overview Chart -->
    @if($children->count() > 1)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Children Attendance Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceOverviewChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($children->count() > 1)
    const attendanceCtx = document.getElementById('attendanceOverviewChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['attendanceOverview']['labels'] ?? []),
                datasets: [{
                    label: 'Attendance %',
                    data: @json($chartData['attendanceOverview']['data'] ?? []),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush

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
    .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        background: transparent;
    }
</style>
@endsection
