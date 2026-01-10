@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.library.index') }}">Library</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($book->title, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <x-card>
                <div class="text-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="img-fluid mb-3" style="max-height: 300px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 200px; height: 300px;">
                            <i class="fas fa-book fa-4x text-muted"></i>
                        </div>
                    @endif
                    
                    <span class="badge bg-{{ $book->quantity > 0 ? 'success' : 'danger' }} fs-6">
                        {{ $book->quantity > 0 ? $book->quantity . ' Copies Available' : 'Not Available' }}
                    </span>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card>
                <h4 class="mb-3">{{ $book->title }}</h4>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Author</label>
                        <p class="mb-0 fw-medium">{{ $book->author ?? 'Unknown' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Publisher</label>
                        <p class="mb-0 fw-medium">{{ $book->publisher ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">ISBN</label>
                        <p class="mb-0 fw-medium">{{ $book->isbn ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Edition</label>
                        <p class="mb-0 fw-medium">{{ $book->edition ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Publication Year</label>
                        <p class="mb-0 fw-medium">{{ $book->publication_year ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Category</label>
                        <p class="mb-0 fw-medium">{{ $book->category->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Rack/Shelf</label>
                        <p class="mb-0 fw-medium">{{ $book->rack_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Total Copies</label>
                        <p class="mb-0 fw-medium">{{ $book->total_quantity ?? $book->quantity ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($book->description)
                    <hr>
                    <h6 class="mb-2">Description</h6>
                    <p class="text-muted">{{ $book->description }}</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('student.library.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Library
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
