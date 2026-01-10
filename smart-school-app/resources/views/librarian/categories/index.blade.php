@extends('layouts.app')

@section('title', 'Book Categories')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Book Categories</h1>
        <a href="{{ route('librarian.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Filter Categories">
        <form action="{{ route('librarian.categories.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Category name" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Categories List" class="mt-4">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Books Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <a href="{{ route('librarian.categories.show', $category->id) }}" class="text-decoration-none fw-semibold">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $category->books_count }} books</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('librarian.categories.show', $category->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('librarian.categories.edit', $category->id) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('librarian.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete" {{ $category->books_count > 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $categories->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-folder"
                title="No Categories Found"
                description="No categories match your search criteria."
            />
        @endif
    </x-card>
</div>
@endsection
