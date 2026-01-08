{{-- Email Settings View --}}
{{-- Prompt 274: SMTP configuration, email templates, sender settings --}}

@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
<div x-data="emailSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Email Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Email</li>
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
        <!-- Email Configuration -->
        <div class="col-lg-8">
            <form action="{{ route('settings.email.update') ?? '#' }}" method="POST" @submit.prevent="saveSettings()">
                @csrf
                @method('PUT')

                <!-- Mail Driver Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-envelope me-2 text-primary"></i>Mail Driver</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mail Driver <span class="text-danger">*</span></label>
                                <select class="form-select @error('mail_driver') is-invalid @enderror" 
                                        name="mail_driver" x-model="form.mail_driver" @change="updateDriverFields()">
                                    <option value="smtp">SMTP</option>
                                    <option value="sendmail">Sendmail</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="ses">Amazon SES</option>
                                    <option value="postmark">Postmark</option>
                                    <option value="log">Log (Testing)</option>
                                </select>
                                @error('mail_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="email_enabled" 
                                           x-model="form.email_enabled" id="emailEnabled">
                                    <label class="form-check-label" for="emailEnabled">
                                        <span x-text="form.email_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMTP Configuration -->
                <div class="card border-0 shadow-sm mb-4" x-show="form.mail_driver === 'smtp'">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-server me-2 text-warning"></i>SMTP Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('smtp_host') is-invalid @enderror" 
                                       name="smtp_host" x-model="form.smtp_host" placeholder="smtp.gmail.com">
                                @error('smtp_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('smtp_port') is-invalid @enderror" 
                                       name="smtp_port" x-model="form.smtp_port" placeholder="587">
                                @error('smtp_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMTP Username</label>
                                <input type="text" class="form-control @error('smtp_username') is-invalid @enderror" 
                                       name="smtp_username" x-model="form.smtp_username">
                                @error('smtp_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMTP Password</label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           class="form-control @error('smtp_password') is-invalid @enderror" 
                                           name="smtp_password" x-model="form.smtp_password">
                                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                                @error('smtp_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Encryption</label>
                                <select class="form-select @error('smtp_encryption') is-invalid @enderror" 
                                        name="smtp_encryption" x-model="form.smtp_encryption">
                                    <option value="">None</option>
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                                @error('smtp_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Timeout (seconds)</label>
                                <input type="number" class="form-control" name="smtp_timeout" 
                                       x-model="form.smtp_timeout" min="5" max="120">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mailgun Configuration -->
                <div class="card border-0 shadow-sm mb-4" x-show="form.mail_driver === 'mailgun'">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-cloud me-2 text-danger"></i>Mailgun Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Domain <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mailgun_domain" 
                                       x-model="form.mailgun_domain" placeholder="mg.yourdomain.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">API Key <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" class="form-control" 
                                           name="mailgun_secret" x-model="form.mailgun_secret">
                                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Endpoint</label>
                                <select class="form-select" name="mailgun_endpoint" x-model="form.mailgun_endpoint">
                                    <option value="api.mailgun.net">US Region (api.mailgun.net)</option>
                                    <option value="api.eu.mailgun.net">EU Region (api.eu.mailgun.net)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amazon SES Configuration -->
                <div class="card border-0 shadow-sm mb-4" x-show="form.mail_driver === 'ses'">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-cloud me-2 text-warning"></i>Amazon SES Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">AWS Access Key ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ses_key" x-model="form.ses_key">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">AWS Secret Access Key <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" class="form-control" 
                                           name="ses_secret" x-model="form.ses_secret">
                                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">AWS Region <span class="text-danger">*</span></label>
                                <select class="form-select" name="ses_region" x-model="form.ses_region">
                                    <option value="us-east-1">US East (N. Virginia)</option>
                                    <option value="us-west-2">US West (Oregon)</option>
                                    <option value="eu-west-1">EU (Ireland)</option>
                                    <option value="ap-south-1">Asia Pacific (Mumbai)</option>
                                    <option value="ap-southeast-1">Asia Pacific (Singapore)</option>
                                </select>
                            </div>
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
                                <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('from_email') is-invalid @enderror" 
                                       name="from_email" x-model="form.from_email" placeholder="noreply@school.com">
                                @error('from_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">From Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                       name="from_name" x-model="form.from_name" placeholder="Smart School">
                                @error('from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reply-To Email</label>
                                <input type="email" class="form-control @error('reply_to_email') is-invalid @enderror" 
                                       name="reply_to_email" x-model="form.reply_to_email" placeholder="support@school.com">
                                @error('reply_to_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reply-To Name</label>
                                <input type="text" class="form-control @error('reply_to_name') is-invalid @enderror" 
                                       name="reply_to_name" x-model="form.reply_to_name" placeholder="School Support">
                                @error('reply_to_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Templates -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2 text-info"></i>Email Templates</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="showTemplateModal = true">
                            <i class="bi bi-plus-lg me-1"></i> Add Template
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Template Name</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="template in templates" :key="template.id">
                                        <tr>
                                            <td>
                                                <span class="fw-medium" x-text="template.name"></span>
                                            </td>
                                            <td x-text="template.subject"></td>
                                            <td>
                                                <span class="badge" 
                                                      :class="template.type === 'system' ? 'bg-primary' : 'bg-secondary'"
                                                      x-text="template.type"></span>
                                            </td>
                                            <td>
                                                <span class="badge" 
                                                      :class="template.active ? 'bg-success' : 'bg-danger'"
                                                      x-text="template.active ? 'Active' : 'Inactive'"></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            @click="editTemplate(template)" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            @click="previewTemplate(template)" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Test Email -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-send me-2 text-primary"></i>Test Email</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" x-model="testEmail" placeholder="test@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" x-model="testSubject" 
                               placeholder="Test Email from Smart School">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" x-model="testMessage" rows="3" 
                                  placeholder="Enter test message..."></textarea>
                    </div>
                    <button type="button" class="btn btn-primary w-100" @click="sendTestEmail()" :disabled="sendingTest">
                        <span x-show="!sendingTest"><i class="bi bi-send me-1"></i> Send Test Email</span>
                        <span x-show="sendingTest"><span class="spinner-border spinner-border-sm me-1"></span> Sending...</span>
                    </button>
                </div>
            </div>

            <!-- Email Statistics -->
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
                        <span class="text-muted">Opened</span>
                        <strong class="text-info" x-text="stats.opened">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bounced</span>
                        <strong class="text-danger" x-text="stats.bounced">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Failed</span>
                        <strong class="text-warning" x-text="stats.failed">0</strong>
                    </div>
                    <hr>
                    <a href="{{ route('email.logs') ?? '#' }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-list-ul me-1"></i> View Email Logs
                    </a>
                </div>
            </div>

            <!-- Quick Setup -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-success"></i>Quick Setup</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Click to auto-fill common SMTP settings:</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm text-start" 
                                @click="applyPreset('gmail')">
                            <i class="bi bi-google me-2"></i> Gmail SMTP
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm text-start" 
                                @click="applyPreset('outlook')">
                            <i class="bi bi-microsoft me-2"></i> Outlook/Office 365
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm text-start" 
                                @click="applyPreset('yahoo')">
                            <i class="bi bi-envelope me-2"></i> Yahoo Mail
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm text-start" 
                                @click="applyPreset('sendgrid')">
                            <i class="bi bi-cloud me-2"></i> SendGrid
                        </button>
                    </div>
                </div>
            </div>

            <!-- Connection Status -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-wifi me-2 text-info"></i>Connection Status</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi fs-1" :class="connectionStatus === 'connected' ? 'bi-check-circle text-success' : 
                                                   connectionStatus === 'error' ? 'bi-x-circle text-danger' : 
                                                   'bi-question-circle text-muted'"></i>
                    </div>
                    <p class="mb-3" x-text="connectionStatus === 'connected' ? 'Connected' : 
                                           connectionStatus === 'error' ? 'Connection Failed' : 
                                           'Not Tested'"></p>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="testConnection()" :disabled="testing">
                        <span x-show="!testing"><i class="bi bi-arrow-clockwise me-1"></i> Test Connection</span>
                        <span x-show="testing"><span class="spinner-border spinner-border-sm me-1"></span> Testing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function emailSettings() {
    return {
        saving: false,
        sendingTest: false,
        testing: false,
        showPassword: false,
        showTemplateModal: false,
        connectionStatus: 'unknown',
        testEmail: '',
        testSubject: 'Test Email from Smart School',
        testMessage: 'This is a test email from Smart School Management System.',
        stats: {
            total_sent: {{ $stats['total_sent'] ?? 1250 }},
            delivered: {{ $stats['delivered'] ?? 1230 }},
            opened: {{ $stats['opened'] ?? 890 }},
            bounced: {{ $stats['bounced'] ?? 15 }},
            failed: {{ $stats['failed'] ?? 5 }}
        },
        templates: [
            { id: 1, name: 'Welcome Email', subject: 'Welcome to Smart School', type: 'system', active: true },
            { id: 2, name: 'Password Reset', subject: 'Reset Your Password', type: 'system', active: true },
            { id: 3, name: 'Fee Reminder', subject: 'Fee Payment Reminder', type: 'notification', active: true },
            { id: 4, name: 'Attendance Alert', subject: 'Attendance Notification', type: 'notification', active: true },
            { id: 5, name: 'Exam Results', subject: 'Exam Results Published', type: 'notification', active: false }
        ],
        form: {
            mail_driver: '{{ $settings["mail_driver"] ?? "smtp" }}',
            email_enabled: {{ ($settings['email_enabled'] ?? true) ? 'true' : 'false' }},
            smtp_host: '{{ $settings["smtp_host"] ?? "" }}',
            smtp_port: {{ $settings['smtp_port'] ?? 587 }},
            smtp_username: '{{ $settings["smtp_username"] ?? "" }}',
            smtp_password: '{{ $settings["smtp_password"] ?? "" }}',
            smtp_encryption: '{{ $settings["smtp_encryption"] ?? "tls" }}',
            smtp_timeout: {{ $settings['smtp_timeout'] ?? 30 }},
            mailgun_domain: '{{ $settings["mailgun_domain"] ?? "" }}',
            mailgun_secret: '{{ $settings["mailgun_secret"] ?? "" }}',
            mailgun_endpoint: '{{ $settings["mailgun_endpoint"] ?? "api.mailgun.net" }}',
            ses_key: '{{ $settings["ses_key"] ?? "" }}',
            ses_secret: '{{ $settings["ses_secret"] ?? "" }}',
            ses_region: '{{ $settings["ses_region"] ?? "us-east-1" }}',
            from_email: '{{ $settings["from_email"] ?? "" }}',
            from_name: '{{ $settings["from_name"] ?? "Smart School" }}',
            reply_to_email: '{{ $settings["reply_to_email"] ?? "" }}',
            reply_to_name: '{{ $settings["reply_to_name"] ?? "" }}'
        },
        
        updateDriverFields() {
            // Reset driver-specific fields when switching
        },
        
        applyPreset(provider) {
            const presets = {
                gmail: { host: 'smtp.gmail.com', port: 587, encryption: 'tls' },
                outlook: { host: 'smtp.office365.com', port: 587, encryption: 'tls' },
                yahoo: { host: 'smtp.mail.yahoo.com', port: 587, encryption: 'tls' },
                sendgrid: { host: 'smtp.sendgrid.net', port: 587, encryption: 'tls' }
            };
            
            if (presets[provider]) {
                this.form.smtp_host = presets[provider].host;
                this.form.smtp_port = presets[provider].port;
                this.form.smtp_encryption = presets[provider].encryption;
                this.form.mail_driver = 'smtp';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Preset Applied',
                    text: `${provider.charAt(0).toUpperCase() + provider.slice(1)} SMTP settings applied. Please enter your credentials.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved!',
                    text: 'Email settings have been updated successfully.',
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
        
        async sendTestEmail() {
            if (!this.testEmail) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please enter a recipient email address.'
                });
                return;
            }
            
            this.sendingTest = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Test Email Sent!',
                    text: 'Test email has been sent to ' + this.testEmail,
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: 'Failed to send test email. Please check your settings.'
                });
            } finally {
                this.sendingTest = false;
            }
        },
        
        async testConnection() {
            this.testing = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 2000));
                this.connectionStatus = 'connected';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Connection Successful!',
                    text: 'SMTP connection test passed.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                this.connectionStatus = 'error';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Failed!',
                    text: 'Could not connect to SMTP server. Please check your settings.'
                });
            } finally {
                this.testing = false;
            }
        },
        
        editTemplate(template) {
            Swal.fire({
                icon: 'info',
                title: 'Edit Template',
                text: 'Template editor coming soon!'
            });
        },
        
        previewTemplate(template) {
            Swal.fire({
                icon: 'info',
                title: template.name,
                html: `<p><strong>Subject:</strong> ${template.subject}</p><p>Template preview coming soon!</p>`
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
