@extends('layouts.app')

@section('title', 'Exam Results - ' . ($child->user->name ?? 'Child'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.index') }}">My Children</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.show', $child->id) }}">{{ $child->user->name ?? 'Child' }}</a></li>
                    <li class="breadcrumb-item active">Exam Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">{{ $child->user->name ?? 'Child' }}'s Exam Results</h5>
            <span class="badge bg-primary">{{ $child->schoolClass->name ?? '' }} - {{ $child->section->name ?? '' }}</span>
        </div>

        @forelse($marks as $examId => $examMarks)
            @php
                $firstMark = $examMarks->first();
                $exam = $firstMark->examSchedule->exam ?? null;
                $totalObtained = $examMarks->sum('obtained_marks');
                $totalFull = $examMarks->sum(fn($m) => $m->examSchedule->full_marks ?? 0);
                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ $exam->name ?? 'Unknown Exam' }}</h6>
                        @if($exam && $exam->examType)
                            <small class="text-muted">{{ $exam->examType->name }}</small>
                        @endif
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0 {{ $percentage >= 40 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</h5>
                        <small class="text-muted">{{ $totalObtained }} / {{ $totalFull }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Full Marks</th>
                                    <th>Pass Marks</th>
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
                                        <td>{{ $schedule->passing_marks ?? '-' }}</td>
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
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No exam results available yet.</p>
            </div>
        @endforelse
    </x-card>

    <div class="mt-4">
        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>
@endsection
