@extends('layouts.app')

@section('title', 'Due Fees Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-reports.index') }}">Fee Reports</a></li>
            <li class="breadcrumb-item active">Due Fees Report</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Due Fees Report</h1>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>

    <x-card title="Filter">
        <form action="{{ route('accountant.fees-reports.due') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <div class="card mt-4 bg-warning text-dark">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Total Due Amount</h5>
                </div>
                <div class="col-auto">
                    <h3 class="mb-0">{{ number_format($totalDue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Due Fee List" class="mt-4">
        @if($dueList->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class/Section</th>
                            <th>Fee Group</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dueList as $fee)
                            <tr>
                                <td>
                                    <div>{{ $fee->student->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $fee->student->admission_no ?? '' }}</small>
                                </td>
                                <td>{{ $fee->student->schoolClass->name ?? '' }} - {{ $fee->student->section->name ?? '' }}</td>
                                <td>{{ $fee->feeGroup->name ?? 'N/A' }}</td>
                                <td>{{ number_format($fee->total_amount, 2) }}</td>
                                <td class="text-success">{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="text-danger fw-semibold">{{ number_format($fee->balance, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('accountant.fees-collection.collect', $fee->id) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cash"></i> Collect
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $dueList->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-check-circle"
                title="No Due Fees"
                description="All fees have been collected. Great job!"
            />
        @endif
    </x-card>
</div>
@endsection
