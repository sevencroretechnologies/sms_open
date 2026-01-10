@extends('layouts.app')

@section('title', 'Notices')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notices</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notices</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card class="mb-4">
        <form method="GET" action="{{ route('teacher.notices.index') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search notices..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    <a href="{{ route('teacher.notices.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <div class="row">
        @forelse($notices as $notice)
            <div class="col-md-6 col-lg-4 mb-4">
                <x-card class="h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
                            {{ ucfirst($notice->priority ?? 'normal') }}
                        </span>
                        <small class="text-muted">{{ $notice->publish_date ? $notice->publish_date->format('d M Y') : '' }}</small>
                    </div>
                    <h5 class="card-title">{{ $notice->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit(strip_tags($notice->content), 120) }}</p>
                    <a href="{{ route('teacher.notices.show', $notice->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> Read More
                    </a>
                </x-card>
            </div>
        @empty
            <div class="col-12">
                <x-card class="text-center py-5">
                    <i class="bi bi-megaphone fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No Notices Found</h5>
                    <p class="text-muted mb-0">There are no notices available at the moment.</p>
                </x-card>
            </div>
        @endforelse
    </div>

    @if($notices->hasPages())
        <div class="d-flex justify-content-center">
            {{ $notices->links() }}
        </div>
    @endif
</div>
@endsection
