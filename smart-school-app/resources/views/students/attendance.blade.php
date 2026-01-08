{{-- Student Attendance View --}}
{{-- Prompt 147: Student attendance view with calendar and statistics --}}

@extends('layouts.app')

@section('title', 'Student Attendance - ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))

@section('content')
<div x-data="studentAttendance()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Attendance</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id ?? 0) }}">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
            <button type="button" class="btn btn-outline-primary" @click="exportAttendance()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            @can('mark_attendance')
            <button type="button" class="btn btn-primary" @click="showMarkModal = true">
                <i class="bi bi-check-circle me-1"></i> Mark Attendance
            </button>
            @endcan
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img 
                    src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) . '&background=4f46e5&color=fff&size=60' }}"
                    alt="{{ $student->first_name ?? '' }}"
                    class="rounded-circle me-3"
                    style="width: 60px; height: 60px; object-fit: cover;"
                >
                <div>
                    <h5 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-light text-dark me-2">{{ $student->admission_number ?? 'N/A' }}</span>
                        <span class="badge bg-primary">{{ $student->class->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-percent text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $attendancePercentage ?? 0 }}%</h3>
                            <small class="text-muted">Overall Attendance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $presentDays ?? 0 }}</h3>
                            <small class="text-muted">Present Days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $absentDays ?? 0 }}</h3>
                            <small class="text-muted">Absent Days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $lateDays ?? 0 }}</h3>
                            <small class="text-muted">Late Days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Calendar View -->
        <div class="col-lg-8">
            <x-card title="Attendance Calendar" icon="bi-calendar3">
                <x-slot name="actions">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="previousMonth()">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="fw-medium" x-text="currentMonthYear"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="nextMonth()">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </x-slot>

                <!-- Calendar Grid -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
                                <tr>
                                    <template x-for="(day, dayIndex) in week" :key="dayIndex">
                                        <td 
                                            class="p-2" 
                                            :class="{
                                                'bg-light': !day.isCurrentMonth,
                                                'bg-success bg-opacity-25': day.status === 'present',
                                                'bg-danger bg-opacity-25': day.status === 'absent',
                                                'bg-warning bg-opacity-25': day.status === 'late',
                                                'bg-info bg-opacity-25': day.status === 'leave',
                                                'bg-secondary bg-opacity-25': day.status === 'holiday'
                                            }"
                                            style="min-width: 40px; height: 50px;"
                                        >
                                            <span 
                                                class="d-block" 
                                                :class="{ 'text-muted': !day.isCurrentMonth }"
                                                x-text="day.date"
                                            ></span>
                                            <small x-show="day.status" class="d-block">
                                                <i class="bi" :class="{
                                                    'bi-check-circle-fill text-success': day.status === 'present',
                                                    'bi-x-circle-fill text-danger': day.status === 'absent',
                                                    'bi-clock-fill text-warning': day.status === 'late',
                                                    'bi-calendar-x-fill text-info': day.status === 'leave',
                                                    'bi-calendar-fill text-secondary': day.status === 'holiday'
                                                }"></i>
                                            </small>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-1">&nbsp;</span>
                        <small>Present</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-danger me-1">&nbsp;</span>
                        <small>Absent</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-1">&nbsp;</span>
                        <small>Late</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-info me-1">&nbsp;</span>
                        <small>Leave</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-1">&nbsp;</span>
                        <small>Holiday</small>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Charts -->
        <div class="col-lg-4">
            <x-card title="Attendance Distribution" icon="bi-pie-chart">
                <div class="text-center">
                    <canvas id="attendancePieChart" height="250"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="col-12">
            <x-card title="Monthly Attendance Trend" icon="bi-graph-up">
                <canvas id="attendanceTrendChart" height="100"></canvas>
            </x-card>
        </div>

        <!-- Attendance Records Table -->
        <div class="col-12">
            <x-card title="Attendance Records" icon="bi-list-ul">
                <x-slot name="actions">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" style="width: auto;" x-model="filterMonth">
                            <option value="">All Months</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <select class="form-select form-select-sm" style="width: auto;" x-model="filterStatus">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Marked By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="record in filteredRecords" :key="record.id">
                                <tr>
                                    <td x-text="formatDate(record.date)"></td>
                                    <td x-text="getDayName(record.date)"></td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': record.status === 'present',
                                            'bg-danger': record.status === 'absent',
                                            'bg-warning': record.status === 'late',
                                            'bg-info': record.status === 'leave'
                                        }" x-text="record.status.charAt(0).toUpperCase() + record.status.slice(1)"></span>
                                    </td>
                                    <td x-text="record.remarks || '-'"></td>
                                    <td x-text="record.marked_by || '-'"></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" @click="editRecord(record)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredRecords.length === 0">
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    No attendance records found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div class="modal fade" :class="{ 'show d-block': showMarkModal }" tabindex="-1" x-show="showMarkModal" x-transition>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Mark Attendance</h5>
                    <button type="button" class="btn-close" @click="showMarkModal = false"></button>
                </div>
                <form @submit.prevent="saveAttendance">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" x-model="markForm.date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" x-model="markForm.status" required>
                                    <option value="">Select status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="leave">Leave</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" rows="2" x-model="markForm.remarks" placeholder="Add any remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showMarkModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span x-show="!saving"><i class="bi bi-check me-1"></i> Save</span>
                            <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showMarkModal" x-transition></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function studentAttendance() {
    return {
        currentDate: new Date(),
        attendanceData: @json($attendanceRecords ?? []),
        
        filterMonth: '',
        filterStatus: '',
        
        showMarkModal: false,
        markForm: {
            date: new Date().toISOString().split('T')[0],
            status: '',
            remarks: ''
        },
        saving: false,
        
        get currentMonthYear() {
            return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },
        
        get calendarWeeks() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            const weeks = [];
            let currentWeek = [];
            
            // Fill in days from previous month
            for (let i = 0; i < firstDay.getDay(); i++) {
                const prevDate = new Date(year, month, -firstDay.getDay() + i + 1);
                currentWeek.push({
                    date: prevDate.getDate(),
                    isCurrentMonth: false,
                    status: null
                });
            }
            
            // Fill in days of current month
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const record = this.attendanceData.find(r => r.date === dateStr);
                
                currentWeek.push({
                    date: day,
                    isCurrentMonth: true,
                    status: record ? record.status : null
                });
                
                if (currentWeek.length === 7) {
                    weeks.push(currentWeek);
                    currentWeek = [];
                }
            }
            
            // Fill in days from next month
            if (currentWeek.length > 0) {
                let nextDay = 1;
                while (currentWeek.length < 7) {
                    currentWeek.push({
                        date: nextDay++,
                        isCurrentMonth: false,
                        status: null
                    });
                }
                weeks.push(currentWeek);
            }
            
            return weeks;
        },
        
        get filteredRecords() {
            let records = [...this.attendanceData];
            
            if (this.filterMonth) {
                records = records.filter(r => {
                    const month = new Date(r.date).getMonth() + 1;
                    return month === parseInt(this.filterMonth);
                });
            }
            
            if (this.filterStatus) {
                records = records.filter(r => r.status === this.filterStatus);
            }
            
            return records.sort((a, b) => new Date(b.date) - new Date(a.date));
        },
        
        previousMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
        },
        
        nextMonth() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
        },
        
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        
        getDayName(date) {
            return new Date(date).toLocaleDateString('en-US', { weekday: 'long' });
        },
        
        editRecord(record) {
            this.markForm = {
                id: record.id,
                date: record.date,
                status: record.status,
                remarks: record.remarks || ''
            };
            this.showMarkModal = true;
        },
        
        async saveAttendance() {
            if (!this.markForm.date || !this.markForm.status) return;
            
            this.saving = true;
            
            try {
                const url = this.markForm.id 
                    ? `/students/{{ $student->id ?? 0 }}/attendance/${this.markForm.id}`
                    : '{{ route("students.attendance.store", $student->id ?? 0) }}';
                    
                const response = await fetch(url, {
                    method: this.markForm.id ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.markForm)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    if (this.markForm.id) {
                        const index = this.attendanceData.findIndex(r => r.id === this.markForm.id);
                        if (index !== -1) {
                            this.attendanceData[index] = result.record;
                        }
                    } else {
                        this.attendanceData.push(result.record);
                    }
                    
                    this.showMarkModal = false;
                    this.markForm = { date: new Date().toISOString().split('T')[0], status: '', remarks: '' };
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Attendance has been saved.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error('Failed to save attendance');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save attendance. Please try again.' });
            } finally {
                this.saving = false;
            }
        },
        
        exportAttendance() {
            window.location.href = '{{ route("students.attendance.export", $student->id ?? 0) }}?month=' + this.filterMonth + '&status=' + this.filterStatus;
        },
        
        initCharts() {
            // Pie Chart
            const pieCtx = document.getElementById('attendancePieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Absent', 'Late', 'Leave'],
                        datasets: [{
                            data: [{{ $presentDays ?? 0 }}, {{ $absentDays ?? 0 }}, {{ $lateDays ?? 0 }}, {{ $leaveDays ?? 0 }}],
                            backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
            
            // Trend Chart
            const trendCtx = document.getElementById('attendanceTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                        datasets: [{
                            label: 'Attendance %',
                            data: @json($monthlyTrend ?? [92, 88, 95, 90, 87, 93, 91, 89, 94, 92, 90, 88]),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0,
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
        },
        
        init() {
            this.$nextTick(() => {
                this.initCharts();
            });
        }
    };
}
</script>
@endpush
@endsection
