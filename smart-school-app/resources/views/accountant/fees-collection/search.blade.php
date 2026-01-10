@extends('layouts.app')

@section('title', 'Search Student')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-collection.index') }}">Fee Collection</a></li>
            <li class="breadcrumb-item active">Search Student</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Search Student for Fee Collection</h1>

    <x-card title="Search">
        <form action="{{ route('accountant.fees-collection.search') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-lg" 
                       placeholder="Enter student name or admission number" value="{{ request('search') }}" autofocus>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </form>
    </x-card>

    @if(isset($students))
        <x-card title="Search Results" class="mt-4">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Admission No</th>
                                <th>Class/Section</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($student->user->profile_photo)
                                                <img src="{{ asset('storage/' . $student->user->profile_photo) }}" alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    {{ substr($student->user->name ?? 'S', 0, 1) }}
                                                </div>
                                            @endif
                                            <span>{{ $student->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $student->admission_no }}</td>
                                    <td>{{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('accountant.fees-collection.student-fees', $student->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i> View Fees
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-empty-state 
                    icon="bi-search"
                    title="No Students Found"
                    description="No students match your search criteria."
                />
            @endif
        </x-card>
    @endif
</div>
@endsection
