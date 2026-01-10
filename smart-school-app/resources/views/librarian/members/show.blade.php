@extends('layouts.app')

@section('title', 'Member Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.members.index') }}">Members</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Member Details</h1>
        <a href="{{ route('librarian.members.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row">
        <div class="col-md-4">
            <x-card title="Member Information">
                <div class="text-center mb-4">
                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($member->user->name ?? 'N', 0, 1)) }}
                    </div>
                    <h5 class="mt-3 mb-1">{{ $member->user->name ?? 'N/A' }}</h5>
                    <span class="badge bg-{{ $member->member_type == 'student' ? 'info' : 'secondary' }}">
                        {{ ucfirst($member->member_type) }}
                    </span>
                </div>

                <hr>

                <div class="mb-3">
                    <p class="mb-1 text-muted">Email</p>
                    <p class="fw-semibold">{{ $member->user->email ?? 'N/A' }}</p>
                </div>

                <div class="mb-3">
                    <p class="mb-1 text-muted">Member Since</p>
                    <p class="fw-semibold">{{ $member->created_at->format('M d, Y') }}</p>
                </div>

                <div class="mb-3">
                    <p class="mb-1 text-muted">Status</p>
                    <p>
                        @if($member->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>

                <hr>

                <form action="{{ route('librarian.members.toggle-status', $member->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-{{ $member->is_active ? 'warning' : 'success' }} w-100">
                        <i class="bi bi-{{ $member->is_active ? 'pause' : 'play' }} me-1"></i>
                        {{ $member->is_active ? 'Deactivate Member' : 'Activate Member' }}
                    </button>
                </form>
            </x-card>

            <x-card title="Statistics" class="mt-4">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="mb-0">{{ $totalIssues }}</h4>
                        <small class="text-muted">Total Issues</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-primary">{{ $activeIssues }}</h4>
                        <small class="text-muted">Active Issues</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="mb-0">${{ number_format($totalFines, 2) }}</h4>
                        <small class="text-muted">Total Fines</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-danger">${{ number_format($unpaidFines, 2) }}</h4>
                        <small class="text-muted">Unpaid Fines</small>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card title="Issue History">
                @if($issues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Fine</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issues as $issue)
                                    <tr>
                                        <td>
                                            <a href="{{ route('librarian.books.show', $issue->book_id) }}" class="text-decoration-none">
                                                {{ $issue->book->title ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                        <td>{{ $issue->due_date->format('M d, Y') }}</td>
                                        <td>{{ $issue->return_date ? $issue->return_date->format('M d, Y') : '-' }}</td>
                                        <td>
                                            @if($issue->fine_amount > 0)
                                                <span class="text-danger">${{ number_format($issue->fine_amount, 2) }}</span>
                                                @if($issue->fine_paid)
                                                    <span class="badge bg-success">Paid</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
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
                        {{ $issues->links() }}
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-journal"
                        title="No Issue History"
                        description="This member has not borrowed any books yet."
                    />
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
