@extends('layouts.app')

@section('title', 'My Results')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.exams.index') }}">My Exams</a></li>
                    <li class="breadcrumb-item active">My Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('student.exams.index') }}">Exam Schedules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#results">My Results</a>
                </li>
            </ul>
        </div>
    </div>

    @forelse($marks as $examId => $examMarks)
        @php
            $firstMark = $examMarks->first();
            $exam = $firstMark->examSchedule->exam ?? null;
            $totalObtained = $examMarks->sum('obtained_marks');
            $totalFull = $examMarks->sum(fn($m) => $m->examSchedule->full_marks ?? 0);
            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
        @endphp
        <x-card class="mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="mb-1">{{ $exam->name ?? 'Unknown Exam' }}</h5>
                    @if($exam && $exam->examType)
                        <span class="badge bg-primary">{{ $exam->examType->name }}</span>
                    @endif
                </div>
                <div class="text-end">
                    <h4 class="mb-0 {{ $percentage >= 40 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</h4>
                    <small class="text-muted">{{ $totalObtained }} / {{ $totalFull }}</small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Full Marks</th>
                            <th>Obtained</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examMarks as $mark)
                            @php
                                $schedule = $mark->examSchedule;
                                $subjectPercentage = $schedule && $schedule->full_marks > 0 
                                    ? round(($mark->obtained_marks / $schedule->full_marks) * 100, 1) 
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->full_marks ?? '-' }}</td>
                                <td>
                                    <strong>{{ $mark->obtained_marks }}</strong>
                                    <small class="text-muted">({{ $subjectPercentage }}%)</small>
                                </td>
                                <td>
                                    @if($mark->grade)
                                        <span class="badge bg-info">{{ $mark->grade }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($schedule && $mark->obtained_marks >= $schedule->passing_marks)
                                        <span class="badge bg-success">Pass</span>
                                    @else
                                        <span class="badge bg-danger">Fail</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @empty
        <x-card>
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No exam results available yet.</p>
            </div>
        </x-card>
    @endforelse
</div>
@endsection
