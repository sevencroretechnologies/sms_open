{{-- Teachers List View --}}
{{-- Admin teachers listing page --}}

@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
<div x-data="{ search: '{{ request('search') }}' }">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Teachers</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
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

    <!-- Search -->
    <x-card class="mb-4">
        <form action="{{ route('admin.teachers.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, employee ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Teachers Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-person-badge me-2"></i>
            Teachers List
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subjects</th>
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
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person-badge"></i>
                                    </span>
                                    <div>
                                        <span class="fw-medium">{{ $teacher->user->name ?? 'N/A' }}</span>
                                        <small class="d-block text-muted">{{ $teacher->designation ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $teacher->employee_id ?? '-' }}</td>
                            <td>{{ $teacher->user->email ?? '-' }}</td>
                            <td>{{ $teacher->phone ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $teacher->subjects_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge {{ ($teacher->is_active ?? true) ? 'bg-success' : 'bg-danger' }}">
                                    {{ ($teacher->is_active ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $teacher->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
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

        @if(isset($teachers) && $teachers->hasPages())
        <x-slot name="footer">
            {{ $teachers->links() }}
        </x-slot>
        @endif
    </x-card>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this teacher?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/admin/teachers/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
