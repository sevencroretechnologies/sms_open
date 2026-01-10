@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')
<div class="container-fluid" x-data="attendanceReportManager()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Attendance Report</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.attendance.mark') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Mark Attendance
            </a>
        </div>
    </div>

    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>Generate Report
        </x-slot>
        
        <form method="GET" action="{{ route('teacher.attendance.report') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" @change="loadSections($event.target.value)" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-select" required>
                        <option value="">Select Section</option>
                        <template x-for="section in sections" :key="section.id">
                            <option :value="section.id" x-text="section.display_name" :selected="section.id == '{{ request('section_id') }}'"></option>
                        </template>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Generate
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    @if(count($reportData) > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ count($reportData) }}</h3>
                        <small>Total Students</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ collect($reportData)->avg('percentage') ? number_format(collect($reportData)->avg('percentage'), 1) : 0 }}%</h3>
                        <small>Average Attendance</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ collect($reportData)->filter(fn($r) => $r['percentage'] < 75)->count() }}</h3>
                        <small>Below 75%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ collect($reportData)->sum('absent') }}</h3>
                        <small>Total Absences</small>
                    </div>
                </div>
            </div>
        </div>

        <x-card :noPadding="true">
            <x-slot name="header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span><i class="bi bi-table me-2"></i>Attendance Summary</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </x-slot>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th class="text-center">Total Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Late</th>
                            <th class="text-center">Leave</th>
                            <th class="text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $data['student']->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($data['student']->user->name ?? 'S') . '&background=4f46e5&color=fff' }}" 
                                             class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        <div>
                                            <span class="fw-medium">{{ $data['student']->user->name ?? 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $data['student']->roll_number ?? $data['student']->admission_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $data['total_days'] }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $data['present'] }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $data['absent'] }}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark">{{ $data['late'] }}</span></td>
                                <td class="text-center"><span class="badge bg-info">{{ $data['leave'] }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 100px;">
                                            <div class="progress-bar {{ $data['percentage'] >= 75 ? 'bg-success' : ($data['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                        <span class="fw-medium {{ $data['percentage'] >= 75 ? 'text-success' : ($data['percentage'] >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ $data['percentage'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @else
        <x-card class="text-center py-5">
            <i class="bi bi-file-earmark-bar-graph fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Select Class & Date Range</h5>
            <p class="text-muted mb-0">Please select a class, section, and date range to generate the attendance report.</p>
        </x-card>
    @endif
</div>
@endsection

@push('scripts')
<script>
function attendanceReportManager() {
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
