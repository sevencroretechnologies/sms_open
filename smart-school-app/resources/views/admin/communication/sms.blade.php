{{-- SMS List View --}}
{{-- Admin SMS listing page --}}

@extends('layouts.app')

@section('title', 'SMS')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Communication</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">SMS</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.sms-settings') }}" class="btn btn-outline-secondary">
                <i class="bi bi-gear me-1"></i> Settings
            </a>
            <a href="{{ route('admin.communication.sms-templates') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-1"></i> Templates
            </a>
            <a href="{{ route('admin.communication.compose-sms') }}" class="btn btn-primary">
                <i class="bi bi-chat-dots me-1"></i> Compose SMS
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

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $totalSms ?? 0 }}</h3>
                    <small class="text-muted">Total Sent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $deliveredCount ?? 0 }}</h3>
                    <small class="text-muted">Delivered</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $pendingCount ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $creditsRemaining ?? 0 }}</h3>
                    <small class="text-muted">Credits Left</small>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-chat-dots me-2"></i>
            Sent Messages
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Message</th>
                        <th>Recipients</th>
                        <th>Sent By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages ?? [] as $index => $sms)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.communication.sms-show', $sms) }}" class="text-decoration-none">
                                    {{ Str::limit($sms->message ?? 'No message', 50) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $sms->recipients_count ?? 0 }} recipients</span>
                            </td>
                            <td>{{ $sms->sender->name ?? 'System' }}</td>
                            <td>{{ isset($sms->created_at) ? $sms->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match($sms->status ?? 'pending') {
                                        'sent', 'delivered' => 'bg-success',
                                        'pending' => 'bg-warning',
                                        'failed' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($sms->status ?? 'pending') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.communication.sms-show', $sms) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No SMS sent yet</p>
                                    <a href="{{ route('admin.communication.compose-sms') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-chat-dots me-1"></i> Send First SMS
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($messages) && $messages->hasPages())
        <x-slot name="footer">
            {{ $messages->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection
