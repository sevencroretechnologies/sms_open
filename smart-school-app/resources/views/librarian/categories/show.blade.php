@extends('layouts.app')

@section('title', 'Category Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $category->name }}</h1>
        <div>
            <a href="{{ route('librarian.categories.edit', $category->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('librarian.categories.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <x-card title="Category Info">
                <p class="mb-1 text-muted">Name</p>
                <p class="fw-semibold">{{ $category->name }}</p>

                <p class="mb-1 text-muted">Description</p>
                <p>{{ $category->description ?? 'No description' }}</p>

                <p class="mb-1 text-muted">Status</p>
                <p>
                    @if($category->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </p>

                <p class="mb-1 text-muted">Total Books</p>
                <p class="fw-semibold">{{ $category->books->count() }}</p>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card title="Books in this Category">
                @if($category->books->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Available</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->books as $book)
                                    <tr>
                                        <td>
                                            <a href="{{ route('librarian.books.show', $book->id) }}" class="text-decoration-none">
                                                {{ $book->title }}
                                            </a>
                                        </td>
                                        <td>{{ $book->author ?? '-' }}</td>
                                        <td><code>{{ $book->isbn }}</code></td>
                                        <td>
                                            {{ $book->available_quantity }} / {{ $book->quantity }}
                                        </td>
                                        <td>
                                            <a href="{{ route('librarian.books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-book"
                        title="No Books"
                        description="No books in this category yet."
                    />
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
