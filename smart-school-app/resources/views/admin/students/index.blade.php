{{-- Students List View --}}
{{-- Admin students listing page with CRUD operations --}}

@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div x-data="studentsListManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.student-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-up-circle me-1"></i> Promotions
            </a>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Student
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

    <!-- Filters Card -->
    <x-card class="mb-4">
        <div class="row g-3">
            <!-- Search -->
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control border-start-0" 
                        placeholder="Search by name, admission no..."
                        x-model="filters.search"
                        @input.debounce.300ms="applyFilters()"
                    >
                </div>
            </div>

            <!-- Class Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Class</label>
                <select class="form-select" x-model="filters.class_id" @change="applyFilters()">
                    <option value="">All Classes</option>
                    @foreach($classes ?? [] as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Section Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Section</label>
                <select class="form-select" x-model="filters.section_id" @change="applyFilters()">
                    <option value="">All Sections</option>
                    @foreach($sections ?? [] as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Session Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Session</label>
                <select class="form-select" x-model="filters.session_id" @change="applyFilters()">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions ?? [] as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select" x-model="filters.status" @change="applyFilters()">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="clearFilters()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </x-card>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-25 rounded p-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" x-text="stats.total">{{ $students->total() ?? 0 }}</h3>
                            <small class="text-muted">Total Students</small>
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
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" x-text="stats.active">0</h3>
                            <small class="text-muted">Active</small>
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
                                <i class="bi bi-gender-male text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" x-text="stats.male">0</h3>
                            <small class="text-muted">Male</small>
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
                                <i class="bi bi-gender-female text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" x-text="stats.female">0</h3>
                            <small class="text-muted">Female</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-people me-2"></i>
                    Student List
                    <span class="badge bg-primary ms-2">{{ $students->total() ?? 0 }}</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <select class="form-select form-select-sm" style="width: auto;" x-model="perPage" @change="applyFilters()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Student</th>
                        <th>Admission No</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students ?? [] as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <div>
                                        <span class="fw-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                        <small class="d-block text-muted">{{ $student->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->admission_number ?? '-' }}</td>
                            <td>{{ $student->schoolClass->name ?? '-' }}</td>
                            <td>{{ $student->section->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $student->gender === 'male' ? 'bg-info' : 'bg-warning' }}">
                                    {{ ucfirst($student->gender ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $student->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No students found</p>
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Student
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($students) && $students->hasPages())
        <x-slot name="footer">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                </div>
                {{ $students->links() }}
            </div>
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
                <p>Are you sure you want to delete this student?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This action cannot be undone.
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
function studentsListManager() {
    return {
        filters: {
            search: '',
            class_id: '',
            section_id: '',
            session_id: '',
            status: ''
        },
        perPage: 15,
        stats: {
            total: {{ $students->total() ?? 0 }},
            active: 0,
            male: 0,
            female: 0
        },

        applyFilters() {
            const params = new URLSearchParams();
            if (this.filters.search) params.append('search', this.filters.search);
            if (this.filters.class_id) params.append('class_id', this.filters.class_id);
            if (this.filters.section_id) params.append('section_id', this.filters.section_id);
            if (this.filters.session_id) params.append('session_id', this.filters.session_id);
            if (this.filters.status) params.append('status', this.filters.status);
            params.append('per_page', this.perPage);
            window.location.href = '{{ route("admin.students.index") }}?' + params.toString();
        },

        clearFilters() {
            this.filters = { search: '', class_id: '', section_id: '', session_id: '', status: '' };
            window.location.href = '{{ route("admin.students.index") }}';
        }
    };
}

function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/admin/students/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
