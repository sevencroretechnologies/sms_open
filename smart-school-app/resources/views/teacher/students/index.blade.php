@extends('layouts.app')

@section('title', 'My Students')

@section('content')
<div class="container-fluid" x-data="studentListManager()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>Filter Students
        </x-slot>
        
        <form method="GET" action="{{ route('teacher.students.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Roll No, Admission No" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select" @change="loadSections($event.target.value)">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select">
                        <option value="">All Sections</option>
                        <template x-for="section in sections" :key="section.id">
                            <option :value="section.id" x-text="section.display_name"></option>
                        </template>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-people me-2"></i>Students ({{ $students->total() }})
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Roll No</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name ?? 'S') . '&background=4f46e5&color=fff' }}" 
                                         class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <span class="fw-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                        <br>
                                        <small class="text-muted">{{ $student->admission_number ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->roll_number ?? '-' }}</td>
                            <td>{{ $student->schoolClass->display_name ?? 'N/A' }}</td>
                            <td>{{ $student->section->display_name ?? 'N/A' }}</td>
                            <td>
                                <small>{{ $student->user->email ?? '-' }}</small>
                                <br>
                                <small class="text-muted">{{ $student->user->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('teacher.students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('teacher.students.attendance', $student->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-calendar-check"></i>
                                    </a>
                                    <a href="{{ route('teacher.students.marks', $student->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-journal-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">No students found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="card-footer">
                {{ $students->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script>
function studentListManager() {
    return {
        sections: [],
        
        async loadSections(classId) {
            if (!classId) {
                this.sections = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/classes/${classId}/sections`);
                if (response.ok) {
                    this.sections = await response.json();
                }
            } catch (error) {
                console.error('Error loading sections:', error);
            }
        },
        
        init() {
            const classId = '{{ request('class_id') }}';
            if (classId) {
                this.loadSections(classId);
            }
        }
    }
}
</script>
@endpush
