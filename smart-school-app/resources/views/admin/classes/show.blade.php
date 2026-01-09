{{-- Class Show View --}}
{{-- Admin class details --}}

@extends('layouts.app')

@section('title', 'Class Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Class Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.classes.edit', $class ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.classes.subjects', $class ?? 1) }}" class="btn btn-info">
                <i class="bi bi-book me-1"></i> Subjects
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Class Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-mortarboard fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $class->name ?? 'Class Name' }}</h4>
                    <span class="badge {{ ($class->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($class->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Numeric Name</span>
                        <span>{{ $class->numeric_name ?? '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Order</span>
                        <span>{{ $class->order ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($class->created_at) ? $class->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>

            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $class->sections_count ?? 0 }}</h3>
                            <small class="text-muted">Sections</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $class->students_count ?? 0 }}</h3>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Sections -->
            <x-card class="mb-4" :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-grid me-2"></i>Sections</span>
                        <a href="{{ route('admin.sections.create', ['class_id' => $class->id ?? 1]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Add Section
                        </a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Section</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($class->sections ?? [] as $section)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $section->name }}</span>
                                    </td>
                                    <td>{{ $section->students_count ?? 0 }}</td>
                                    <td>
                                        <span class="badge {{ $section->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $section->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No sections found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Subjects -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-book me-2"></i>Assigned Subjects</span>
                        <a href="{{ route('admin.classes.assign-subjects', $class ?? 1) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Assign Subjects
                        </a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($class->subjects ?? [] as $subject)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $subject->name }}</span>
                                    </td>
                                    <td>{{ $subject->code ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ ucfirst($subject->type ?? 'theory') }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No subjects assigned
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
