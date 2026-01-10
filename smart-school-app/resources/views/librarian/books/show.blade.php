@extends('layouts.app')

@section('title', 'Book Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.books.index') }}">Books</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Book Details</h1>
        <div>
            <a href="{{ route('librarian.books.edit', $book->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('librarian.books.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <x-card title="{{ $book->title }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Author</p>
                        <p class="fw-semibold">{{ $book->author ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">ISBN</p>
                        <p class="fw-semibold"><code>{{ $book->isbn }}</code></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Category</p>
                        <p class="fw-semibold">{{ $book->category->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Publisher</p>
                        <p class="fw-semibold">{{ $book->publisher ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Edition</p>
                        <p class="fw-semibold">{{ $book->edition ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Publication Year</p>
                        <p class="fw-semibold">{{ $book->publish_year ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Language</p>
                        <p class="fw-semibold">{{ $book->language ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Rack Number</p>
                        <p class="fw-semibold">{{ $book->rack_number ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Pages</p>
                        <p class="fw-semibold">{{ $book->pages ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Price</p>
                        <p class="fw-semibold">{{ $book->price ? number_format($book->price, 2) : 'Not specified' }}</p>
                    </div>
                </div>

                @if($book->description)
                    <div class="mb-3">
                        <p class="mb-1 text-muted">Description</p>
                        <p>{{ $book->description }}</p>
                    </div>
                @endif
            </x-card>

            <x-card title="Recent Issues" class="mt-4">
                @if($book->issues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->issues as $issue)
                                    <tr>
                                        <td>{{ $issue->member->user->name ?? 'N/A' }}</td>
                                        <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                        <td>{{ $issue->due_date->format('M d, Y') }}</td>
                                        <td>{{ $issue->return_date ? $issue->return_date->format('M d, Y') : '-' }}</td>
                                        <td>
                                            @if($issue->isReturned())
                                                <span class="badge bg-success">Returned</span>
                                            @elseif($issue->isOverdue())
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
                @else
                    <p class="text-muted mb-0">No issue history for this book.</p>
                @endif
            </x-card>
        </div>

        <div class="col-md-4">
            <div class="card {{ $book->is_active ? 'bg-success' : 'bg-secondary' }} text-white mb-4">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Status</h6>
                    <h4 class="card-title mb-0">{{ $book->is_active ? 'Active' : 'Inactive' }}</h4>
                </div>
            </div>

            <x-card title="Availability">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="mb-0">{{ $book->quantity }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-success">{{ $book->available_quantity }}</h4>
                        <small class="text-muted">Available</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-warning">{{ $book->quantity - $book->available_quantity }}</h4>
                        <small class="text-muted">Issued</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    @if($book->available_quantity == 0)
                        <span class="badge bg-danger fs-6">Not Available</span>
                    @elseif($book->available_quantity < 3)
                        <span class="badge bg-warning fs-6">Low Stock</span>
                    @else
                        <span class="badge bg-success fs-6">Available</span>
                    @endif
                </div>
            </x-card>

            <x-card title="Quick Actions" class="mt-4">
                <div class="d-grid gap-2">
                    @if($book->isAvailable())
                        <a href="{{ route('librarian.issues.create') }}?book_id={{ $book->id }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-right me-1"></i> Issue This Book
                        </a>
                    @endif
                    <a href="{{ route('librarian.books.edit', $book->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil me-1"></i> Edit Book
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
