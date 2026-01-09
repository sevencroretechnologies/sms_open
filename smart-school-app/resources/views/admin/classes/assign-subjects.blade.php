{{-- Assign Subjects to Class View --}}
{{-- Admin class subject assignment form --}}

@extends('layouts.app')

@section('title', 'Assign Subjects')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Assign Subjects</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.show', $class ?? 1) }}">{{ $class->name ?? 'Class' }}</a></li>
                    <li class="breadcrumb-item active">Assign Subjects</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.classes.subjects', $class ?? 1) }}" class="btn btn-outline-secondary">
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

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.classes.store-subjects', $class ?? 1) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <x-card :noPadding="true">
                    <x-slot name="header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <span>
                                <i class="bi bi-book me-2"></i>
                                Select Subjects for {{ $class->name ?? 'Class' }}
                            </span>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleAll()">
                                <label class="form-check-label" for="selectAll">Select All</label>
                            </div>
                        </div>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" class="form-check-input" onclick="toggleAll()" id="headerCheckbox">
                                    </th>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Assign Teacher</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects ?? [] as $subject)
                                    @php
                                        $isAssigned = isset($assignedSubjects) && $assignedSubjects->contains($subject->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" class="form-check-input subject-checkbox" {{ $isAssigned ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="d-inline-flex align-items-center justify-content-center rounded bg-info bg-opacity-10 text-info" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-book"></i>
                                                </span>
                                                <span class="fw-medium">{{ $subject->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $subject->code ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ ucfirst($subject->type ?? 'theory') }}</span>
                                        </td>
                                        <td>
                                            <select name="teachers[{{ $subject->id }}]" class="form-select form-select-sm">
                                                <option value="">Select Teacher</option>
                                                @foreach($teachers ?? [] as $teacher)
                                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-book fs-1 d-block mb-2"></i>
                                                <p class="mb-2">No subjects available</p>
                                                <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus-lg me-1"></i> Create Subject
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.classes.subjects', $class ?? 1) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Assignments
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </div>

            <div class="col-lg-4">
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-info-circle me-2"></i>
                        Class Info
                    </x-slot>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 50px; height: 50px;">
                            <i class="bi bi-mortarboard fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $class->name ?? 'Class Name' }}</h6>
                            <small class="text-muted">{{ $class->sections_count ?? 0 }} sections</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Current Subjects</span>
                            <span>{{ $assignedSubjects->count() ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Students</span>
                            <span>{{ $class->students_count ?? 0 }}</span>
                        </li>
                    </ul>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-lightbulb me-2"></i>
                        Quick Tips
                    </x-slot>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Select subjects to assign to this class
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Optionally assign a teacher to each subject
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Uncheck to remove subject assignment
                        </li>
                    </ul>
                </x-card>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleAll() {
    const checkboxes = document.querySelectorAll('.subject-checkbox');
    const selectAll = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const isChecked = selectAll.checked || headerCheckbox.checked;
    
    checkboxes.forEach(cb => cb.checked = isChecked);
    selectAll.checked = isChecked;
    headerCheckbox.checked = isChecked;
}
</script>
@endpush
