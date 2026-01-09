{{-- Section Show View --}}
{{-- Admin section details --}}

@extends('layouts.app')

@section('title', 'Section Details')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Section Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.sections.edit', $section ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
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
            <!-- Section Card -->
            <x-card class="mb-4">
                <div class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-grid fs-1"></i>
                    </div>
                    <h4 class="mb-1">{{ $section->name ?? 'Section Name' }}</h4>
                    <p class="text-muted mb-2">{{ $section->schoolClass->name ?? 'Class' }}</p>
                    <span class="badge {{ ($section->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                        {{ ($section->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Capacity</span>
                        <span>{{ $section->capacity ?? 'Unlimited' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Students</span>
                        <span>{{ $section->students_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ isset($section->created_at) ? $section->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-lg-8">
            <!-- Students List -->
            <x-card :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-people me-2"></i>Students in Section</span>
                        <a href="{{ route('admin.students.index', ['section_id' => $section->id ?? 1]) }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Admission No</th>
                                <th>Roll No</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($section->students ?? [] as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.students.show', $student) }}" class="text-decoration-none">
                                            {{ $student->user->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $student->admission_number ?? '-' }}</td>
                                    <td>{{ $student->roll_number ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $student->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No students in this section
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
