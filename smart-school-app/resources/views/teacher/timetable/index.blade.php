@extends('layouts.app')

@section('title', 'My Timetable')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Timetable</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Timetable</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.timetable.print') }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-printer me-1"></i> Print
            </a>
        </div>
    </div>

    @if($classTeacherSections->count() > 0)
        <x-card class="mb-4">
            <x-slot name="header">
                <i class="bi bi-person-badge me-2"></i>Class Teacher Assignments
            </x-slot>
            <div class="row g-3">
                @foreach($classTeacherSections as $section)
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h6 class="mb-1">{{ $section->schoolClass->display_name ?? 'N/A' }} - {{ $section->display_name }}</h6>
                            <small class="text-muted">{{ $section->students->count() }} Students</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-calendar-week me-2"></i>Weekly Schedule
        </x-slot>

        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Day</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dayNames as $day)
                        <tr>
                            <td class="fw-medium text-capitalize">{{ $day }}</td>
                            <td>
                                @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($timetable[$day] as $slot)
                                            <div class="border rounded p-2 bg-light" style="min-width: 180px;">
                                                <div class="fw-medium text-primary">
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                </div>
                                                <div class="small">
                                                    <strong>{{ $slot->subject->name ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="small text-muted">
                                                    {{ $slot->schoolClass->display_name ?? '' }} - {{ $slot->section->display_name ?? '' }}
                                                </div>
                                                @if($slot->room_number)
                                                    <div class="small text-muted">
                                                        <i class="bi bi-geo-alt"></i> Room {{ $slot->room_number }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No classes scheduled</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
