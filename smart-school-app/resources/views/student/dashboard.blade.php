@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Student Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ $profileData['name'] }}!</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if($profileData['photo'])
                                <img src="{{ asset('storage/' . $profileData['photo']) }}" alt="Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="avatar avatar-xl bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-mortarboard fs-1 text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h4 class="mb-1">{{ $profileData['name'] }}</h4>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-hash me-2"></i>Roll No: {{ $profileData['roll_number'] }}
                            </p>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-building me-2"></i>Class: {{ $profileData['class_name'] }}-{{ $profileData['section_name'] }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('student.profile.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-person me-1"></i>View Profile
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
                            <p class="text-muted small mb-1">Attendance</p>
                            <h3 class="mb-0">{{ $attendanceSummary['percentage'] }}%</h3>
                            <small class="text-{{ $attendanceSummary['percentage'] >= 75 ? 'success' : ($attendanceSummary['percentage'] >= 50 ? 'warning' : 'danger') }}">
                                <i class="bi bi-{{ $attendanceSummary['percentage'] >= 75 ? 'arrow-up' : 'arrow-down' }}"></i> {{ $attendanceSummary['status'] }}
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
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
                            <small class="text-muted">Scheduled exams</small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-journal-text"></i>
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
                            <p class="text-muted small mb-1">Pending Fees</p>
                            <h3 class="mb-0">{{ number_format($feeStatus['pending_amount']) }}</h3>
                            <small class="text-{{ $feeStatus['pending_amount'] > 0 ? 'warning' : 'success' }}">
                                <i class="bi bi-{{ $feeStatus['pending_amount'] > 0 ? 'exclamation-circle' : 'check-circle' }}"></i> 
                                {{ $feeStatus['pending_count'] }} pending
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-currency-rupee"></i>
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
                            <p class="text-muted small mb-1">Pending Homework</p>
                            <h3 class="mb-0">{{ count($pendingHomework) }}</h3>
                            <small class="text-info">Assignments due</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-journal-bookmark"></i>
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
                            <a href="{{ route('student.timetable.index') }}" class="btn btn-primary">
                                <i class="bi bi-calendar3 me-2"></i>View Timetable
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('student.exams.index') }}" class="btn btn-success">
                                <i class="bi bi-journal-text me-2"></i>View Results
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('student.fees.index') }}" class="btn btn-info text-white">
                                <i class="bi bi-credit-card me-2"></i>Pay Fees
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('student.attendance.index') }}" class="btn btn-warning">
                                <i class="bi bi-calendar-check me-2"></i>Attendance
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('student.homework.index') }}" class="btn btn-secondary">
                                <i class="bi bi-journal-bookmark me-2"></i>Homework
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule & Homework -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Today's Classes</h6>
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
                                    <th>Teacher</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaySchedule as $item)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $item->period_number ?? $loop->iteration }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($item->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('h:i A') }}</td>
                                        <td><strong>{{ $item->subject->name ?? 'N/A' }}</strong></td>
                                        <td>{{ $item->teacher->user->name ?? 'N/A' }}</td>
                                        <td>{{ $item->room_number ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
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
                    <h6 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Pending Homework</h6>
                    <span class="badge bg-warning">{{ count($pendingHomework) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @forelse($pendingHomework as $homework)
                            <a href="{{ route('student.homework.index') }}" class="list-group-item list-group-item-action py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $homework['title'] }}</h6>
                                        <small class="text-muted">{{ $homework['subject_name'] }}</small>
                                    </div>
                                    <span class="badge bg-{{ $homework['urgency'] }}">
                                        @if($homework['days_left'] <= 0)
                                            Due Today
                                        @elseif($homework['days_left'] == 1)
                                            Due Tomorrow
                                        @else
                                            Due in {{ $homework['days_left'] }} days
                                        @endif
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-check-circle fs-3 d-block mb-2"></i>
                                No pending homework
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Results & Upcoming Exams -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Recent Results</h6>
                    <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @forelse($recentResults as $result)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-1">{{ $result['exam_name'] }}</h6>
                                    <small class="text-muted">{{ $result['subject_name'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $result['percentage'] >= 80 ? 'success' : ($result['percentage'] >= 60 ? 'primary' : ($result['percentage'] >= 40 ? 'warning' : 'danger')) }} fs-6">{{ $result['percentage'] }}%</span>
                                    <small class="d-block text-muted">Grade {{ $result['grade'] }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                No results available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Upcoming Exams</h6>
                    <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-link">View Schedule</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @forelse($upcomingExams as $exam)
                            @php
                                $examDate = \Carbon\Carbon::parse($exam->exam_date);
                                $daysLeft = now()->diffInDays($examDate, false);
                                $urgency = $daysLeft <= 3 ? 'danger' : ($daysLeft <= 7 ? 'warning' : 'info');
                            @endphp
                            <div class="list-group-item d-flex align-items-center py-3">
                                <div class="bg-{{ $urgency }} bg-opacity-10 text-{{ $urgency }} rounded p-2 me-3">
                                    <i class="bi bi-journal-text fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $exam->subject->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ $exam->exam->name ?? 'Exam' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $urgency }}">{{ $examDate->format('M d, Y') }}</span>
                                    <small class="d-block text-muted">{{ $daysLeft }} days left</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
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
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Attendance Trend (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Subject Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    
    if (document.getElementById('attendanceChart')) {
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: chartData.attendanceMonthly?.labels || [],
                datasets: [{
                    label: 'Attendance %',
                    data: chartData.attendanceMonthly?.data || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
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
    
    if (document.getElementById('performanceChart')) {
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: chartData.subjectPerformance?.labels || [],
                datasets: [{
                    label: 'Average %',
                    data: chartData.subjectPerformance?.data || [],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)'
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
