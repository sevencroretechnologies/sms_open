@extends('layouts.app')

@section('title', 'Exam Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $exam->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">{{ $exam->name }}</li>
                </ol>
            </nav>
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

    <div class="row mb-4">
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <i class="bi bi-journal-bookmark fs-1 text-primary mb-2 d-block"></i>
                    <h5 class="mb-1">{{ $exam->examType->name ?? 'N/A' }}</h5>
                    <small class="text-muted">Exam Type</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <i class="bi bi-calendar-event fs-1 text-success mb-2 d-block"></i>
                    <h5 class="mb-1">{{ $exam->start_date ? $exam->start_date->format('d M Y') : '-' }}</h5>
                    <small class="text-muted">Start Date</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <i class="bi bi-calendar-check fs-1 text-info mb-2 d-block"></i>
                    <h5 class="mb-1">{{ $exam->end_date ? $exam->end_date->format('d M Y') : '-' }}</h5>
                    <small class="text-muted">End Date</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card>
                <div class="text-center">
                    <i class="bi bi-list-check fs-1 text-warning mb-2 d-block"></i>
                    <h5 class="mb-1">{{ $exam->examSchedules->count() }}</h5>
                    <small class="text-muted">Total Schedules</small>
                </div>
            </x-card>
        </div>
    </div>

    @if($exam->description)
        <x-card class="mb-4">
            <x-slot name="header">
                <i class="bi bi-info-circle me-2"></i>Description
            </x-slot>
            <p class="mb-0">{{ $exam->description }}</p>
        </x-card>
    @endif

    @foreach($schedules as $classId => $classSchedules)
        @php
            $firstSchedule = $classSchedules->first();
            $className = $firstSchedule->schoolClass->display_name ?? 'Unknown Class';
        @endphp
        <x-card class="mb-4" :noPadding="true">
            <x-slot name="header">
                <i class="bi bi-calendar3 me-2"></i>{{ $className }} Schedules
            </x-slot>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Marks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classSchedules as $schedule)
                            <tr>
                                <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->section->display_name ?? 'N/A' }}</td>
                                <td>{{ $schedule->exam_date ? $schedule->exam_date->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($schedule->start_time && $schedule->end_time)
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $schedule->room_number ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $schedule->full_marks ?? '-' }}</span>
                                    <small class="text-muted">/ Pass: {{ $schedule->passing_marks ?? '-' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('teacher.exams.marks', $schedule->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Enter Marks
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endforeach

    @if($schedules->isEmpty())
        <x-card class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-muted">No Schedules Found</h5>
            <p class="text-muted mb-0">There are no exam schedules for your classes in this exam.</p>
        </x-card>
    @endif
</div>
@endsection
