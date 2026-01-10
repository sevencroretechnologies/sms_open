@extends('layouts.app')

@section('title', 'Overdue Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Overdue</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Overdue Books Report</h1>
        <a href="{{ route('librarian.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Overdue Books</h6>
                    <h3 class="card-title mb-0">{{ number_format($totalOverdue) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Estimated Fines Due</h6>
                    <h3 class="card-title mb-0">${{ number_format($totalFinesDue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Overdue Books">
        @if($overdueIssues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Est. Fine</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueIssues as $issue)
                            @php
                                $daysOverdue = now()->diffInDays($issue->due_date);
                                $estimatedFine = $daysOverdue * 1.00;
                            @endphp
                            <tr>
                                <td>{{ $issue->book->title ?? 'N/A' }}</td>
                                <td>{{ $issue->member->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $issue->member->member_type == 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($issue->member->member_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $issue->member->user->email ?? 'N/A' }}</td>
                                <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                <td class="text-danger">{{ $issue->due_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $daysOverdue }} days</span>
                                </td>
                                <td class="text-danger fw-semibold">${{ number_format($estimatedFine, 2) }}</td>
                                <td>
                                    <a href="{{ route('librarian.issues.return', $issue->id) }}" class="btn btn-sm btn-success" title="Return">
                                        <i class="bi bi-box-arrow-in-left"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $overdueIssues->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-check-circle"
                title="No Overdue Books"
                description="All books have been returned on time. Great job!"
            />
        @endif
    </x-card>
</div>
@endsection
