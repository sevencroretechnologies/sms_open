{{-- Academic Sessions List View --}}
{{-- Admin academic sessions listing page --}}

@extends('layouts.app')

@section('title', 'Academic Sessions')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Academic Sessions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Academic Sessions</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.academic-sessions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Session
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

    <!-- Sessions Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-calendar-range me-2"></i>
            Academic Sessions List
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Session Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Current</th>
                        <th>Status</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicSessions ?? [] as $index => $session)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <span class="fw-medium">{{ $session->name }}</span>
                                </div>
                            </td>
                            <td>{{ $session->start_date ? $session->start_date->format('d M Y') : '-' }}</td>
                            <td>{{ $session->end_date ? $session->end_date->format('d M Y') : '-' }}</td>
                            <td>
                                @if($session->is_current)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Current</span>
                                @else
                                    <form action="{{ route('admin.academic-sessions.set-current', $session) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Set Current</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $session->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.academic-sessions.show', $session) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.academic-sessions.edit', $session) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $session->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-range fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No academic sessions found</p>
                                    <a href="{{ route('admin.academic-sessions.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Session
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
                <p>Are you sure you want to delete this academic session?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This may affect related student records.
                </p>
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
    form.action = '/admin/academic-sessions/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
