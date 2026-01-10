@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Add Category</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Add New Category</h1>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Category Details">
        <form action="{{ route('librarian.categories.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Category
                </button>
                <a href="{{ route('librarian.categories.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection
