{{-- Emails List View --}}
{{-- Admin emails listing page --}}

@extends('layouts.app')

@section('title', 'Emails')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Email Communication</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Emails</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.email-templates') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-1"></i> Templates
            </a>
            <a href="{{ route('admin.communication.compose-email') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Compose Email
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
                    <h3 class="mb-0">{{ $totalEmails ?? 0 }}</h3>
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
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $failedCount ?? 0 }}</h3>
                    <small class="text-muted">Failed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Emails Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <i class="bi bi-envelope me-2"></i>
            Sent Emails
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Subject</th>
                        <th>Recipients</th>
                        <th>Sent By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails ?? [] as $index => $email)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.communication.email-show', $email) }}" class="text-decoration-none fw-medium">
                                    {{ Str::limit($email->subject ?? 'No Subject', 40) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $email->recipients_count ?? 0 }} recipients</span>
                            </td>
                            <td>{{ $email->sender->name ?? 'System' }}</td>
                            <td>{{ isset($email->created_at) ? $email->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match($email->status ?? 'pending') {
                                        'sent', 'delivered' => 'bg-success',
                                        'pending' => 'bg-warning',
                                        'failed' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($email->status ?? 'pending') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.communication.email-show', $email) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-envelope fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No emails sent yet</p>
                                    <a href="{{ route('admin.communication.compose-email') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Compose First Email
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($emails) && method_exists($emails, 'hasPages') && $emails->hasPages())
        <x-slot name="footer">
            {{ $emails->links() }}
        </x-slot>
        @endif
    </x-card>
</div>
@endsection
