{{-- Timetable Create View --}}
{{-- Admin timetable creation form --}}

@extends('layouts.app')

@section('title', 'Create Timetable')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Create Timetable</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.timetable.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.timetable.store') }}" method="POST">
        @csrf
        
        <!-- Selection -->
        <x-card class="mb-4">
            <x-slot name="header">
                <i class="bi bi-filter me-2"></i>
                Select Class & Section
            </x-slot>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">Select Class</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', request('class_id')) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select @error('section_id') is-invalid @enderror">
                        <option value="">All Sections</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', request('section_id')) == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                    @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Session</label>
                    <select name="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror">
                        <option value="">Current Session</option>
                        @foreach($academicSessions ?? [] as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-card>

        <!-- Timetable Grid -->
        <x-card :noPadding="true">
            <x-slot name="header">
                <i class="bi bi-calendar-week me-2"></i>
                Weekly Schedule
            </x-slot>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">Period</th>
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
                                <td class="bg-light">
                                    <div class="mb-2">
                                        <small class="fw-medium">Period {{ $i }}</small>
                                    </div>
                                    <div class="row g-1">
                                        <div class="col-6">
                                            <input type="time" name="periods[{{ $i }}][start_time]" class="form-control form-control-sm" value="{{ old("periods.{$i}.start_time") }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" name="periods[{{ $i }}][end_time]" class="form-control form-control-sm" value="{{ old("periods.{$i}.end_time") }}">
                                        </div>
                                    </div>
                                </td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                    <td>
                                        <select name="schedule[{{ $i }}][{{ $day }}][subject_id]" class="form-select form-select-sm mb-1">
                                            <option value="">Subject</option>
                                            @foreach($subjects ?? [] as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="schedule[{{ $i }}][{{ $day }}][teacher_id]" class="form-select form-select-sm">
                                            <option value="">Teacher</option>
                                            @foreach($teachers ?? [] as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.timetable.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Timetable
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
@endsection
