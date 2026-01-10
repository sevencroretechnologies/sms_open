@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Attendance</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-pencil me-2"></i>Edit Attendance Record
                </x-slot>

                <form method="POST" action="{{ route('teacher.attendance.update', $attendance->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student</label>
                            <p class="fw-medium mb-0">{{ $attendance->student->user->name ?? 'N/A' }}</p>
                            <small class="text-muted">{{ $attendance->student->admission_number ?? '' }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date</label>
                            <p class="fw-medium mb-0">{{ $attendance->attendance_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Class</label>
                            <p class="fw-medium mb-0">{{ $attendance->schoolClass->display_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Section</label>
                            <p class="fw-medium mb-0">{{ $attendance->section->display_name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attendance Status <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($attendanceTypes as $type)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="attendance_type_id" 
                                           id="type_{{ $type->id }}" value="{{ $type->id }}"
                                           {{ $attendance->attendance_type_id == $type->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_{{ $type->id }}">
                                        <span class="badge" style="background-color: {{ $type->color }}">{{ $type->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('attendance_type_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks...">{{ old('remarks', $attendance->remarks) }}</textarea>
                        @error('remarks')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Attendance
                        </button>
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
