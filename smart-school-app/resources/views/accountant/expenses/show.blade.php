@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.expenses.index') }}">Expenses</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expense Details</h1>
        <div>
            <a href="{{ route('accountant.expenses.edit', $expense->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('accountant.expenses.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <x-card title="{{ $expense->title }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Category</p>
                        <p class="fw-semibold">
                            <span class="badge bg-secondary">{{ $expense->category->name ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Expense Date</p>
                        <p class="fw-semibold">{{ \Carbon\Carbon::parse($expense->expense_date)->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Vendor</p>
                        <p class="fw-semibold">{{ $expense->vendor ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Invoice No</p>
                        <p class="fw-semibold">{{ $expense->invoice_no ?? 'Not specified' }}</p>
                    </div>
                </div>

                @if($expense->description)
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Description</p>
                        <p>{{ $expense->description }}</p>
                    </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Created By</p>
                        <p class="fw-semibold">{{ $expense->createdBy->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Created At</p>
                        <p class="fw-semibold">{{ $expense->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Amount</h6>
                    <h2 class="card-title mb-0">{{ number_format($expense->amount, 2) }}</h2>
                </div>
            </div>

            <x-card title="Actions" class="mt-4">
                <div class="d-grid gap-2">
                    <a href="{{ route('accountant.expenses.edit', $expense->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Expense
                    </a>
                    <form action="{{ route('accountant.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Delete Expense
                        </button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
