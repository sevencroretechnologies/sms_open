@extends('layouts.app')

@section('title', 'Fee Reports')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Fee Reports</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Fee Reports Dashboard</h1>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">Today's Collection</h6>
                            <h3 class="card-title mb-0">{{ number_format($todayCollection, 2) }}</h3>
                        </div>
                        <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">This Month</h6>
                            <h3 class="card-title mb-0">{{ number_format($monthCollection, 2) }}</h3>
                        </div>
                        <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">Total Due</h6>
                            <h3 class="card-title mb-0">{{ number_format($totalDue, 2) }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">Total Collection</h6>
                            <h3 class="card-title mb-0">{{ number_format($totalCollection, 2) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <x-card title="Quick Reports">
                <div class="list-group list-group-flush">
                    <a href="{{ route('accountant.fees-reports.collection') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-graph-up text-success me-2"></i>
                            <strong>Collection Report</strong>
                            <p class="mb-0 small text-muted">View fee collection by date range</p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accountant.fees-reports.due') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-clock text-warning me-2"></i>
                            <strong>Due Fees Report</strong>
                            <p class="mb-0 small text-muted">View all pending fee payments</p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accountant.fees-reports.defaulters') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-exclamation-circle text-danger me-2"></i>
                            <strong>Defaulters Report</strong>
                            <p class="mb-0 small text-muted">View overdue fee payments</p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accountant.fees-reports.class-wise') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-bar-chart text-info me-2"></i>
                            <strong>Class-wise Summary</strong>
                            <p class="mb-0 small text-muted">View collection summary by class</p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card title="Quick Actions">
                <div class="d-grid gap-3">
                    <a href="{{ route('accountant.fees-collection.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-cash me-2"></i> Collect Fees
                    </a>
                    <a href="{{ route('accountant.fees-collection.search') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-search me-2"></i> Search Student
                    </a>
                    <a href="{{ route('accountant.expenses.index') }}" class="btn btn-outline-info btn-lg">
                        <i class="bi bi-receipt me-2"></i> Manage Expenses
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
