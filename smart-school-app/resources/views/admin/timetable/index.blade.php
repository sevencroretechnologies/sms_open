{{-- Timetable List View --}}
{{-- Admin timetable management page --}}

@extends('layouts.app')

@section('title', 'Timetable')

@section('content')
<div x-data="{ classId: '', sectionId: '' }">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Timetable Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Timetable</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Timetable
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <!-- Filters -->
    <x-card class="mb-4">
        <x-slot name="header">
            <i class="bi bi-funnel me-2"></i>
            Select Class & Section
        </x-slot>
        
        <form action="{{ route('admin.timetable.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select" x-model="classId">
                        <option value="">Select Class</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select" x-model="sectionId">
                        <option value="">Select Section</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> View Timetable
                    </button>
                    <a href="{{ route('admin.timetable.print', ['class_id' => request('class_id'), 'section_id' => request('section_id')]) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="bi bi-printer me-1"></i> Print
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Timetable Grid -->
    @if(request('class_id'))
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-calendar-week me-2"></i>
            Weekly Timetable
        </x-slot>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
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
                    @forelse($periods ?? [] as $period)
                        <tr>
                            <td class="text-center bg-light">
                                <small class="fw-medium">{{ $period->start_time }} - {{ $period->end_time }}</small>
                            </td>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <td class="text-center">
                                    @php
                                        $slot = $timetable[$period->id][$day] ?? null;
                                    @endphp
                                    @if($slot)
                                        <div class="p-2 rounded bg-primary bg-opacity-10">
                                            <div class="fw-medium text-primary">{{ $slot->subject->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $slot->teacher->name ?? 'N/A' }}</small>
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
                                    <p class="mb-2">No timetable found for this class</p>
                                    <a href="{{ route('admin.timetable.create', ['class_id' => request('class_id')]) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Create Timetable
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
    @else
    <x-card>
        <div class="text-center py-5">
            <i class="bi bi-calendar-week fs-1 text-muted d-block mb-3"></i>
            <h5 class="text-muted">Select a Class to View Timetable</h5>
            <p class="text-muted mb-0">Choose a class and section from the filters above to view or manage the timetable.</p>
        </div>
    </x-card>
    @endif
</div>
@endsection
