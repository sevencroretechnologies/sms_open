@extends('layouts.app')

@section('title', 'Collection Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-reports.index') }}">Fee Reports</a></li>
            <li class="breadcrumb-item active">Collection Report</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Collection Report</h1>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>

    <x-card title="Filter by Date Range">
        <form action="{{ route('accountant.fees-reports.collection') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Generate Report
                </button>
            </div>
        </form>
    </x-card>

    <div class="card mt-4 bg-success text-white">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Total Collection: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</h5>
                </div>
                <div class="col-auto">
                    <h3 class="mb-0">{{ number_format($totalAmount, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Transaction Details" class="mt-4">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Fee Group</th>
                            <th>Method</th>
                            <th>Collected By</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><code>{{ $transaction->transaction_id }}</code></td>
                                <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</td>
                                <td>
                                    <div>{{ $transaction->feesAllotment->student->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $transaction->feesAllotment->student->admission_no ?? '' }}</small>
                                </td>
                                <td>{{ $transaction->feesAllotment->student->schoolClass->name ?? '' }}</td>
                                <td>{{ $transaction->feesAllotment->feeGroup->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                <td>{{ $transaction->collector->name ?? 'N/A' }}</td>
                                <td class="text-end text-success fw-semibold">{{ number_format($transaction->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-receipt"
                title="No Transactions"
                description="No fee transactions found for the selected date range."
            />
        @endif
    </x-card>
</div>
@endsection
