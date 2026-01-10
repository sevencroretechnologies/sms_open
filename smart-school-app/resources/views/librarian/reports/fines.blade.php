@extends('layouts.app')

@section('title', 'Fines Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Fines</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Fines Report</h1>
        <a href="{{ route('librarian.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Fines</h6>
                    <h3 class="card-title mb-0">${{ number_format($summary['total_fines'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Paid Fines</h6>
                    <h3 class="card-title mb-0">${{ number_format($summary['paid_fines'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Unpaid Fines</h6>
                    <h3 class="card-title mb-0">${{ number_format($summary['unpaid_fines'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Filter">
        <form action="{{ route('librarian.reports.fines') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Payment Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Fines List" class="mt-4">
        @if($fines->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Fine Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fines as $fine)
                            <tr>
                                <td>{{ $fine->book->title ?? 'N/A' }}</td>
                                <td>{{ $fine->member->user->name ?? 'N/A' }}</td>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                <td>{{ $fine->due_date->format('M d, Y') }}</td>
                                <td>{{ $fine->return_date ? $fine->return_date->format('M d, Y') : '-' }}</td>
                                <td class="fw-semibold">${{ number_format($fine->fine_amount, 2) }}</td>
                                <td>
                                    @if($fine->fine_paid)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $fines->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-cash"
                title="No Fines Found"
                description="No fines match your filter criteria."
            />
        @endif
    </x-card>
</div>
@endsection
