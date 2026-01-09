{{-- Class Subjects View --}}
{{-- Admin class subjects management --}}

@extends('layouts.app')

@section('title', 'Class Subjects')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Class Subjects</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.show', $class ?? 1) }}">{{ $class->name ?? 'Class' }}</a></li>
                    <li class="breadcrumb-item active">Subjects</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.classes.assign-subjects', $class ?? 1) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Assign Subjects
            </a>
            <a href="{{ route('admin.classes.show', $class ?? 1) }}" class="btn btn-outline-secondary">
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

    <!-- Class Info -->
    <x-card class="mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px;">
                <i class="bi bi-mortarboard fs-3"></i>
            </div>
            <div>
                <h5 class="mb-1">{{ $class->name ?? 'Class Name' }}</h5>
                <span class="text-muted">{{ $class->subjects_count ?? 0 }} subjects assigned</span>
            </div>
        </div>
    </x-card>

    <!-- Subjects Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-book me-2"></i>
            Assigned Subjects
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Subject Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects ?? [] as $index => $subject)
                        <tr>
                            <td>{{ $index + 1 }}</td>
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
                            <td>{{ $subject->pivot->teacher->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmRemove({{ $subject->id }})">
                                    <i class="bi bi-x-lg"></i> Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-book fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No subjects assigned to this class</p>
                                    <a href="{{ route('admin.classes.assign-subjects', $class ?? 1) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Assign Subjects
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

<!-- Remove Confirmation Modal -->
<div class="modal fade" id="removeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Confirm Remove
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this subject from the class?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="removeForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmRemove(subjectId) {
    const form = document.getElementById('removeForm');
    form.action = '/admin/classes/{{ $class->id ?? 1 }}/subjects/' + subjectId;
    new bootstrap.Modal(document.getElementById('removeModal')).show();
}
</script>
@endpush
