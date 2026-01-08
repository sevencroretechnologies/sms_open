{{-- Reports Dashboard View --}}
{{-- Prompt 266: Reports overview with quick links, recent reports, scheduled reports --}}

@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div x-data="reportsDashboard()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reports Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="showScheduledReports()">
                <i class="bi bi-clock-history me-1"></i> Scheduled Reports
            </button>
            <button type="button" class="btn btn-primary" @click="showCustomReport()">
                <i class="bi bi-plus-lg me-1"></i> Custom Report
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-file-earmark-text fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total_reports'] ?? 15 }}</h3>
                    <small class="text-muted">Available Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['generated_today'] ?? 5 }}</h3>
                    <small class="text-muted">Generated Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-clock fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['scheduled'] ?? 3 }}</h3>
                    <small class="text-muted">Scheduled Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-download fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['downloads_this_month'] ?? 42 }}</h3>
                    <small class="text-muted">Downloads This Month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row g-4">
        <!-- Student Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-people text-primary me-2"></i>Student Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.students') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list-ul me-2 text-muted"></i>Student List</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-badge me-2 text-muted"></i>Student Details</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-check me-2 text-muted"></i>Student Attendance</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-award me-2 text-muted"></i>Student Results</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-currency-dollar me-2 text-muted"></i>Student Fees</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Academic Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-book text-success me-2"></i>Academic Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-building me-2 text-muted"></i>Class List</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-diagram-3 me-2 text-muted"></i>Section List</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal-text me-2 text-muted"></i>Subject List</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar3 me-2 text-muted"></i>Class Timetable</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bar-chart me-2 text-muted"></i>Class Statistics</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Attendance Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-calendar-check text-warning me-2"></i>Attendance Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.attendance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-day me-2 text-muted"></i>Daily Attendance</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-month me-2 text-muted"></i>Monthly Attendance</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-year me-2 text-muted"></i>Yearly Attendance</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-graph-up me-2 text-muted"></i>Attendance Summary</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Examination Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-clipboard-data text-danger me-2"></i>Examination Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.exams') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calendar-event me-2 text-muted"></i>Exam Schedule</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-trophy me-2 text-muted"></i>Exam Results</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-person me-2 text-muted"></i>Report Cards</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-pie-chart me-2 text-muted"></i>Grade Distribution</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bar-chart-line me-2 text-muted"></i>Exam Statistics</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Fees Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-cash-stack text-info me-2"></i>Fees Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.fees') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-receipt me-2 text-muted"></i>Fee Collection</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-text me-2 text-muted"></i>Fee Summary</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-exclamation-triangle me-2 text-muted"></i>Pending Fees</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-graph-up-arrow me-2 text-muted"></i>Fee Collection Trend</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-x me-2 text-muted"></i>Fee Defaulters</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Financial Reports -->
        <div class="col-lg-4 col-md-6">
            <x-card class="h-100">
                <x-slot name="header">
                    <i class="bi bi-wallet2 text-secondary me-2"></i>Financial Reports
                </x-slot>
                <div class="list-group list-group-flush">
                    <a href="{{ route('reports.financial') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-arrow-down-circle me-2 text-muted"></i>Income Report</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-arrow-up-circle me-2 text-muted"></i>Expense Report</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('accounting.balance-sheet') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-earmark-spreadsheet me-2 text-muted"></i>Balance Sheet</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('accounting.report') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-calculator me-2 text-muted"></i>Financial Summary</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Recent Reports -->
    <x-card class="mt-4" :noPadding="true">
        <x-slot name="header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <span><i class="bi bi-clock-history me-2"></i>Recently Generated Reports</span>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
        </x-slot>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Report Name</th>
                        <th>Type</th>
                        <th>Generated By</th>
                        <th>Generated At</th>
                        <th>Format</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports ?? [] as $report)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle avatar-sm bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <span class="fw-medium">{{ $report->name ?? 'Report' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $report->type ?? 'General' }}
                                </span>
                            </td>
                            <td>{{ $report->generated_by ?? 'Admin' }}</td>
                            <td>{{ isset($report->created_at) ? $report->created_at->diffForHumans() : 'Just now' }}</td>
                            <td>
                                @php
                                    $formatIcons = [
                                        'pdf' => 'bi-file-pdf text-danger',
                                        'excel' => 'bi-file-excel text-success',
                                        'csv' => 'bi-file-text text-primary'
                                    ];
                                @endphp
                                <i class="bi {{ $formatIcons[$report->format ?? 'pdf'] ?? 'bi-file-pdf' }}"></i>
                                {{ strtoupper($report->format ?? 'PDF') }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" title="Regenerate">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                                No reports generated yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
function reportsDashboard() {
    return {
        showScheduledReports() {
            // Show scheduled reports modal or navigate to scheduled reports page
            alert('Scheduled Reports feature coming soon!');
        },
        
        showCustomReport() {
            // Show custom report builder modal or navigate to custom report page
            window.location.href = '/admin/reports/custom';
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 12px;
}

.list-group-item {
    border-left: 0;
    border-right: 0;
}

.list-group-item:first-child {
    border-top: 0;
}

.list-group-item:last-child {
    border-bottom: 0;
}
</style>
@endpush
