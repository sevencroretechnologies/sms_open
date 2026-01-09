{{-- SMS Settings View --}}
{{-- Admin SMS gateway settings --}}

@extends('layouts.app')

@section('title', 'SMS Settings')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Gateway Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.sms') }}">SMS</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.sms') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
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

    @if($errors->any())
        <x-alert type="danger" :dismissible="true">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.communication.sms-settings-update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Gateway Selection -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-gear me-2"></i>
                        SMS Gateway
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Gateway Provider <span class="text-danger">*</span></label>
                            <select name="gateway" id="gatewaySelect" class="form-select @error('gateway') is-invalid @enderror" required>
                                <option value="">Select Gateway</option>
                                <option value="twilio" {{ old('gateway', $settings->gateway ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                <option value="nexmo" {{ old('gateway', $settings->gateway ?? '') == 'nexmo' ? 'selected' : '' }}>Nexmo (Vonage)</option>
                                <option value="msg91" {{ old('gateway', $settings->gateway ?? '') == 'msg91' ? 'selected' : '' }}>MSG91</option>
                                <option value="textlocal" {{ old('gateway', $settings->gateway ?? '') == 'textlocal' ? 'selected' : '' }}>TextLocal</option>
                                <option value="custom" {{ old('gateway', $settings->gateway ?? '') == 'custom' ? 'selected' : '' }}>Custom API</option>
                            </select>
                            @error('gateway')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sender ID</label>
                            <input type="text" name="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ old('sender_id', $settings->sender_id ?? '') }}" placeholder="e.g., SCHOOL">
                            @error('sender_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                <!-- API Credentials -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-key me-2"></i>
                        API Credentials
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">API Key / Account SID <span class="text-danger">*</span></label>
                            <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key', $settings->api_key ?? '') }}" required>
                            @error('api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">API Secret / Auth Token <span class="text-danger">*</span></label>
                            <input type="password" name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ old('api_secret', $settings->api_secret ?? '') }}" required>
                            @error('api_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12" id="customApiUrl" style="display: none;">
                            <label class="form-label">Custom API URL</label>
                            <input type="url" name="api_url" class="form-control @error('api_url') is-invalid @enderror" value="{{ old('api_url', $settings->api_url ?? '') }}" placeholder="https://api.example.com/sms/send">
                            @error('api_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                <!-- Additional Settings -->
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-sliders me-2"></i>
                        Additional Settings
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Country Code</label>
                            <input type="text" name="country_code" class="form-control @error('country_code') is-invalid @enderror" value="{{ old('country_code', $settings->country_code ?? '+1') }}" placeholder="+1">
                            @error('country_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SMS Type</label>
                            <select name="sms_type" class="form-select @error('sms_type') is-invalid @enderror">
                                <option value="transactional" {{ old('sms_type', $settings->sms_type ?? '') == 'transactional' ? 'selected' : '' }}>Transactional</option>
                                <option value="promotional" {{ old('sms_type', $settings->sms_type ?? '') == 'promotional' ? 'selected' : '' }}>Promotional</option>
                            </select>
                            @error('sms_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $settings->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Enable SMS Gateway</label>
                            </div>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-info" onclick="testConnection()">
                                <i class="bi bi-lightning me-1"></i> Test Connection
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Settings
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </div>

            <div class="col-lg-4">
                <!-- Status -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-activity me-2"></i>
                        Gateway Status
                    </x-slot>
                    
                    <div class="text-center">
                        @if($settings->is_active ?? false)
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-check-lg fs-3"></i>
                            </div>
                            <h5 class="text-success">Active</h5>
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-x-lg fs-3"></i>
                            </div>
                            <h5 class="text-danger">Inactive</h5>
                        @endif
                        <p class="text-muted small mb-0">Last tested: {{ isset($settings->last_tested_at) ? $settings->last_tested_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </x-card>

                <!-- Credits -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-credit-card me-2"></i>
                        SMS Credits
                    </x-slot>
                    
                    <div class="text-center">
                        <h2 class="mb-0">{{ $settings->credits_remaining ?? 0 }}</h2>
                        <small class="text-muted">Credits Remaining</small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">This Month</span>
                        <span>{{ $settings->credits_used_this_month ?? 0 }} used</span>
                    </div>
                </x-card>

                <!-- Help -->
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-question-circle me-2"></i>
                        Need Help?
                    </x-slot>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <a href="https://www.twilio.com/docs" target="_blank" class="text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Twilio Documentation
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="https://developer.vonage.com" target="_blank" class="text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Nexmo Documentation
                            </a>
                        </li>
                        <li class="mb-0">
                            <a href="https://msg91.com/help" target="_blank" class="text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-2"></i>MSG91 Documentation
                            </a>
                        </li>
                    </ul>
                </x-card>
            </div>
        </div>
    </form>
</div>

<!-- Test Result Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connection Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="testResult">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Testing...</span>
                </div>
                <p class="mt-3">Testing connection...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('gatewaySelect').addEventListener('change', function() {
    document.getElementById('customApiUrl').style.display = this.value === 'custom' ? 'block' : 'none';
});

function testConnection() {
    const modal = new bootstrap.Modal(document.getElementById('testModal'));
    modal.show();
    
    fetch('/admin/communication/sms-settings/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('testResult');
        if (data.success) {
            resultDiv.innerHTML = '<div class="text-success"><i class="bi bi-check-circle fs-1"></i><p class="mt-3">Connection successful!</p></div>';
        } else {
            resultDiv.innerHTML = '<div class="text-danger"><i class="bi bi-x-circle fs-1"></i><p class="mt-3">Connection failed: ' + (data.message || 'Unknown error') + '</p></div>';
        }
    })
    .catch(error => {
        document.getElementById('testResult').innerHTML = '<div class="text-danger"><i class="bi bi-x-circle fs-1"></i><p class="mt-3">Connection failed: ' + error.message + '</p></div>';
    });
}
</script>
@endpush
