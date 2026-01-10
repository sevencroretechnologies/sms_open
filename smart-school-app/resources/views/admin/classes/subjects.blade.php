@extends('layouts.app')

@section('title', 'Class Subjects')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Class Subjects</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item active">Subjects</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.class-subjects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Assign Subjects
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes ?? [] as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Subject</label>
                        <select name="subject_id" class="form-select">
                            <option value="">All Subjects</option>
                            @foreach($subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-book me-2"></i>Subject Assignments
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-book fs-1 d-block mb-2"></i>
                                <p class="mb-2">No subject assignments found</p>
                                <a href="{{ route('admin.class-subjects.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Assign Subjects
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
