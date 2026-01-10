@extends('layouts.app')

@section('title', 'Collect Fee')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-collection.index') }}">Fee Collection</a></li>
            <li class="breadcrumb-item active">Collect Fee</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Collect Fee Payment</h1>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row">
        <div class="col-md-5">
            <x-card title="Student Information">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg me-3">
                        @if($allotment->student->user->profile_photo)
                            <img src="{{ asset('storage/' . $allotment->student->user->profile_photo) }}" alt="Photo" class="rounded-circle" width="64" height="64">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 24px;">
                                {{ substr($allotment->student->user->name ?? 'S', 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $allotment->student->user->name ?? 'N/A' }}</h5>
                        <p class="text-muted mb-0">{{ $allotment->student->admission_no ?? '' }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1 text-muted">Class</p>
                        <p class="fw-semibold">{{ $allotment->student->schoolClass->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted">Section</p>
                        <p class="fw-semibold">{{ $allotment->student->section->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Fee Details" class="mt-4">
                <div class="mb-3">
                    <p class="mb-1 text-muted">Fee Group</p>
                    <p class="fw-semibold">{{ $allotment->feeGroup->name ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1 text-muted">Due Date</p>
                    <p class="fw-semibold {{ $allotment->due_date < now() ? 'text-danger' : '' }}">
                        {{ \Carbon\Carbon::parse($allotment->due_date)->format('M d, Y') }}
                        @if($allotment->due_date < now())
                            <span class="badge bg-danger ms-2">Overdue</span>
                        @endif
                    </p>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-4">
                        <p class="mb-1 text-muted small">Total</p>
                        <p class="fw-bold">{{ number_format($allotment->total_amount, 2) }}</p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1 text-muted small">Paid</p>
                        <p class="fw-bold text-success">{{ number_format($allotment->paid_amount, 2) }}</p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1 text-muted small">Balance</p>
                        <p class="fw-bold text-danger">{{ number_format($allotment->balance, 2) }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-7">
            <x-card title="Payment Form">
                <form action="{{ route('accountant.fees-collection.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="allotment_id" value="{{ $allotment->id }}">

                    <div class="mb-3">
                        <label class="form-label">Amount to Pay <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   step="0.01" min="0.01" max="{{ $allotment->balance }}" 
                                   value="{{ old('amount', $allotment->balance) }}" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum: {{ number_format($allotment->balance, 2) }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3" 
                                  placeholder="Optional notes about this payment">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Process Payment
                        </button>
                        <a href="{{ route('accountant.fees-collection.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
