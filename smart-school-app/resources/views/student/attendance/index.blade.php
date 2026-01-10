@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-primary">{{ $summary['total'] }}</h3>
                    <small class="text-muted">Total Days</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-success">{{ $summary['present'] }}</h3>
                    <small class="text-muted">Present</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-danger">{{ $summary['absent'] }}</h3>
                    <small class="text-muted">Absent</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 {{ $summary['percentage'] >= 75 ? 'text-success' : 'text-warning' }}">{{ $summary['percentage'] }}%</h3>
                    <small class="text-muted">Attendance Rate</small>
                </div>
            </x-card>
        </div>
    </div>

    <x-card title="Attendance Calendar">
        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <label for="month" class="form-label">Select Month</label>
                <input type="month" class="form-control" id="month" name="month" value="{{ $month }}" onchange="this.form.submit()">
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
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
                    @php
                        $currentDate = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $endOfMonth = $endDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    @endphp
                    
                    @while($currentDate <= $endOfMonth)
                        <tr>
                            @for($i = 0; $i < 7; $i++)
                                @php
                                    $dateKey = $currentDate->format('Y-m-d');
                                    $attendance = $calendarData[$dateKey] ?? null;
                                    $isCurrentMonth = $currentDate->month == $startDate->month;
                                @endphp
                                <td class="text-center p-2 {{ !$isCurrentMonth ? 'text-muted bg-light' : '' }}" style="height: 60px; vertical-align: middle;">
                                    <div class="small">{{ $currentDate->day }}</div>
                                    @if($attendance && $isCurrentMonth)
                                        @if($attendance->attendanceType && $attendance->attendanceType->is_present)
                                            <span class="badge bg-success">P</span>
                                        @else
                                            <span class="badge bg-danger">A</span>
                                        @endif
                                    @endif
                                </td>
                                @php $currentDate->addDay(); @endphp
                            @endfor
                        </tr>
                    @endwhile
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <span class="badge bg-success me-2">P = Present</span>
            <span class="badge bg-danger me-2">A = Absent</span>
        </div>
    </x-card>

    <x-card title="Attendance History" class="mt-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>{{ $attendance->attendance_date->format('l') }}</td>
                            <td>
                                @if($attendance->attendanceType)
                                    <span class="badge bg-{{ $attendance->attendanceType->is_present ? 'success' : 'danger' }}">
                                        {{ $attendance->attendanceType->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $attendance->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <p class="text-muted mb-0">No attendance records found for this month.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
