@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Exams</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#schedules">Exam Schedules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('student.exams.results') }}">My Results</a>
                </li>
            </ul>
        </div>
    </div>

    @if($currentSession)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Showing exams for academic session: <strong>{{ $currentSession->name }}</strong>
        </div>
    @endif

    <div class="row">
        @forelse($exams as $exam)
            <div class="col-md-6 col-lg-4 mb-4">
                <x-card>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $exam->name }}</h5>
                            <span class="badge bg-primary">{{ $exam->examType->name ?? 'N/A' }}</span>
                        </div>
                        @if($exam->start_date && $exam->start_date->isFuture())
                            <span class="badge bg-warning">Upcoming</span>
                        @elseif($exam->end_date && $exam->end_date->isPast())
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-info">Ongoing</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $exam->start_date ? $exam->start_date->format('d M Y') : 'N/A' }} - {{ $exam->end_date ? $exam->end_date->format('d M Y') : 'N/A' }}
                        </small>
                    </div>

                    @if(isset($schedules[$exam->id]))
                        <div class="mb-3">
                            <small class="text-muted">{{ $schedules[$exam->id]->count() }} subjects scheduled</small>
                        </div>
                    @endif

                    <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        View Schedule & Results
                    </a>
                </x-card>
            </div>
        @empty
            <div class="col-12">
                <x-card>
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No exams scheduled for this session.</p>
                    </div>
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
