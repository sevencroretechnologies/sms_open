{{-- General Settings View --}}
{{-- Prompt 272: School information, contact details, academic year settings, logo upload --}}

@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
<div x-data="generalSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">General Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">General</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="resetForm()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data" @submit.prevent="saveSettings()">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- School Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>School Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('school_name') is-invalid @enderror" 
                                       name="school_name" x-model="form.school_name" required>
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School Code</label>
                                <input type="text" class="form-control @error('school_code') is-invalid @enderror" 
                                       name="school_code" x-model="form.school_code">
                                @error('school_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">School Tagline</label>
                                <input type="text" class="form-control @error('school_tagline') is-invalid @enderror" 
                                       name="school_tagline" x-model="form.school_tagline" 
                                       placeholder="e.g., Excellence in Education">
                                @error('school_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">School Address</label>
                                <textarea class="form-control @error('school_address') is-invalid @enderror" 
                                          name="school_address" x-model="form.school_address" rows="2"></textarea>
                                @error('school_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       name="city" x-model="form.city">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       name="state" x-model="form.state">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <select class="form-select @error('country') is-invalid @enderror" 
                                        name="country" x-model="form.country">
                                    <option value="">Select Country</option>
                                    <option value="IN">India</option>
                                    <option value="US">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="SG">Singapore</option>
                                    <option value="MY">Malaysia</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       name="postal_code" x-model="form.postal_code">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" x-model="form.phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" x-model="form.email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       name="website" x-model="form.website" placeholder="https://">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-calendar-event me-2 text-success"></i>Academic Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Academic Session <span class="text-danger">*</span></label>
                                <select class="form-select @error('current_session_id') is-invalid @enderror" 
                                        name="current_session_id" x-model="form.current_session_id" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicSessions ?? [] as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                    <option value="1">2025-2026</option>
                                    <option value="2">2024-2025</option>
                                </select>
                                @error('current_session_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Working Days</label>
                                <input type="number" class="form-control @error('working_days') is-invalid @enderror" 
                                       name="working_days" x-model="form.working_days" min="1" max="365">
                                @error('working_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Academic Year Start Date</label>
                                <input type="date" class="form-control @error('academic_start_date') is-invalid @enderror" 
                                       name="academic_start_date" x-model="form.academic_start_date">
                                @error('academic_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Academic Year End Date</label>
                                <input type="date" class="form-control @error('academic_end_date') is-invalid @enderror" 
                                       name="academic_end_date" x-model="form.academic_end_date">
                                @error('academic_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-clock me-2 text-warning"></i>Time Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">School Start Time</label>
                                <input type="time" class="form-control @error('school_start_time') is-invalid @enderror" 
                                       name="school_start_time" x-model="form.school_start_time">
                                @error('school_start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">School End Time</label>
                                <input type="time" class="form-control @error('school_end_time') is-invalid @enderror" 
                                       name="school_end_time" x-model="form.school_end_time">
                                @error('school_end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Time Zone</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" 
                                        name="timezone" x-model="form.timezone">
                                    <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                                    <option value="America/New_York">America/New_York (EST)</option>
                                    <option value="America/Los_Angeles">America/Los_Angeles (PST)</option>
                                    <option value="Europe/London">Europe/London (GMT)</option>
                                    <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                                    <option value="Asia/Singapore">Asia/Singapore (SGT)</option>
                                    <option value="Australia/Sydney">Australia/Sydney (AEST)</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2 text-info"></i>Contact Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Principal Information</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Principal Name</label>
                                <input type="text" class="form-control @error('principal_name') is-invalid @enderror" 
                                       name="principal_name" x-model="form.principal_name">
                                @error('principal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Principal Email</label>
                                <input type="email" class="form-control @error('principal_email') is-invalid @enderror" 
                                       name="principal_email" x-model="form.principal_email">
                                @error('principal_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Principal Phone</label>
                                <input type="tel" class="form-control @error('principal_phone') is-invalid @enderror" 
                                       name="principal_phone" x-model="form.principal_phone">
                                @error('principal_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-4">
                                <h6 class="text-muted mb-3">Admin Information</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admin Name</label>
                                <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                       name="admin_name" x-model="form.admin_name">
                                @error('admin_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admin Email</label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                       name="admin_email" x-model="form.admin_email">
                                @error('admin_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admin Phone</label>
                                <input type="tel" class="form-control @error('admin_phone') is-invalid @enderror" 
                                       name="admin_phone" x-model="form.admin_phone">
                                @error('admin_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo Upload Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-image me-2 text-primary"></i>School Logo</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="logo-preview mx-auto mb-3" 
                                 style="width: 150px; height: 150px; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="Logo Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </template>
                                <template x-if="!logoPreview">
                                    <div class="text-muted">
                                        <i class="bi bi-building fs-1 d-block mb-2"></i>
                                        <small>No logo uploaded</small>
                                    </div>
                                </template>
                            </div>
                            <input type="file" class="form-control @error('school_logo') is-invalid @enderror" 
                                   name="school_logo" accept="image/*" @change="previewLogo($event)">
                            @error('school_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Recommended: 200x200px, PNG or JPG</small>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeLogo()" x-show="logoPreview">
                            <i class="bi bi-trash me-1"></i> Remove Logo
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('settings.sms') ?? '#' }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-chat-dots me-2"></i> SMS Settings
                            </a>
                            <a href="{{ route('settings.email') ?? '#' }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-envelope me-2"></i> Email Settings
                            </a>
                            <a href="{{ route('settings.payment') ?? '#' }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-credit-card me-2"></i> Payment Settings
                            </a>
                            <a href="{{ route('settings.theme') ?? '#' }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-palette me-2"></i> Theme Settings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Settings Info -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Information</h6>
                        <p class="small text-muted mb-2">
                            <strong>Last Updated:</strong><br>
                            <span x-text="lastUpdated">Never</span>
                        </p>
                        <p class="small text-muted mb-0">
                            <strong>Updated By:</strong><br>
                            <span x-text="updatedBy">-</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function generalSettings() {
    return {
        saving: false,
        logoPreview: '{{ $settings["school_logo"] ?? "" }}',
        lastUpdated: '{{ $settings["updated_at"] ?? "Never" }}',
        updatedBy: '{{ $settings["updated_by"] ?? "-" }}',
        form: {
            school_name: '{{ $settings["school_name"] ?? "Smart School" }}',
            school_code: '{{ $settings["school_code"] ?? "" }}',
            school_tagline: '{{ $settings["school_tagline"] ?? "" }}',
            school_address: '{{ $settings["school_address"] ?? "" }}',
            city: '{{ $settings["city"] ?? "" }}',
            state: '{{ $settings["state"] ?? "" }}',
            country: '{{ $settings["country"] ?? "IN" }}',
            postal_code: '{{ $settings["postal_code"] ?? "" }}',
            phone: '{{ $settings["phone"] ?? "" }}',
            email: '{{ $settings["email"] ?? "" }}',
            website: '{{ $settings["website"] ?? "" }}',
            current_session_id: '{{ $settings["current_session_id"] ?? "1" }}',
            working_days: '{{ $settings["working_days"] ?? "220" }}',
            academic_start_date: '{{ $settings["academic_start_date"] ?? "" }}',
            academic_end_date: '{{ $settings["academic_end_date"] ?? "" }}',
            school_start_time: '{{ $settings["school_start_time"] ?? "08:00" }}',
            school_end_time: '{{ $settings["school_end_time"] ?? "15:00" }}',
            timezone: '{{ $settings["timezone"] ?? "Asia/Kolkata" }}',
            principal_name: '{{ $settings["principal_name"] ?? "" }}',
            principal_email: '{{ $settings["principal_email"] ?? "" }}',
            principal_phone: '{{ $settings["principal_phone"] ?? "" }}',
            admin_name: '{{ $settings["admin_name"] ?? "" }}',
            admin_email: '{{ $settings["admin_email"] ?? "" }}',
            admin_phone: '{{ $settings["admin_phone"] ?? "" }}'
        },
        
        previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeLogo() {
            this.logoPreview = '';
            const fileInput = document.querySelector('input[name="school_logo"]');
            if (fileInput) fileInput.value = '';
        },
        
        resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                window.location.reload();
            }
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                const formData = new FormData(document.querySelector('form'));
                
                const response = await fetch('{{ route("settings.general.update") ?? "#" }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Settings Saved!',
                        text: 'General settings have been updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    this.lastUpdated = new Date().toLocaleString();
                } else {
                    throw new Error('Failed to save settings');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save settings. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.logo-preview {
    background-color: #f8f9fa;
    transition: border-color 0.2s ease;
}

.logo-preview:hover {
    border-color: #4f46e5 !important;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
