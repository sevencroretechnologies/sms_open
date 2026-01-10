@extends('layouts.app')

@section('title', $notice->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notice Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.notices.index') }}">Notices</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($notice->title, 30) }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('teacher.notices.index') }}" class="btn btn-outline-secondary mt-3 mt-md-0">
            <i class="bi bi-arrow-left me-1"></i> Back to Notices
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-{{ $notice->priority == 'high' ? 'danger' : ($notice->priority == 'medium' ? 'warning' : 'info') }}">
                        {{ ucfirst($notice->priority ?? 'normal') }} Priority
                    </span>
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        {{ $notice->publish_date ? $notice->publish_date->format('d M Y, h:i A') : '' }}
                    </small>
                </div>
                
                <h3 class="mb-4">{{ $notice->title }}</h3>
                
                <div class="notice-content">
                    {!! nl2br(e($notice->content)) !!}
                </div>

                @if($notice->attachments && $notice->attachments->count() > 0)
                    <hr>
                    <h6 class="mb-3"><i class="bi bi-paperclip me-1"></i> Attachments</h6>
                    <div class="list-group">
                        @foreach($notice->attachments as $attachment)
                            <a href="{{ Storage::url($attachment->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                <span>
                                    <i class="bi bi-file-earmark me-2"></i>
                                    {{ $attachment->original_name ?? 'Attachment' }}
                                </span>
                                <i class="bi bi-download"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>Notice Information
                </x-slot>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 100px;">Audience</td>
                        <td class="text-capitalize">{{ $notice->audience ?? 'All' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Published</td>
                        <td>{{ $notice->publish_date ? $notice->publish_date->format('d M Y') : '-' }}</td>
                    </tr>
                    @if($notice->expiry_date)
                        <tr>
                            <td class="text-muted">Expires</td>
                            <td>{{ $notice->expiry_date->format('d M Y') }}</td>
                        </tr>
                    @endif
                    @if($notice->creator)
                        <tr>
                            <td class="text-muted">Posted By</td>
                            <td>{{ $notice->creator->name ?? 'Admin' }}</td>
                        </tr>
                    @endif
                </table>
            </x-card>
        </div>
    </div>
</div>
@endsection
