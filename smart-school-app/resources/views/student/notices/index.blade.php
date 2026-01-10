@extends('layouts.app')

@section('title', 'Notices')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notices</li>
                </ol>
            </nav>
        </div>
    </div>

    <x-card>
        <form method="GET" class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search notices..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="row">
            @forelse($notices as $notice)
                <div class="col-12 mb-3">
                    <div class="card border-start border-4 {{ $notice->priority == 'high' ? 'border-danger' : ($notice->priority == 'medium' ? 'border-warning' : 'border-info') }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('student.notices.show', $notice->id) }}" class="text-decoration-none">
                                            {{ $notice->title }}
                                        </a>
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $notice->publish_date ? $notice->publish_date->format('d M Y') : 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($notice->priority ?? 'normal') }}
                                </span>
                            </div>
                            <p class="card-text mt-2 text-muted">
                                {{ Str::limit(strip_tags($notice->content), 200) }}
                            </p>
                            <a href="{{ route('student.notices.show', $notice->id) }}" class="btn btn-outline-primary btn-sm">
                                Read More <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No notices available.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($notices->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notices->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
