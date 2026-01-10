@extends('layouts.app')

@section('title', 'Expense Categories')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.expenses.index') }}">Expenses</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expense Categories</h1>
        <a href="{{ route('accountant.expenses.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Expenses
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
            <x-card title="Add New Category">
                <form action="{{ route('accountant.expenses.store-category') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Add Category
                    </button>
                </form>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card title="Categories List">
                @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Expenses Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td class="fw-semibold">{{ $category->name }}</td>
                                        <td>{{ $category->description ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $category->expenses_count }} expenses</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $categories->links() }}
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-tags"
                        title="No Categories"
                        description="No expense categories have been created yet."
                    />
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
