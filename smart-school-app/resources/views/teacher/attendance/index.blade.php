@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
<div class="container-fluid" x-data="attendanceIndexManager()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Attendance History</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.attendance.mark') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Mark Attendance
            </a>
            <a href="{{ route('teacher.attendance.report') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Reports
            </a>
        </div>
    </div>

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

    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>Filter Attendance
        </x-slot>
        
        <form method="GET" action="{{ route('teacher.attendance.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
                    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-calendar-check me-2"></i>Attendance Records
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $attendance->student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($attendance->student->user->name ?? 'S') . '&background=4f46e5&color=fff' }}" 
                                         class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <span class="fw-medium">{{ $attendance->student->user->name ?? 'N/A' }}</span>
                                        <br>
                                        <small class="text-muted">{{ $attendance->student->admission_number ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $attendance->schoolClass->display_name ?? 'N/A' }}</td>
                            <td>{{ $attendance->section->display_name ?? 'N/A' }}</td>
                            <td>
                                @if($attendance->attendanceType)
                                    <span class="badge" style="background-color: {{ $attendance->attendanceType->color }}">
                                        {{ $attendance->attendanceType->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $attendance->remarks ?? '-' }}</td>
                            <td>
                                <a href="{{ route('teacher.attendance.edit', $attendance->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">No attendance records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator && $attendances->hasPages())
            <div class="card-footer">
                {{ $attendances->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script>
function attendanceIndexManager() {
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
