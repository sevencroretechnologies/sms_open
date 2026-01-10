@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.notices.index') }}">Notices</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($notice->title, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="mb-2">{{ $notice->title }}</h4>
                        <div class="d-flex gap-3 text-muted small">
                            <span>
                                <i class="fas fa-calendar me-1"></i>
                                {{ $notice->publish_date ? $notice->publish_date->format('d M Y') : 'N/A' }}
                            </span>
                            @if($notice->user)
                                <span>
                                    <i class="fas fa-user me-1"></i>
                                    {{ $notice->user->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
                        {{ ucfirst($notice->priority ?? 'normal') }}
                    </span>
                </div>

                <hr>

                <div class="notice-content">
                    {!! nl2br(e($notice->content)) !!}
                </div>

                @if($notice->attachments && $notice->attachments->count() > 0)
                    <hr>
                    <h6 class="mb-3">Attachments</h6>
                    <div class="list-group">
                        @foreach($notice->attachments as $attachment)
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-file me-2"></i>
                                    {{ $attachment->original_name ?? 'Attachment' }}
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('student.notices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Notices
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
