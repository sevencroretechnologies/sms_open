@extends('layouts.app')

@section('title', 'Overdue Books')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.issues.index') }}">Book Issues</a></li>
            <li class="breadcrumb-item active">Overdue Books</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Overdue Books</h1>
        <a href="{{ route('librarian.issues.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Issues
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Overdue</h6>
                    <h3 class="card-title mb-0">{{ $overdueIssues->total() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Overdue Books List">
        @if($overdueIssues->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Type</th>
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
                                <td>
                                    <a href="{{ route('librarian.books.show', $issue->book_id) }}" class="text-decoration-none">
                                        {{ $issue->book->title ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>{{ $issue->member->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $issue->member->member_type == 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($issue->member->member_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                <td class="text-danger">{{ $issue->due_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $daysOverdue }} days</span>
                                </td>
                                <td class="text-danger fw-semibold">${{ number_format($estimatedFine, 2) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('librarian.issues.show', $issue->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('librarian.issues.return', $issue->id) }}" class="btn btn-outline-success" title="Return">
                                            <i class="bi bi-box-arrow-in-left"></i>
                                        </a>
                                    </div>
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
                description="All books have been returned on time."
            />
        @endif
    </x-card>
</div>
@endsection
