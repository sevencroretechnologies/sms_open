{{-- Timetable Show View --}}
{{-- Admin timetable details --}}

@extends('layouts.app')

@section('title', 'View Timetable')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">View Timetable</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.timetable.print', ['class_id' => $class->id ?? 1, 'section_id' => $section->id ?? null]) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i> Print
            </a>
            <a href="{{ route('admin.timetable.edit', $timetable ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.timetable.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Class Info -->
    <x-card class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px;">
                        <i class="bi bi-mortarboard fs-3"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $class->name ?? 'Class Name' }} {{ $section ? '- Section ' . $section->name : '' }}</h5>
                        <span class="text-muted">{{ $academicSession->name ?? 'Current Session' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-primary">{{ count($periods ?? []) }} Periods</span>
                <span class="badge bg-info">6 Days</span>
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
                        <th style="width: 120px;">Time</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods ?? [] as $period)
                        <tr>
                            <td class="text-center bg-light">
                                <div class="fw-medium">Period {{ $loop->iteration }}</div>
                                <small class="text-muted">{{ $period->start_time }} - {{ $period->end_time }}</small>
                            </td>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <td class="text-center p-2">
                                    @php
                                        $slot = $timetable[$period->id][$day] ?? null;
                                    @endphp
                                    @if($slot)
                                        <div class="p-2 rounded bg-primary bg-opacity-10">
                                            <div class="fw-medium text-primary">{{ $slot->subject->name ?? 'N/A' }}</div>
                                            <small class="text-muted d-block">{{ $slot->teacher->user->name ?? $slot->teacher->name ?? 'N/A' }}</small>
                                            @if($slot->room)
                                                <small class="badge bg-light text-dark">{{ $slot->room }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-week fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No schedule defined</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
