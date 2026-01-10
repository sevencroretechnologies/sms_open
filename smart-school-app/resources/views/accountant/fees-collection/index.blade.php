@extends('layouts.app')

@section('title', 'Fee Collection')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Fee Collection</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Fee Collection</h1>
        <a href="{{ route('accountant.fees-collection.search') }}" class="btn btn-primary">
            <i class="bi bi-search me-1"></i> Search Student
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Filter Pending Fees">
        <form action="{{ route('accountant.fees-collection.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-select">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Student name or admission no" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Pending Fee Payments" class="mt-4">
        @if($pendingFees->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class/Section</th>
                            <th>Fee Group</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingFees as $fee)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $fee->student->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $fee->student->admission_no ?? '' }}</small>
                                </td>
                                <td>{{ $fee->student->schoolClass->name ?? '' }} - {{ $fee->student->section->name ?? '' }}</td>
                                <td>{{ $fee->feeGroup->name ?? 'N/A' }}</td>
                                <td>{{ number_format($fee->total_amount, 2) }}</td>
                                <td class="text-success">{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="text-danger fw-semibold">{{ number_format($fee->balance, 2) }}</td>
                                <td>
                                    @if($fee->due_date < now())
                                        <span class="text-danger">{{ \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') }}</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('accountant.fees-collection.collect', $fee->id) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cash me-1"></i> Collect
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingFees->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-cash-stack"
                title="No Pending Fees"
                description="There are no pending fee payments matching your criteria."
            />
        @endif
    </x-card>
</div>
@endsection
