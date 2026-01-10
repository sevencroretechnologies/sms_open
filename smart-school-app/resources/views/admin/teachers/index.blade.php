@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Teachers</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Teachers</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Teacher
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email, employee ID..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Department</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-25 rounded p-3">
                                <i class="bi bi-person-workspace text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">{{ count($teachers ?? []) }}</h3>
                            <small class="text-muted">Total Teachers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-25 rounded p-3">
                                <i class="bi bi-person-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-25 rounded p-3">
                                <i class="bi bi-gender-male text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Male</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-25 rounded p-3">
                                <i class="bi bi-gender-female text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Female</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-person-workspace me-2"></i>Teachers List</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?format=csv">CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?format=excel">Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?format=pdf">PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Teacher</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Subjects</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers ?? [] as $index => $teacher)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $teacher->name ?? 'N/A' }}</span>
                                        <small class="d-block text-muted">{{ $teacher->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $teacher->employee_id ?? 'N/A' }}</td>
                            <td>{{ $teacher->department ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $teacher->subjects_count ?? 0 }}</span></td>
                            <td>{{ $teacher->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $teacher->is_active ?? true ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $teacher->is_active ?? true ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.teachers.show', $teacher->id ?? 0) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teachers.edit', $teacher->id ?? 0) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $teacher->id ?? 0 }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-workspace fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No teachers found</p>
                                    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Teacher
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
