@extends('layouts.app')

@section('title', 'My Children')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Children</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row">
        @forelse($children as $child)
            <div class="col-md-6 col-lg-4 mb-4">
                <x-card>
                    <div class="text-center mb-3">
                        @if($child->photo)
                            <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->user->name ?? 'Student' }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <span class="text-white fs-2">{{ substr($child->user->name ?? 'S', 0, 1) }}</span>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $child->user->name ?? 'N/A' }}</h5>
                        <p class="text-muted mb-2">{{ $child->admission_no ?? 'N/A' }}</p>
                        <span class="badge bg-primary">{{ $child->schoolClass->name ?? 'N/A' }} - {{ $child->section->name ?? 'N/A' }}</span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a>
                        <a href="{{ route('parent.children.attendance', $child->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-calendar-check me-2"></i>Attendance
                        </a>
                        <a href="{{ route('parent.children.exams', $child->id) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-chart-bar me-2"></i>Exam Results
                        </a>
                        <a href="{{ route('parent.children.fees', $child->id) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-money-bill me-2"></i>Fees
                        </a>
                    </div>
                </x-card>
            </div>
        @empty
            <div class="col-12">
                <x-card>
                    <div class="text-center py-5">
                        <i class="fas fa-child fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No children linked to your account.</p>
                    </div>
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
