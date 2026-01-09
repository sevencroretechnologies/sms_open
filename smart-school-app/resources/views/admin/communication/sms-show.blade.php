{{-- SMS Show View --}}
{{-- Admin SMS details --}}

@extends('layouts.app')

@section('title', 'View SMS')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.sms') }}">SMS</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.sms') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- SMS Content -->
            <x-card class="mb-4">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-chat-dots me-2"></i>Message</span>
                        @php
                            $statusClass = match($sms->status ?? 'pending') {
                                'sent', 'delivered' => 'bg-success',
                                'pending' => 'bg-warning',
                                'failed' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($sms->status ?? 'pending') }}</span>
                    </div>
                </x-slot>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-person fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-medium">{{ $sms->sender->name ?? 'System' }}</div>
                            <small class="text-muted">{{ isset($sms->created_at) ? $sms->created_at->format('d M Y, h:i A') : 'N/A' }}</small>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light rounded">
                        {{ $sms->message ?? 'SMS content will appear here.' }}
                    </div>
                </div>

                <div class="d-flex justify-content-between text-muted small">
                    <span>{{ strlen($sms->message ?? '') }} characters</span>
                    <span>{{ ceil(strlen($sms->message ?? '') / 160) }} SMS segment(s)</span>
                </div>
            </x-card>
        </div>

        <div class="col-lg-4">
            <!-- Recipients -->
            <x-card class="mb-4" :noPadding="true">
                <x-slot name="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span><i class="bi bi-people me-2"></i>Recipients</span>
                        <span class="badge bg-primary">{{ $sms->recipients_count ?? 0 }}</span>
                    </div>
                </x-slot>

                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($sms->recipients ?? [] as $recipient)
                        <div class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded bg-light" style="width: 32px; height: 32px;">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <div>
                                    <div class="fw-medium small">{{ $recipient->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $recipient->phone ?? '' }}</small>
                                </div>
                            </div>
                            @php
                                $recipientStatus = $recipient->pivot->status ?? 'pending';
                                $recipientStatusClass = match($recipientStatus) {
                                    'delivered' => 'bg-success',
                                    'sent' => 'bg-info',
                                    'pending' => 'bg-warning',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $recipientStatusClass }} small">{{ ucfirst($recipientStatus) }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No recipients
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- SMS Info -->
            <x-card>
                <x-slot name="header">
                    <i class="bi bi-info-circle me-2"></i>
                    SMS Information
                </x-slot>
                
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sent By</span>
                        <span>{{ $sms->sender->name ?? 'System' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Sent At</span>
                        <span>{{ isset($sms->created_at) ? $sms->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Status</span>
                        <span class="badge {{ $statusClass }}">{{ ucfirst($sms->status ?? 'pending') }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Recipients</span>
                        <span>{{ $sms->recipients_count ?? 0 }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Credits Used</span>
                        <span>{{ $sms->credits_used ?? 0 }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
