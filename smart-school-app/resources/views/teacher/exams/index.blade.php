@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Exams</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Exams</li>
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

    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-journal-bookmark me-2"></i>Exam List
            @if($currentSession)
                <span class="badge bg-primary ms-2">{{ $currentSession->name }}</span>
            @endif
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Schedules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $exam->name }}</span>
                                @if($exam->description)
                                    <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $exam->examType->name ?? 'N/A' }}</td>
                            <td>{{ $exam->start_date ? $exam->start_date->format('d M Y') : '-' }}</td>
                            <td>{{ $exam->end_date ? $exam->end_date->format('d M Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $exam->examSchedules->count() }} Schedules</span>
                            </td>
                            <td>
                                <a href="{{ route('teacher.exams.show', $exam->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">No exams found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
            <div class="card-footer">
                {{ $exams->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
