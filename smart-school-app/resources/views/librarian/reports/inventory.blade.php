@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Inventory</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Book Inventory Report</h1>
        <a href="{{ route('librarian.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Books</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['total_books']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Copies</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['total_copies']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Available</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary['available_copies']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Value</h6>
                    <h3 class="card-title mb-0">${{ number_format($summary['total_value'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <x-card title="Filter">
        <form action="{{ route('librarian.reports.inventory') }}" method="GET" class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Book Inventory" class="mt-4">
        @if($books->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Available</th>
                            <th>Issued</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author ?? '-' }}</td>
                                <td><code>{{ $book->isbn }}</code></td>
                                <td>{{ $book->category->name ?? 'N/A' }}</td>
                                <td>{{ $book->quantity }}</td>
                                <td class="text-success">{{ $book->available_quantity }}</td>
                                <td class="text-warning">{{ $book->quantity - $book->available_quantity }}</td>
                                <td>{{ $book->price ? '$' . number_format($book->price, 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $books->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-book"
                title="No Books Found"
                description="No books match your filter criteria."
            />
        @endif
    </x-card>
</div>
@endsection
