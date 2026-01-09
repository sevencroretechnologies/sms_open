@extends('layouts.app')

@section('title', 'Subjects')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Subjects</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Subjects</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Subject
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book me-2"></i>Subjects List</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.subjects.export') }}?format=csv">CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.subjects.export') }}?format=excel">Excel</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Subject Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Classes</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects ?? [] as $index => $subject)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-info bg-opacity-10 rounded p-2">
                                        <i class="bi bi-book text-info"></i>
                                    </div>
                                    <span class="fw-medium">{{ $subject->name }}</span>
                                </div>
                            </td>
                            <td>{{ $subject->code ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($subject->type ?? 'theory') }}</span></td>
                            <td><span class="badge bg-light text-dark">{{ $subject->classes_count ?? 0 }}</span></td>
                            <td>
                                <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-book fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No subjects found</p>
                                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Subject
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
