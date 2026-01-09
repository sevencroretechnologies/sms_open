{{-- Subject Show View --}}
{{-- Admin subject details --}}

@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Subject Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.subjects.edit', $subject ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
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
            <!-- Subject Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10 text-info mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-book fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $subject->name ?? 'Subject Name' }}</h4>
                    <p class="text-muted mb-2">{{ $subject->code ?? 'No Code' }}</p>
                    <span class="badge {{ ($subject->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($subject->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Type</span>
                        <span class="badge bg-light text-dark">{{ ucfirst($subject->type ?? 'theory') }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Classes</span>
                        <span>{{ $subject->classes_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($subject->created_at) ? $subject->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>

            <!-- Description -->
            @if($subject->description ?? false)
            <x-card class="mb-4">
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Description
                </x-slot>
                <p class="mb-0">{{ $subject->description }}</p>
            </x-card>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Assigned Classes -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-mortarboard me-2"></i>
                    Assigned Classes
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Class</th>
                                <th>Teacher</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subject->classes ?? [] as $index => $class)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.classes.show', $class) }}" class="text-decoration-none fw-medium">
                                            {{ $class->name }}
                                        </a>
                                    </td>
                                    <td>{{ $class->pivot->teacher->name ?? '-' }}</td>
                                    <td>{{ $class->students_count ?? 0 }}</td>
                                    <td>
                                        <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Not assigned to any class
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
