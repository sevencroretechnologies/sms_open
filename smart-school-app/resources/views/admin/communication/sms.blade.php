@extends('layouts.app')

@section('title', 'SMS Communication')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Communication</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">SMS</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.sms.settings') }}" class="btn btn-outline-secondary">
                <i class="bi bi-gear me-1"></i> Settings
            </a>
            <a href="{{ route('admin.sms.templates') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-text me-1"></i> Templates
            </a>
            <a href="{{ route('admin.sms.compose') }}" class="btn btn-primary">
                <i class="bi bi-chat-dots me-1"></i> Send SMS
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-25 rounded p-3">
                                <i class="bi bi-chat-dots text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Total Sent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-25 rounded p-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Delivered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-25 rounded p-3">
                                <i class="bi bi-wallet2 text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Credits Left</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-25 rounded p-3">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0">0</h3>
                            <small class="text-muted">Failed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-chat-dots me-2"></i>SMS History
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Message</th>
                        <th>Recipients</th>
                        <th>Sent By</th>
                        <th>Sent At</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                                <p class="mb-2">No SMS sent yet</p>
                                <a href="{{ route('admin.sms.compose') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-chat-dots me-1"></i> Send First SMS
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
