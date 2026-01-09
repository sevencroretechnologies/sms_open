{{-- Email Show View --}}
{{-- Admin email details --}}

@extends('layouts.app')

@section('title', 'View Email')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Email Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.emails') }}">Emails</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.emails') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Email Content -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-envelope me-2"></i>{{ $email->subject ?? 'Email Subject' }}</span>
                        @php
                            $statusClass = match($email->status ?? 'pending') {
                                'sent', 'delivered' => 'bg-success',
                                'pending' => 'bg-warning',
                                'failed' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($email->status ?? 'pending') }}</span>
                    </div>
                </x-slot>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-person fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-medium">{{ $email->sender->name ?? 'System' }}</div>
                            <small class="text-muted">{{ isset($email->created_at) ? $email->created_at->format('d M Y, h:i A') : 'N/A' }}</small>
                        </div>
                    </div>
                    
                    <div class="email-body">
                        {!! nl2br(e($email->message ?? 'Email content will appear here.')) !!}
                    </div>
                </div>

                @if(isset($email->attachments) && count($email->attachments) > 0)
                <div class="border-top pt-3">
                    <h6 class="mb-3"><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($email->attachments as $attachment)
                            <a href="{{ $attachment->url }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="bi bi-file-earmark me-1"></i>{{ $attachment->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-4">
            <!-- Recipients -->
            <x-card class="mb-4" :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-people me-2"></i>Recipients</span>
                        <span class="badge bg-primary">{{ $email->recipients_count ?? 0 }}</span>
                    </div>
                </x-slot>

                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($email->recipients ?? [] as $recipient)
                        <div class="list-group-item d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-light" style="width: 32px; height: 32px;">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <div>
                                <div class="fw-medium small">{{ $recipient->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $recipient->email ?? '' }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No recipients
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- Email Info -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    Email Information
                </x-slot>
                
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sent By</span>
                        <span>{{ $email->sender->name ?? 'System' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sent At</span>
                        <span>{{ isset($email->created_at) ? $email->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Status</span>
                        <span class="badge {{ $statusClass }}">{{ ucfirst($email->status ?? 'pending') }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Recipients</span>
                        <span>{{ $email->recipients_count ?? 0 }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
