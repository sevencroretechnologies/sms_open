@extends('layouts.app')

@section('title', 'Student Fees')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-collection.index') }}">Fee Collection</a></li>
            <li class="breadcrumb-item active">Student Fees</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Student Fee Details</h1>
        <a href="{{ route('accountant.fees-collection.search') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Search
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <x-card title="Student Information">
                <div class="text-center mb-3">
                    @if($student->user->profile_photo)
                        <img src="{{ asset('storage/' . $student->user->profile_photo) }}" alt="Photo" class="rounded-circle" width="100" height="100">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 36px;">
                            {{ substr($student->user->name ?? 'S', 0, 1) }}
                        </div>
                    @endif
                </div>
                <h5 class="text-center mb-1">{{ $student->user->name ?? 'N/A' }}</h5>
                <p class="text-center text-muted mb-3">{{ $student->admission_no }}</p>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1 text-muted small">Class</p>
                        <p class="fw-semibold">{{ $student->schoolClass->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted small">Section</p>
                        <p class="fw-semibold">{{ $student->section->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card title="Fee Allotments">
                @if($allotments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fee Group</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allotments as $allotment)
                                    <tr>
                                        <td>{{ $allotment->feeGroup->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($allotment->total_amount, 2) }}</td>
                                        <td class="text-success">{{ number_format($allotment->paid_amount, 2) }}</td>
                                        <td class="text-danger">{{ number_format($allotment->balance, 2) }}</td>
                                        <td>
                                            @if($allotment->due_date < now() && $allotment->balance > 0)
                                                <span class="text-danger">{{ \Carbon\Carbon::parse($allotment->due_date)->format('M d, Y') }}</span>
                                            @else
                                                {{ \Carbon\Carbon::parse($allotment->due_date)->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($allotment->balance == 0)
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($allotment->paid_amount > 0)
                                                <span class="badge bg-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($allotment->balance > 0)
                                                <a href="{{ route('accountant.fees-collection.collect', $allotment->id) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-cash"></i> Collect
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-receipt"
                        title="No Fee Allotments"
                        description="No fees have been allotted to this student."
                    />
                @endif
            </x-card>

            <x-card title="Recent Payments" class="mt-4">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Fee Group</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td><code>{{ $transaction->transaction_id }}</code></td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</td>
                                        <td>{{ $transaction->feesAllotment->feeGroup->name ?? 'N/A' }}</td>
                                        <td class="text-success fw-semibold">{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                        <td>
                                            <a href="{{ route('accountant.fees-collection.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt"></i> Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-credit-card"
                        title="No Payments"
                        description="No payment transactions found for this student."
                    />
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
