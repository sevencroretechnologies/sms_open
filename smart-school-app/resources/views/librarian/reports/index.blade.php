@extends('layouts.app')

@section('title', 'Library Reports')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Library Reports Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Books</h6>
                    <h3 class="card-title mb-0">{{ number_format($totalBooks) }}</h3>
                    <small class="opacity-75">{{ number_format($totalCopies) }} copies</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Available</h6>
                    <h3 class="card-title mb-0">{{ number_format($availableCopies) }}</h3>
                    <small class="opacity-75">copies available</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Issued</h6>
                    <h3 class="card-title mb-0">{{ number_format($issuedCopies) }}</h3>
                    <small class="opacity-75">copies issued</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Overdue</h6>
                    <h3 class="card-title mb-0">{{ number_format($overdueCount) }}</h3>
                    <small class="opacity-75">books overdue</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 text-muted">Total Members</h6>
                    <h3 class="card-title mb-0">{{ number_format($totalMembers) }}</h3>
                    <small class="text-muted">{{ number_format($activeMembers) }} active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 text-muted">Today's Issues</h6>
                    <h3 class="card-title mb-0">{{ number_format($todayIssues) }}</h3>
                    <small class="text-muted">books issued today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 text-muted">Today's Returns</h6>
                    <h3 class="card-title mb-0">{{ number_format($todayReturns) }}</h3>
                    <small class="text-muted">books returned today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 text-muted">Unpaid Fines</h6>
                    <h3 class="card-title mb-0 text-danger">${{ number_format($unpaidFines, 2) }}</h3>
                    <small class="text-muted">of ${{ number_format($totalFines, 2) }} total</small>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Available Reports">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-book me-2"></i>Inventory Report</h5>
                        <p class="card-text text-muted">View complete book inventory with category-wise breakdown and availability status.</p>
                        <a href="{{ route('librarian.reports.inventory') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-arrow-left-right me-2"></i>Circulation Report</h5>
                        <p class="card-text text-muted">Track book issues and returns with date range filters and member details.</p>
                        <a href="{{ route('librarian.reports.circulation') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-exclamation-triangle me-2"></i>Overdue Report</h5>
                        <p class="card-text text-muted">List of all overdue books with member contact information and estimated fines.</p>
                        <a href="{{ route('librarian.reports.overdue') }}" class="btn btn-danger">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cash me-2"></i>Fines Report</h5>
                        <p class="card-text text-muted">Track all fines collected and pending with payment status details.</p>
                        <a href="{{ route('librarian.reports.fines') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-folder me-2"></i>Category-wise Report</h5>
                        <p class="card-text text-muted">View book distribution across categories with availability statistics.</p>
                        <a href="{{ route('librarian.reports.category-wise') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection
