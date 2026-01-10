@extends('layouts.app')

@section('title', 'View Timetable')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">View Timetable</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.timetables.index') }}">Timetable</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.timetables.print', ['class' => 1, 'section' => 1]) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i> Print
            </a>
            <a href="{{ route('admin.timetables.edit', $timetable->id ?? 0) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar-week me-2"></i>Weekly Timetable
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0">
                    <thead class="table-light">
                        <tr>
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
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No timetable data available.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
