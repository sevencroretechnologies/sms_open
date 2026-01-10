@extends('layouts.app')

@section('title', 'View SMS')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">View SMS</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.index') }}">SMS</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.sms.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-chat-dots me-2"></i>SMS Details
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex gap-3 text-muted small mb-3">
                            <span><i class="bi bi-person me-1"></i>Sent by: {{ $sms->sender->name ?? 'Admin' }}</span>
                            <span><i class="bi bi-clock me-1"></i>{{ $sms->created_at ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="bg-light rounded p-3">
                        <p class="mb-0">{{ $sms->message ?? 'No message content available.' }}</p>
                    </div>
                    <div class="mt-3 text-muted small">
                        <span>Characters: {{ strlen($sms->message ?? '') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>Recipients
                </div>
                <div class="card-body">
                    <p class="text-muted">No recipients data available.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Status
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-success">{{ $sms->status ?? 'Sent' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Sent At</small>
                        <strong>{{ $sms->sent_at ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Delivered</small>
                        <strong>{{ $sms->delivered_count ?? 0 }} / {{ $sms->recipients_count ?? 0 }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Credits Used</small>
                        <strong>{{ $sms->credits_used ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
