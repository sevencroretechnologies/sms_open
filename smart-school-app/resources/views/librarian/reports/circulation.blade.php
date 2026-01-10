@extends('layouts.app')

@section('title', 'Circulation Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Circulation</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Circulation Report</h1>
        <a href="{{ route('librarian.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Issues</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['total_issues']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Returns</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['total_returns']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Overdue</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['overdue']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Filter by Date Range">
        <form action="{{ route('librarian.reports.circulation') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Circulation Details ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})" class="mt-4">
        @if($issues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Type</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issues as $issue)
                            <tr>
                                <td>{{ $issue->book->title ?? 'N/A' }}</td>
                                <td>{{ $issue->member->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $issue->member->member_type == 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($issue->member->member_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                <td>{{ $issue->due_date->format('M d, Y') }}</td>
                                <td>{{ $issue->return_date ? $issue->return_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if($issue->return_date)
                                        <span class="badge bg-success">Returned</span>
                                    @elseif($issue->due_date < now())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-primary">Issued</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $issues->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-journal"
                title="No Circulation Data"
                description="No book issues found for the selected date range."
            />
        @endif
    </x-card>
</div>
@endsection
