@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Teacher Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ $teacher->name ?? 'Teacher' }}!</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Teacher Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar avatar-xl bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-badge fs-1 text-white"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mb-1">{{ $teacher->name ?? 'Teacher Name' }}</h4>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-envelope me-2"></i>{{ $teacher->email ?? 'teacher@school.com' }}
                            </p>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-telephone me-2"></i>{{ $teacher->phone ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('teacher.profile.edit') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">My Classes</p>
                            <h3 class="mb-0">{{ $myClasses->count() }}</h3>
                            <small class="text-muted">Assigned classes</small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="mb-0">{{ $attendanceSummary['total_students'] }}</h3>
                            <small class="text-muted">In my classes</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Today's Attendance</p>
                            <h3 class="mb-0">{{ $attendanceSummary['attendance_percentage'] }}</h3>
                            <small class="text-{{ $attendanceSummary['classes_pending'] > 0 ? 'warning' : 'success' }}">
                                <i class="bi bi-{{ $attendanceSummary['classes_pending'] > 0 ? 'exclamation-circle' : 'check-circle' }}"></i> 
                                {{ $attendanceSummary['classes_pending'] }} classes pending
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Upcoming Exams</p>
                            <h3 class="mb-0">{{ $upcomingExams->count() }}</h3>
                            <small class="text-info">This month</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-auto">
                            <a href="{{ route('teacher.attendance.mark') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-check me-2"></i>Mark Attendance
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-2"></i>Enter Marks
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('teacher.timetable.index') }}" class="btn btn-info text-white">
                                <i class="bi bi-calendar3 me-2"></i>View Schedule
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('teacher.students.index') }}" class="btn btn-success">
                                <i class="bi bi-people me-2"></i>My Students
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('teacher.messages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-envelope me-2"></i>Messages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule & My Classes -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Today's Schedule</h6>
                    <span class="badge bg-primary">{{ now()->format('l') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Period</th>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaySchedule as $item)
                                    @php
                                        $startTime = \Carbon\Carbon::parse($item->start_time);
                                        $endTime = \Carbon\Carbon::parse($item->end_time);
                                        $now = now();
                                        $status = $now->lt($startTime) ? 'upcoming' : ($now->gt($endTime) ? 'completed' : 'current');
                                    @endphp
                                    <tr class="{{ $status == 'current' ? 'table-primary' : '' }}">
                                        <td><span class="badge bg-secondary">{{ $item->period_number ?? $loop->iteration }}</span></td>
                                        <td>{{ $startTime->format('h:i A') }} - {{ $endTime->format('h:i A') }}</td>
                                        <td><strong>{{ $item->subject->name ?? 'N/A' }}</strong></td>
                                        <td>{{ $item->schoolClass->name ?? 'N/A' }}-{{ $item->section->name ?? '' }}</td>
                                        <td>{{ $item->room_number ?? 'N/A' }}</td>
                                        <td>
                                            @if($status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($status == 'current')
                                                <span class="badge bg-primary"><i class="bi bi-broadcast me-1"></i>Current</span>
                                            @else
                                                <span class="badge bg-secondary">Upcoming</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                            No classes scheduled for today
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>My Classes</h6>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                        @forelse($myClasses as $class)
                            <a href="{{ route('teacher.students.index', ['class_id' => $class['class_id'] ?? null, 'section_id' => $class['section_id'] ?? null]) }}" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $class['class_name'] }}-{{ $class['section_name'] }}</h6>
                                        <small class="text-muted">{{ $class['subject_name'] ?? 'Class Teacher' }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $class['student_count'] }} students</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-building-x fs-3 d-block mb-2"></i>
                                No classes assigned
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Upcoming Exams -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activities</h6>
                    <span class="badge bg-secondary">{{ count($recentActivities) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @forelse($recentActivities as $activity)
                            <div class="list-group-item d-flex align-items-center py-3">
                                <div class="bg-{{ $activity['type'] == 'attendance' ? 'success' : 'info' }} bg-opacity-10 text-{{ $activity['type'] == 'attendance' ? 'success' : 'info' }} rounded-circle p-2 me-3">
                                    <i class="bi bi-{{ $activity['type'] == 'attendance' ? 'calendar-check' : 'pencil-square' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1">{{ $activity['description'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                                No recent activities
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>Upcoming Exams</h6>
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @forelse($upcomingExams as $exam)
                            <a href="{{ route('teacher.exams.show', $exam->id) }}" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                        <i class="bi bi-journal-bookmark"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1">{{ $exam->subject->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</small>
                                        </div>
                                        <p class="text-muted small mb-0">
                                            {{ $exam->schoolClass->name ?? 'N/A' }}-{{ $exam->section->name ?? '' }} | 
                                            {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                No upcoming exams
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Attendance Trend (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Class Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="classPerformanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    
    if (document.getElementById('attendanceTrendChart')) {
        const attendanceCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: chartData.attendanceTrend?.labels || [],
                datasets: [{
                    label: 'Attendance %',
                    data: chartData.attendanceTrend?.data || [],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    if (document.getElementById('classPerformanceChart')) {
        const performanceCtx = document.getElementById('classPerformanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: chartData.classPerformance?.labels || [],
                datasets: [{
                    label: 'Average Marks',
                    data: chartData.classPerformance?.data || [],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
