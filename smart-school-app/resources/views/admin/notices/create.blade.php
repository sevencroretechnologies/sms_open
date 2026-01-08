{{-- Notices Create View --}}
{{-- Prompt 243: Notice creation form with targeting options --}}

@extends('layouts.app')

@section('title', 'Add Notice')

@section('content')
<div x-data="noticeForm()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Add Notice</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('notices.index') }}">Notices</a></li>
                    <li class="breadcrumb-item active">Add Notice</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('notices.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <form action="{{ route('notices.store') }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-megaphone me-2"></i>
                        Notice Information
                    </x-slot>

                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                id="title" 
                                name="title" 
                                value="{{ old('title') }}"
                                x-model="form.title"
                                required
                                placeholder="Enter notice title"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('content') is-invalid @enderror" 
                                id="content" 
                                name="content" 
                                rows="8"
                                x-model="form.content"
                                required
                                placeholder="Enter notice content..."
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can use basic HTML formatting for rich text content.</div>
                        </div>

                        <!-- Notice Date -->
                        <div class="col-md-6">
                            <label for="notice_date" class="form-label">Notice Date <span class="text-danger">*</span></label>
                            <input 
                                type="date" 
                                class="form-control @error('notice_date') is-invalid @enderror" 
                                id="notice_date" 
                                name="notice_date" 
                                value="{{ old('notice_date', date('Y-m-d')) }}"
                                x-model="form.notice_date"
                                required
                            >
                            @error('notice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input 
                                type="date" 
                                class="form-control @error('expiry_date') is-invalid @enderror" 
                                id="expiry_date" 
                                name="expiry_date" 
                                value="{{ old('expiry_date') }}"
                                x-model="form.expiry_date"
                            >
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty for no expiry</div>
                        </div>

                        <!-- Attachment -->
                        <div class="col-12">
                            <label for="attachment" class="form-label">Attachment</label>
                            <input 
                                type="file" 
                                class="form-control @error('attachment') is-invalid @enderror" 
                                id="attachment" 
                                name="attachment"
                                @change="handleFileChange"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                            >
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 5MB)</div>
                            
                            <!-- Attachment Preview -->
                            <div x-show="attachmentPreview" class="mt-2">
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                    <i class="bi bi-file-earmark fs-4"></i>
                                    <span x-text="attachmentName"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" @click="removeAttachment">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Targeting Options -->
                <x-card class="mt-4">
                    <x-slot name="header">
                        <i class="bi bi-bullseye me-2"></i>
                        Targeting Options
                    </x-slot>

                    <div class="row g-3">
                        <!-- Target Roles -->
                        <div class="col-md-6">
                            <label class="form-label">Target Roles</label>
                            <div class="border rounded p-3">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="allRoles" x-model="form.allRoles" @change="toggleAllRoles">
                                    <label class="form-check-label fw-medium" for="allRoles">All Roles</label>
                                </div>
                                <hr class="my-2">
                                @foreach($roles ?? [
                                    (object)['name' => 'admin', 'display_name' => 'Admin'],
                                    (object)['name' => 'teacher', 'display_name' => 'Teacher'],
                                    (object)['name' => 'student', 'display_name' => 'Student'],
                                    (object)['name' => 'parent', 'display_name' => 'Parent'],
                                    (object)['name' => 'accountant', 'display_name' => 'Accountant'],
                                    (object)['name' => 'librarian', 'display_name' => 'Librarian']
                                ] as $role)
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="role_{{ $role->name }}" 
                                            name="target_roles[]" 
                                            value="{{ $role->name }}"
                                            x-model="form.targetRoles"
                                            :disabled="form.allRoles"
                                        >
                                        <label class="form-check-label" for="role_{{ $role->name }}">{{ $role->display_name ?? ucfirst($role->name) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('target_roles')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Target Classes -->
                        <div class="col-md-6">
                            <label class="form-label">Target Classes</label>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="allClasses" x-model="form.allClasses" @change="toggleAllClasses">
                                    <label class="form-check-label fw-medium" for="allClasses">All Classes</label>
                                </div>
                                <hr class="my-2">
                                @foreach($classes ?? [] as $class)
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="class_{{ $class->id }}" 
                                            name="target_classes[]" 
                                            value="{{ $class->id }}"
                                            x-model="form.targetClasses"
                                            :disabled="form.allClasses"
                                        >
                                        <label class="form-check-label" for="class_{{ $class->id }}">{{ $class->name }}</label>
                                    </div>
                                @endforeach
                                @if(empty($classes ?? []))
                                    <p class="text-muted small mb-0">No classes available</p>
                                @endif
                            </div>
                            @error('target_classes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Publish Options -->
                <x-card class="mt-4">
                    <x-slot name="header">
                        <i class="bi bi-send me-2"></i>
                        Publish Options
                    </x-slot>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="is_published" 
                                    name="is_published" 
                                    value="1"
                                    x-model="form.isPublished"
                                    {{ old('is_published') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="is_published">Publish Immediately</label>
                            </div>
                            <div class="form-text">If unchecked, the notice will be saved as a draft.</div>
                        </div>

                        <div class="col-md-6" x-show="form.isPublished">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="send_notification" 
                                    name="send_notification" 
                                    value="1"
                                    x-model="form.sendNotification"
                                >
                                <label class="form-check-label" for="send_notification">Send Notification</label>
                            </div>
                            <div class="form-text">Send SMS/Email notification to targeted users.</div>
                        </div>
                    </div>
                </x-card>

                <!-- Form Actions -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">
                            <i class="bi bi-check-lg me-1"></i> 
                            <span x-text="form.isPublished ? 'Publish Notice' : 'Save as Draft'"></span>
                        </span>
                        <span x-show="isSubmitting">
                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                        </span>
                    </button>
                    <a href="{{ route('notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-eye me-2"></i>
                        Preview
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-2" style="width: 60px; height: 60px;">
                                <i class="bi bi-megaphone fs-3"></i>
                            </div>
                            <h6 class="mb-0" x-text="form.title || 'Notice Title'"></h6>
                            <small class="text-muted" x-text="form.notice_date || 'Notice Date'"></small>
                        </div>

                        <hr>

                        <div class="small">
                            <div class="mb-3">
                                <span class="text-muted d-block mb-1">Content Preview:</span>
                                <p class="mb-0" x-text="form.content ? form.content.substring(0, 150) + (form.content.length > 150 ? '...' : '') : 'Notice content will appear here...'"></p>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Expiry:</span>
                                <span x-text="form.expiry_date || 'No expiry'"></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Target Roles:</span>
                                <span x-text="form.allRoles ? 'All' : (form.targetRoles.length > 0 ? form.targetRoles.length + ' selected' : 'None')"></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Target Classes:</span>
                                <span x-text="form.allClasses ? 'All' : (form.targetClasses.length > 0 ? form.targetClasses.length + ' selected' : 'None')"></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Attachment:</span>
                                <span x-text="attachmentName || 'None'"></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status:</span>
                                <span>
                                    <span x-show="form.isPublished" class="badge bg-success">Published</span>
                                    <span x-show="!form.isPublished" class="badge bg-warning text-dark">Draft</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="bi bi-lightbulb me-2"></i>
                        Quick Tips
                    </div>
                    <div class="card-body small">
                        <ul class="mb-0 ps-3">
                            <li>Use a clear and descriptive title</li>
                            <li>Set an expiry date for time-sensitive notices</li>
                            <li>Target specific roles or classes for relevant notices</li>
                            <li>Attach supporting documents if needed</li>
                            <li>Save as draft to review before publishing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function noticeForm() {
    return {
        isSubmitting: false,
        attachmentPreview: false,
        attachmentName: '',
        form: {
            title: '{{ old('title') }}',
            content: `{{ old('content') }}`,
            notice_date: '{{ old('notice_date', date('Y-m-d')) }}',
            expiry_date: '{{ old('expiry_date') }}',
            allRoles: false,
            targetRoles: {!! json_encode(old('target_roles', [])) !!},
            allClasses: false,
            targetClasses: {!! json_encode(old('target_classes', [])) !!},
            isPublished: {{ old('is_published') ? 'true' : 'false' }},
            sendNotification: {{ old('send_notification') ? 'true' : 'false' }}
        },

        handleSubmit() {
            this.isSubmitting = true;
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.attachmentPreview = true;
                this.attachmentName = file.name;
            }
        },

        removeAttachment() {
            this.attachmentPreview = false;
            this.attachmentName = '';
            document.getElementById('attachment').value = '';
        },

        toggleAllRoles() {
            if (this.form.allRoles) {
                this.form.targetRoles = [];
            }
        },

        toggleAllClasses() {
            if (this.form.allClasses) {
                this.form.targetClasses = [];
            }
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .ms-auto {
    margin-left: 0 !important;
    margin-right: auto !important;
}

[dir="rtl"] .ps-3 {
    padding-left: 0 !important;
    padding-right: 1rem !important;
}
</style>
@endpush
