{{-- SMS Settings View --}}
{{-- Prompt 273: SMS gateway configuration, API credentials, sender ID settings --}}

@extends('layouts.app')

@section('title', 'SMS Settings')

@section('content')
<div x-data="smsSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">SMS</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.general') ?? '#' }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="button" class="btn btn-primary" @click="saveSettings()" :disabled="saving">
                <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Save Settings</span>
                <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- SMS Gateway Configuration -->
        <div class="col-lg-8">
            <form action="{{ route('settings.sms.update') ?? '#' }}" method="POST" @submit.prevent="saveSettings()">
                @csrf
                @method('PUT')

                <!-- Gateway Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-broadcast me-2 text-primary"></i>SMS Gateway</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">SMS Gateway Provider <span class="text-danger">*</span></label>
                                <select class="form-select @error('sms_gateway') is-invalid @enderror" 
                                        name="sms_gateway" x-model="form.sms_gateway" @change="updateGatewayFields()">
                                    <option value="">Select Gateway</option>
                                    <option value="twilio">Twilio</option>
                                    <option value="nexmo">Nexmo (Vonage)</option>
                                    <option value="msg91">MSG91</option>
                                    <option value="textlocal">TextLocal</option>
                                    <option value="fast2sms">Fast2SMS</option>
                                    <option value="custom">Custom API</option>
                                </select>
                                @error('sms_gateway')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gateway Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="sms_enabled" 
                                           x-model="form.sms_enabled" id="smsEnabled">
                                    <label class="form-check-label" for="smsEnabled">
                                        <span x-text="form.sms_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Credentials -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-key me-2 text-warning"></i>API Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Twilio Fields -->
                            <template x-if="form.sms_gateway === 'twilio'">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Account SID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="twilio_sid" 
                                                   x-model="form.twilio_sid" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Auth Token <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input :type="showToken ? 'text' : 'password'" class="form-control" 
                                                       name="twilio_token" x-model="form.twilio_token">
                                                <button class="btn btn-outline-secondary" type="button" @click="showToken = !showToken">
                                                    <i :class="showToken ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="twilio_phone" 
                                                   x-model="form.twilio_phone" placeholder="+1234567890">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- MSG91 Fields -->
                            <template x-if="form.sms_gateway === 'msg91'">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input :type="showToken ? 'text' : 'password'" class="form-control" 
                                                       name="msg91_api_key" x-model="form.msg91_api_key">
                                                <button class="btn btn-outline-secondary" type="button" @click="showToken = !showToken">
                                                    <i :class="showToken ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sender ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="msg91_sender_id" 
                                                   x-model="form.msg91_sender_id" placeholder="SCHOOL" maxlength="6">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Route</label>
                                            <select class="form-select" name="msg91_route" x-model="form.msg91_route">
                                                <option value="4">Transactional</option>
                                                <option value="1">Promotional</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- TextLocal Fields -->
                            <template x-if="form.sms_gateway === 'textlocal'">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input :type="showToken ? 'text' : 'password'" class="form-control" 
                                                       name="textlocal_api_key" x-model="form.textlocal_api_key">
                                                <button class="btn btn-outline-secondary" type="button" @click="showToken = !showToken">
                                                    <i :class="showToken ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sender Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="textlocal_sender" 
                                                   x-model="form.textlocal_sender" placeholder="SCHOOL">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Fast2SMS Fields -->
                            <template x-if="form.sms_gateway === 'fast2sms'">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input :type="showToken ? 'text' : 'password'" class="form-control" 
                                                       name="fast2sms_api_key" x-model="form.fast2sms_api_key">
                                                <button class="btn btn-outline-secondary" type="button" @click="showToken = !showToken">
                                                    <i :class="showToken ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sender ID</label>
                                            <input type="text" class="form-control" name="fast2sms_sender_id" 
                                                   x-model="form.fast2sms_sender_id" placeholder="FSTSMS">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Route</label>
                                            <select class="form-select" name="fast2sms_route" x-model="form.fast2sms_route">
                                                <option value="dlt">DLT Manual</option>
                                                <option value="q">Quick SMS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Custom API Fields -->
                            <template x-if="form.sms_gateway === 'custom'">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">API URL <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control" name="custom_api_url" 
                                                   x-model="form.custom_api_url" placeholder="https://api.example.com/sms/send">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">API Key</label>
                                            <div class="input-group">
                                                <input :type="showToken ? 'text' : 'password'" class="form-control" 
                                                       name="custom_api_key" x-model="form.custom_api_key">
                                                <button class="btn btn-outline-secondary" type="button" @click="showToken = !showToken">
                                                    <i :class="showToken ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Request Method</label>
                                            <select class="form-select" name="custom_method" x-model="form.custom_method">
                                                <option value="POST">POST</option>
                                                <option value="GET">GET</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- No Gateway Selected -->
                            <template x-if="!form.sms_gateway">
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Please select an SMS gateway provider to configure API credentials.
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Sender Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2 text-success"></i>Sender Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Default Sender ID</label>
                                <input type="text" class="form-control @error('sender_id') is-invalid @enderror" 
                                       name="sender_id" x-model="form.sender_id" placeholder="SCHOOL" maxlength="11">
                                <small class="text-muted">Max 11 characters for alphanumeric sender ID</small>
                                @error('sender_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMS Type</label>
                                <select class="form-select @error('sms_type') is-invalid @enderror" 
                                        name="sms_type" x-model="form.sms_type">
                                    <option value="transactional">Transactional</option>
                                    <option value="promotional">Promotional</option>
                                </select>
                                @error('sms_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Character Limit</label>
                                <input type="number" class="form-control" name="char_limit" 
                                       x-model="form.char_limit" min="160" max="1600">
                                <small class="text-muted">Standard SMS: 160 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unicode Support</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="unicode_support" 
                                           x-model="form.unicode_support" id="unicodeSupport">
                                    <label class="form-check-label" for="unicodeSupport">
                                        Enable Unicode (for non-English characters)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DLT Settings (India) -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2 text-info"></i>DLT Settings (India)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            DLT registration is mandatory for sending SMS in India. Please ensure your templates are registered.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Entity ID (Principal Entity ID)</label>
                                <input type="text" class="form-control @error('dlt_entity_id') is-invalid @enderror" 
                                       name="dlt_entity_id" x-model="form.dlt_entity_id">
                                @error('dlt_entity_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Template ID</label>
                                <input type="text" class="form-control @error('dlt_template_id') is-invalid @enderror" 
                                       name="dlt_template_id" x-model="form.dlt_template_id">
                                @error('dlt_template_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Test SMS -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-send me-2 text-primary"></i>Test SMS</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" x-model="testPhone" placeholder="+91XXXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" x-model="testMessage" rows="3" 
                                  placeholder="Enter test message..."></textarea>
                        <small class="text-muted">
                            <span x-text="testMessage.length"></span>/160 characters
                        </small>
                    </div>
                    <button type="button" class="btn btn-primary w-100" @click="sendTestSms()" :disabled="sendingTest">
                        <span x-show="!sendingTest"><i class="bi bi-send me-1"></i> Send Test SMS</span>
                        <span x-show="sendingTest"><span class="spinner-border spinner-border-sm me-1"></span> Sending...</span>
                    </button>
                </div>
            </div>

            <!-- SMS Balance -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2 text-success"></i>SMS Balance</h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2" x-text="smsBalance">0</div>
                    <p class="text-muted mb-3">SMS Credits Available</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="checkBalance()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Balance
                    </button>
                </div>
            </div>

            <!-- SMS Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-warning"></i>This Month</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Sent</span>
                        <strong x-text="stats.total_sent">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivered</span>
                        <strong class="text-success" x-text="stats.delivered">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Failed</span>
                        <strong class="text-danger" x-text="stats.failed">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pending</span>
                        <strong class="text-warning" x-text="stats.pending">0</strong>
                    </div>
                    <hr>
                    <a href="{{ route('sms.logs') ?? '#' }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-list-ul me-1"></i> View SMS Logs
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-link-45deg me-2 text-info"></i>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('sms.send') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-send me-2"></i> Send Bulk SMS
                        </a>
                        <a href="{{ route('settings.notifications') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-bell me-2"></i> Notification Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function smsSettings() {
    return {
        saving: false,
        sendingTest: false,
        showToken: false,
        testPhone: '',
        testMessage: 'This is a test SMS from Smart School Management System.',
        smsBalance: {{ $smsBalance ?? 1000 }},
        stats: {
            total_sent: {{ $stats['total_sent'] ?? 245 }},
            delivered: {{ $stats['delivered'] ?? 238 }},
            failed: {{ $stats['failed'] ?? 5 }},
            pending: {{ $stats['pending'] ?? 2 }}
        },
        form: {
            sms_gateway: '{{ $settings["sms_gateway"] ?? "" }}',
            sms_enabled: {{ ($settings['sms_enabled'] ?? false) ? 'true' : 'false' }},
            sender_id: '{{ $settings["sender_id"] ?? "SCHOOL" }}',
            sms_type: '{{ $settings["sms_type"] ?? "transactional" }}',
            char_limit: {{ $settings['char_limit'] ?? 160 }},
            unicode_support: {{ ($settings['unicode_support'] ?? false) ? 'true' : 'false' }},
            dlt_entity_id: '{{ $settings["dlt_entity_id"] ?? "" }}',
            dlt_template_id: '{{ $settings["dlt_template_id"] ?? "" }}',
            // Twilio
            twilio_sid: '{{ $settings["twilio_sid"] ?? "" }}',
            twilio_token: '{{ $settings["twilio_token"] ?? "" }}',
            twilio_phone: '{{ $settings["twilio_phone"] ?? "" }}',
            // MSG91
            msg91_api_key: '{{ $settings["msg91_api_key"] ?? "" }}',
            msg91_sender_id: '{{ $settings["msg91_sender_id"] ?? "" }}',
            msg91_route: '{{ $settings["msg91_route"] ?? "4" }}',
            // TextLocal
            textlocal_api_key: '{{ $settings["textlocal_api_key"] ?? "" }}',
            textlocal_sender: '{{ $settings["textlocal_sender"] ?? "" }}',
            // Fast2SMS
            fast2sms_api_key: '{{ $settings["fast2sms_api_key"] ?? "" }}',
            fast2sms_sender_id: '{{ $settings["fast2sms_sender_id"] ?? "" }}',
            fast2sms_route: '{{ $settings["fast2sms_route"] ?? "dlt" }}',
            // Custom
            custom_api_url: '{{ $settings["custom_api_url"] ?? "" }}',
            custom_api_key: '{{ $settings["custom_api_key"] ?? "" }}',
            custom_method: '{{ $settings["custom_method"] ?? "POST" }}'
        },
        
        updateGatewayFields() {
            // Reset gateway-specific fields when switching
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved!',
                    text: 'SMS settings have been updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save settings. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        },
        
        async sendTestSms() {
            if (!this.testPhone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Required',
                    text: 'Please enter a phone number to send test SMS.'
                });
                return;
            }
            
            this.sendingTest = true;
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Test SMS Sent!',
                    text: 'Test SMS has been sent to ' + this.testPhone,
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: 'Failed to send test SMS. Please check your settings.'
                });
            } finally {
                this.sendingTest = false;
            }
        },
        
        async checkBalance() {
            // Simulate balance check
            Swal.fire({
                icon: 'info',
                title: 'SMS Balance',
                text: 'Current balance: ' + this.smsBalance + ' credits',
                timer: 2000,
                showConfirmButton: false
            });
        }
    };
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
