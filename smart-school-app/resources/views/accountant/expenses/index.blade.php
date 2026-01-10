@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Expenses</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expenses Management</h1>
        <div>
            <a href="{{ route('accountant.expenses.categories') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="{{ route('accountant.expenses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Expense
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="card bg-info text-white mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">This Month's Expenses</h5>
                </div>
                <div class="col-auto">
                    <h3 class="mb-0">{{ number_format($totalExpenses, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Filter Expenses">
        <form action="{{ route('accountant.expenses.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by title" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Expenses List" class="mt-4">
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Invoice No</th>
                            <th class="text-end">Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('accountant.expenses.show', $expense->id) }}" class="text-decoration-none">
                                        {{ $expense->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $expense->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $expense->vendor ?? '-' }}</td>
                                <td>{{ $expense->invoice_no ?? '-' }}</td>
                                <td class="text-end fw-semibold">{{ number_format($expense->amount, 2) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('accountant.expenses.show', $expense->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('accountant.expenses.edit', $expense->id) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('accountant.expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $expenses->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-receipt"
                title="No Expenses"
                description="No expenses found matching your criteria."
            />
        @endif
    </x-card>
</div>
@endsection
