@extends('layouts.app')

@section('title', $exam->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.exams.index') }}">My Exams</a></li>
                    <li class="breadcrumb-item active">{{ $exam->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card>
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="mb-1">{{ $exam->name }}</h4>
                <span class="badge bg-primary">{{ $exam->examType->name ?? 'N/A' }}</span>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">
                    {{ $exam->start_date ? $exam->start_date->format('d M Y') : 'N/A' }} - {{ $exam->end_date ? $exam->end_date->format('d M Y') : 'N/A' }}
                </small>
            </div>
        </div>

        @if($exam->description)
            <p class="text-muted mb-4">{{ $exam->description }}</p>
        @endif
    </x-card>

    <x-card title="Exam Schedule & Results" class="mt-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Full Marks</th>
                        <th>Pass Marks</th>
                        <th>Obtained</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $mark = $marks[$schedule->id] ?? null;
                            $percentage = $mark && $schedule->full_marks > 0 
                                ? round(($mark->obtained_marks / $schedule->full_marks) * 100, 1) 
                                : null;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $schedule->subject->name ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $schedule->exam_date ? $schedule->exam_date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'N/A' }}
                                -
                                {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : 'N/A' }}
                            </td>
                            <td>{{ $schedule->room_number ?? '-' }}</td>
                            <td>{{ $schedule->full_marks ?? '-' }}</td>
                            <td>{{ $schedule->passing_marks ?? '-' }}</td>
                            <td>
                                @if($mark)
                                    <strong class="{{ $mark->obtained_marks >= $schedule->passing_marks ? 'text-success' : 'text-danger' }}">
                                        {{ $mark->obtained_marks }}
                                    </strong>
                                    @if($percentage)
                                        <small class="text-muted">({{ $percentage }}%)</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($mark && $mark->grade)
                                    <span class="badge bg-info">{{ $mark->grade }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($mark)
                                    @if($mark->obtained_marks >= $schedule->passing_marks)
                                        <span class="badge bg-success">Pass</span>
                                    @else
                                        <span class="badge bg-danger">Fail</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted mb-0">No exam schedule found for your class.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->count() > 0 && $marks->count() > 0)
            @php
                $totalObtained = $marks->sum('obtained_marks');
                $totalFull = $schedules->sum('full_marks');
                $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
            @endphp
            <div class="mt-4 p-3 bg-light rounded">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h5 class="mb-0">{{ $totalObtained }} / {{ $totalFull }}</h5>
                        <small class="text-muted">Total Marks</small>
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-0 {{ $overallPercentage >= 40 ? 'text-success' : 'text-danger' }}">{{ $overallPercentage }}%</h5>
                        <small class="text-muted">Overall Percentage</small>
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-0">{{ $marks->count() }} / {{ $schedules->count() }}</h5>
                        <small class="text-muted">Results Declared</small>
                    </div>
                </div>
            </div>
        @endif
    </x-card>

    <div class="mt-4">
        <a href="{{ route('student.exams.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Exams
        </a>
    </div>
</div>
@endsection
