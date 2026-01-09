{{-- Student Promote Form View --}}
{{-- Admin student promotion form --}}

@extends('layouts.app')

@section('title', 'Promote Students')

@section('content')
<div x-data="promoteManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Promote Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
                    <li class="breadcrumb-item active">Promote</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
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

    <!-- Promotion Form -->
    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf
        
        <!-- Selection Card -->
        <x-card class="mb-4">
            <x-slot name="header">
                <i class="bi bi-filter me-2"></i>
                Select Class and Session
            </x-slot>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Current Session <span class="text-danger">*</span></label>
                    <select name="from_session_id" class="form-select @error('from_session_id') is-invalid @enderror" required x-model="fromSession" @change="loadStudents()">
                        <option value="">Select Session</option>
                        @foreach($academicSessions ?? [] as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('from_session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Class <span class="text-danger">*</span></label>
                    <select name="from_class_id" class="form-select @error('from_class_id') is-invalid @enderror" required x-model="fromClass" @change="loadStudents()">
                        <option value="">Select Class</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('from_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Session <span class="text-danger">*</span></label>
                    <select name="to_session_id" class="form-select @error('to_session_id') is-invalid @enderror" required x-model="toSession">
                        <option value="">Select Session</option>
                        @foreach($academicSessions ?? [] as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('to_session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Class <span class="text-danger">*</span></label>
                    <select name="to_class_id" class="form-select @error('to_class_id') is-invalid @enderror" required x-model="toClass">
                        <option value="">Select Class</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('to_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-card>

        <!-- Students List -->
        <x-card :noPadding="true">
            <x-slot name="header">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span>
                        <i class="bi bi-people me-2"></i>
                        Select Students to Promote
                    </span>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll" @change="toggleSelectAll()">
                        <label class="form-check-label" for="selectAll">Select All</label>
                    </div>
                </div>
            </x-slot>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" @change="toggleSelectAll()" :checked="allSelected">
                            </th>
                            <th>Student</th>
                            <th>Admission No</th>
                            <th>Roll No</th>
                            <th>Current Class</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students ?? [] as $student)
                            <tr>
                                <td>
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px;">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <span class="fw-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ $student->admission_number ?? '-' }}</td>
                                <td>{{ $student->roll_number ?? '-' }}</td>
                                <td>{{ $student->schoolClass->name ?? '-' }}</td>
                                <td>{{ $student->section->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        <p class="mb-0">Select a class and session to view students</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <span x-text="selectedCount">0</span> students selected
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="selectedCount === 0">
                        <i class="bi bi-arrow-up-circle me-1"></i> Promote Selected Students
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
@endsection

@push('scripts')
<script>
function promoteManager() {
    return {
        fromSession: '',
        fromClass: '',
        toSession: '',
        toClass: '',
        selectedCount: 0,
        allSelected: false,

        init() {
            this.updateSelectedCount();
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.addEventListener('change', () => this.updateSelectedCount());
            });
        },

        toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            this.allSelected = !this.allSelected;
            checkboxes.forEach(cb => cb.checked = this.allSelected);
            this.updateSelectedCount();
        },

        updateSelectedCount() {
            this.selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
        },

        loadStudents() {
            if (this.fromSession && this.fromClass) {
                window.location.href = '{{ route("admin.promotions.create") }}?session_id=' + this.fromSession + '&class_id=' + this.fromClass;
            }
        }
    };
}
</script>
@endpush
