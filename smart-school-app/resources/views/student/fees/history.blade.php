@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.fees.index') }}">My Fees</a></li>
                    <li class="breadcrumb-item active">Payment History</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card title="Payment History">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Fee Group</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td><code>{{ $transaction->transaction_id ?? 'N/A' }}</code></td>
                            <td>{{ $transaction->payment_date ? $transaction->payment_date->format('d M Y h:i A') : 'N/A' }}</td>
                            <td>{{ $transaction->feesAllotment->feeGroup->name ?? 'N/A' }}</td>
                            <td class="text-success fw-bold">{{ number_format($transaction->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($transaction->payment_method ?? 'N/A') }}</span>
                            </td>
                            <td>
                                @if($transaction->receipt_path)
                                    <a href="{{ asset('storage/' . $transaction->receipt_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-0">No payment records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </x-card>

    <div class="mt-4">
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Fees
        </a>
    </div>
</div>
@endsection
