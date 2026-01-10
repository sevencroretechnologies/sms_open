@extends('layouts.app')

@section('title', isset($timetable) ? 'Edit Timetable' : 'Create Timetable')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($timetable) ? 'Edit Timetable' : 'Create Timetable' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.timetables.index') }}">Timetable</a></li>
                    <li class="breadcrumb-item active">{{ isset($timetable) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($timetable) ? route('admin.timetables.update', $timetable->id) : route('admin.timetables.store') }}">
        @csrf
        @if(isset($timetable))
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Timetable Settings
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                        <select name="academic_session_id" class="form-select" required>
                            <option value="">Select Session</option>
                            @foreach($academicSessions ?? [] as $session)
                                <option value="{{ $session->id }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes ?? [] as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" class="form-select" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Effective From</label>
                        <input type="date" name="effective_from" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-calendar-week me-2"></i>Weekly Schedule
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select class and section above to load available subjects and teachers.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">Period</th>
                                <th style="width: 100px;">Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 8; $i++)
                                <tr>
                                    <td class="fw-medium">Period {{ $i }}</td>
                                    <td>
                                        <input type="time" name="periods[{{ $i }}][start]" class="form-control form-control-sm">
                                        <input type="time" name="periods[{{ $i }}][end]" class="form-control form-control-sm mt-1">
                                    </td>
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                        <td>
                                            <select name="schedule[{{ $day }}][{{ $i }}][subject_id]" class="form-select form-select-sm mb-1">
                                                <option value="">Subject</option>
                                            </select>
                                            <select name="schedule[{{ $day }}][{{ $i }}][teacher_id]" class="form-select form-select-sm">
                                                <option value="">Teacher</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($timetable) ? 'Update' : 'Save' }} Timetable
            </button>
        </div>
    </form>
</div>
@endsection
