@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-collection.index') }}">Fee Collection</a></li>
            <li class="breadcrumb-item active">Receipt</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Payment Receipt</h1>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
            <a href="{{ route('accountant.fees-collection.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="card" id="receipt">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h2 class="mb-1">Payment Receipt</h2>
                <p class="text-muted mb-0">Transaction ID: {{ $transaction->transaction_id }}</p>
            </div>

            <hr>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Student Information</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $transaction->feesAllotment->student->user->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Admission No:</strong> {{ $transaction->feesAllotment->student->admission_no ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Class:</strong> {{ $transaction->feesAllotment->student->schoolClass->name ?? '' }} - {{ $transaction->feesAllotment->student->section->name ?? '' }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="text-muted mb-2">Receipt Details</h6>
                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</p>
                    <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
                    <p class="mb-1"><strong>Collected By:</strong> {{ $transaction->collector->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>{{ $transaction->feesAllotment->feeGroup->name ?? 'Fee Payment' }}</strong>
                                <br>
                                <small class="text-muted">Fee Group Payment</small>
                            </td>
                            <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th>Total Paid</th>
                            <th class="text-end">{{ number_format($transaction->amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Fee Summary</h6>
                    <p class="mb-1"><strong>Total Fee:</strong> {{ number_format($transaction->feesAllotment->total_amount, 2) }}</p>
                    <p class="mb-1"><strong>Total Paid:</strong> <span class="text-success">{{ number_format($transaction->feesAllotment->paid_amount, 2) }}</span></p>
                    <p class="mb-1"><strong>Balance Due:</strong> <span class="text-danger">{{ number_format($transaction->feesAllotment->balance, 2) }}</span></p>
                </div>
                <div class="col-md-6">
                    @if($transaction->remarks)
                        <h6 class="text-muted mb-2">Remarks</h6>
                        <p>{{ $transaction->remarks }}</p>
                    @endif
                </div>
            </div>

            <hr>

            <div class="text-center text-muted">
                <small>This is a computer-generated receipt and does not require a signature.</small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt, #receipt * {
            visibility: visible;
        }
        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .breadcrumb, .btn, nav {
            display: none !important;
        }
    }
</style>
@endpush
@endsection
