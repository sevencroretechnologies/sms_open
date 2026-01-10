@extends('layouts.app')

@section('title', 'Assign Subjects to Class')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Assign Subjects to Class</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">Assign Subjects</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.class-subjects.store') }}">
        @csrf

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-building me-2"></i>Select Class
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" class="form-select">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-book me-2"></i>Select Subjects
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Subject</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Teacher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subjects ?? [] as $subject)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input" name="subjects[]" value="{{ $subject->id }}">
                                            </td>
                                            <td>{{ $subject->name }}</td>
                                            <td>{{ $subject->code ?? '-' }}</td>
                                            <td>{{ ucfirst($subject->type ?? 'theory') }}</td>
                                            <td>
                                                <select name="teachers[{{ $subject->id }}]" class="form-select form-select-sm">
                                                    <option value="">Select Teacher</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                No subjects available. <a href="{{ route('admin.subjects.create') }}">Add subjects first</a>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.class-subjects.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Assign Subjects
            </button>
        </div>
    </form>
</div>
@endsection
