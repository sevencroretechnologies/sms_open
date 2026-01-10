@extends('layouts.app')

@section('title', 'Book Issues')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Book Issues</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Book Issues</h1>
        <div>
            <a href="{{ route('librarian.issues.overdue') }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-exclamation-triangle me-1"></i> Overdue Books
            </a>
            <a href="{{ route('librarian.issues.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Issue Book
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Filter Issues">
        <form action="{{ route('librarian.issues.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Currently Issued</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Book title or member name" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Issues List" class="mt-4">
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
                            <th>Actions</th>
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
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('librarian.issues.show', $issue->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$issue->return_date)
                                            <a href="{{ route('librarian.issues.return', $issue->id) }}" class="btn btn-outline-success" title="Return">
                                                <i class="bi bi-box-arrow-in-left"></i>
                                            </a>
                                        @endif
                                    </div>
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
                icon="bi-journal-arrow-up"
                title="No Issues Found"
                description="No book issues match your search criteria."
            />
        @endif
    </x-card>
</div>
@endsection
