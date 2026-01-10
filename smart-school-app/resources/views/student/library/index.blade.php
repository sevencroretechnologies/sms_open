@extends('layouts.app')

@section('title', 'Library')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Library</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('student.library.index') }}">Browse Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('student.library.issued') }}">My Issued Books</a>
                </li>
            </ul>
        </div>
    </div>

    <x-card>
        <form method="GET" class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by title, author, or ISBN..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="row">
            @forelse($books as $book)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="me-3" style="width: 80px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 120px;">
                                        <i class="fas fa-book fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="card-title mb-1">{{ $book->title }}</h6>
                                    <p class="text-muted small mb-1">{{ $book->author ?? 'Unknown Author' }}</p>
                                    @if($book->isbn)
                                        <p class="text-muted small mb-1">ISBN: {{ $book->isbn }}</p>
                                    @endif
                                    <span class="badge bg-{{ $book->quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $book->quantity > 0 ? $book->quantity . ' Available' : 'Not Available' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('student.library.show', $book->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No books found.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($books->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
