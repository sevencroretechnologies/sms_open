@extends('layouts.app')

@section('title', 'My Timetable')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Timetable</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-1">Class Timetable</h5>
                <p class="text-muted mb-0">
                    {{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}
                </p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Day</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dayNames as $day)
                        <tr>
                            <td class="fw-bold text-capitalize bg-light">{{ $day }}</td>
                            <td>
                                @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($timetable[$day] as $slot)
                                            <div class="border rounded p-2" style="min-width: 150px;">
                                                <div class="fw-bold text-primary">{{ $slot->subject->name ?? 'N/A' }}</div>
                                                <small class="text-muted d-block">
                                                    {{ $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('h:i A') : '' }}
                                                    -
                                                    {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('h:i A') : '' }}
                                                </small>
                                                @if($slot->teacher && $slot->teacher->user)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-user me-1"></i>{{ $slot->teacher->user->name }}
                                                    </small>
                                                @endif
                                                @if($slot->room_number)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-door-open me-1"></i>Room {{ $slot->room_number }}
                                                    </small>
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

@push('styles')
<style>
    @media print {
        .breadcrumb, .btn, nav, .sidebar, header, footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection
