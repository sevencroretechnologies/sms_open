@extends('layouts.app')

@section('title', 'Issue Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.issues.index') }}">Book Issues</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Issue Details</h1>
        <div>
            @if(!$issue->return_date)
                <a href="{{ route('librarian.issues.return', $issue->id) }}" class="btn btn-success">
                    <i class="bi bi-box-arrow-in-left me-1"></i> Return Book
                </a>
            @endif
            <a href="{{ route('librarian.issues.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <x-card title="Book Information">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Title</p>
                        <p class="fw-semibold">
                            <a href="{{ route('librarian.books.show', $issue->book_id) }}" class="text-decoration-none">
                                {{ $issue->book->title ?? 'N/A' }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Author</p>
                        <p class="fw-semibold">{{ $issue->book->author ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">ISBN</p>
                        <p class="fw-semibold"><code>{{ $issue->book->isbn ?? 'N/A' }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Category</p>
                        <p class="fw-semibold">{{ $issue->book->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Member Information" class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Name</p>
                        <p class="fw-semibold">{{ $issue->member->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Type</p>
                        <p>
                            <span class="badge bg-{{ $issue->member->member_type == 'student' ? 'info' : 'secondary' }}">
                                {{ ucfirst($issue->member->member_type ?? 'N/A') }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Email</p>
                        <p class="fw-semibold">{{ $issue->member->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Member Status</p>
                        <p>
                            @if($issue->member->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </x-card>

            @if($issue->remarks)
                <x-card title="Remarks" class="mt-4">
                    <p class="mb-0">{{ $issue->remarks }}</p>
                </x-card>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card {{ $issue->return_date ? 'bg-success' : ($issue->due_date < now() ? 'bg-danger' : 'bg-primary') }} text-white mb-4">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Status</h6>
                    <h4 class="card-title mb-0">
                        @if($issue->return_date)
                            Returned
                        @elseif($issue->due_date < now())
                            Overdue
                        @else
                            Issued
                        @endif
                    </h4>
                </div>
            </div>

            <x-card title="Issue Timeline">
                <div class="mb-3">
                    <p class="mb-1 text-muted">Issue Date</p>
                    <p class="fw-semibold">{{ $issue->issue_date->format('M d, Y') }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1 text-muted">Due Date</p>
                    <p class="fw-semibold {{ !$issue->return_date && $issue->due_date < now() ? 'text-danger' : '' }}">
                        {{ $issue->due_date->format('M d, Y') }}
                        @if(!$issue->return_date && $issue->due_date < now())
                            <br><small class="text-danger">({{ $issue->due_date->diffForHumans() }})</small>
                        @endif
                    </p>
                </div>
                @if($issue->return_date)
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Return Date</p>
                        <p class="fw-semibold">{{ $issue->return_date->format('M d, Y') }}</p>
                    </div>
                @endif
                <div class="mb-3">
                    <p class="mb-1 text-muted">Issued By</p>
                    <p class="fw-semibold">{{ $issue->issuedBy->name ?? 'N/A' }}</p>
                </div>
                @if($issue->return_date && $issue->returnedBy)
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Returned To</p>
                        <p class="fw-semibold">{{ $issue->returnedBy->name ?? 'N/A' }}</p>
                    </div>
                @endif
            </x-card>

            @if($issue->fine_amount > 0)
                <x-card title="Fine Details" class="mt-4">
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Fine Amount</p>
                        <p class="fw-semibold text-danger fs-4">${{ number_format($issue->fine_amount, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Payment Status</p>
                        <p>
                            @if($issue->fine_paid)
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning">Unpaid</span>
                            @endif
                        </p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</div>
@endsection
