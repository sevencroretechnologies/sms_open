@extends('layouts.app')

@section('title', 'SMS Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.index') }}">SMS</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sms.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>SMS Gateway Configuration
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">SMS Gateway Provider <span class="text-danger">*</span></label>
                            <select name="gateway" class="form-select" required>
                                <option value="">Select Provider</option>
                                <option value="twilio">Twilio</option>
                                <option value="nexmo">Nexmo (Vonage)</option>
                                <option value="msg91">MSG91</option>
                                <option value="textlocal">TextLocal</option>
                                <option value="custom">Custom API</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Key / Account SID <span class="text-danger">*</span></label>
                            <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $settings['api_key'] ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Secret / Auth Token <span class="text-danger">*</span></label>
                            <input type="password" name="api_secret" class="form-control" value="{{ old('api_secret', $settings['api_secret'] ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sender ID</label>
                            <input type="text" name="sender_id" class="form-control" value="{{ old('sender_id', $settings['sender_id'] ?? '') }}" placeholder="e.g., SCHOOL">
                            <small class="text-muted">The name that appears as the sender (max 11 characters)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Endpoint (for Custom API)</label>
                            <input type="url" name="api_endpoint" class="form-control" value="{{ old('api_endpoint', $settings['api_endpoint'] ?? '') }}" placeholder="https://api.example.com/sms/send">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-sliders me-2"></i>General Settings
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sms_enabled" id="sms_enabled" value="1" {{ old('sms_enabled', $settings['sms_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_enabled">Enable SMS Service</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="unicode_enabled" id="unicode_enabled" value="1" {{ old('unicode_enabled', $settings['unicode_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="unicode_enabled">Enable Unicode (Non-English)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-wallet2 me-2"></i>Credit Balance
                    </div>
                    <div class="card-body text-center">
                        <h2 class="mb-0">0</h2>
                        <small class="text-muted">SMS Credits Available</small>
                        <hr>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Buy Credits
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>Test SMS
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Test Phone Number</label>
                            <input type="text" name="test_phone" class="form-control" placeholder="+1234567890">
                        </div>
                        <button type="button" class="btn btn-outline-primary w-100">
                            <i class="bi bi-send me-1"></i> Send Test SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.sms.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
