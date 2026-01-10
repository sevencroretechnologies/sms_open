@extends('layouts.app')

@section('title', 'Student Marks')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Marks</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.students.show', $student->id) }}">{{ $student->user->name ?? 'Student' }}</a></li>
                    <li class="breadcrumb-item active">Marks</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <x-card class="mb-4">
                <div class="text-center">
                    <img src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name ?? 'S') . '&background=4f46e5&color=fff&size=100' }}" 
                         class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 class="mb-1">{{ $student->user->name ?? 'N/A' }}</h5>
                    <p class="text-muted mb-0">{{ $student->schoolClass->display_name ?? '' }} - {{ $student->section->display_name ?? '' }}</p>
                </div>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card :noPadding="true">
                <x-slot name="header">
                    <i class="bi bi-journal-text me-2"></i>Exam Results
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Marks</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($marks as $mark)
                                <tr>
                                    <td>{{ $mark->examSchedule->exam->name ?? 'N/A' }}</td>
                                    <td>{{ $mark->examSchedule->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $mark->examSchedule->exam_date ? $mark->examSchedule->exam_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($mark->is_absent)
                                            <span class="badge bg-danger">Absent</span>
                                        @else
                                            <span class="fw-medium">{{ $mark->marks_obtained ?? '-' }}</span>
                                            <span class="text-muted">/ {{ $mark->examSchedule->full_marks ?? '-' }}</span>
                                            @if($mark->examSchedule->full_marks && $mark->marks_obtained !== null)
                                                @php
                                                    $percentage = round(($mark->marks_obtained / $mark->examSchedule->full_marks) * 100, 1);
                                                @endphp
                                                <br>
                                                <small class="{{ $percentage >= 75 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                                                    ({{ $percentage }}%)
                                                </small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($mark->grade)
                                            <span class="badge bg-primary">{{ $mark->grade }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $mark->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">No exam results found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($marks->hasPages())
                    <div class="card-footer">
                        {{ $marks->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
