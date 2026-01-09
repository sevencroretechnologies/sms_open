{{-- Compose Email View --}}
{{-- Admin email composition form --}}

@extends('layouts.app')

@section('title', 'Compose Email')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Compose Email</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.emails') }}">Emails</a></li>
                    <li class="breadcrumb-item active">Compose</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.emails') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
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
    <form action="{{ route('admin.communication.send-email') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-envelope me-2"></i>
                        Email Details
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Recipient Type <span class="text-danger">*</span></label>
                            <select name="recipient_type" id="recipientType" class="form-select @error('recipient_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="all_students" {{ old('recipient_type') == 'all_students' ? 'selected' : '' }}>All Students</option>
                                <option value="all_parents" {{ old('recipient_type') == 'all_parents' ? 'selected' : '' }}>All Parents</option>
                                <option value="all_teachers" {{ old('recipient_type') == 'all_teachers' ? 'selected' : '' }}>All Teachers</option>
                                <option value="class" {{ old('recipient_type') == 'class' ? 'selected' : '' }}>By Class</option>
                                <option value="individual" {{ old('recipient_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            </select>
                            @error('recipient_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6" id="classSelect" style="display: none;">
                            <label class="form-label">Select Class</label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12" id="individualSelect" style="display: none;">
                            <label class="form-label">Select Recipients</label>
                            <select name="recipients[]" class="form-select @error('recipients') is-invalid @enderror" multiple>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('recipients', [])) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                            @error('recipients')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="10" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control @error('attachments') is-invalid @enderror" multiple>
                            <small class="text-muted">Max 5 files, 10MB each</small>
                            @error('attachments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.communication.emails') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                                <i class="bi bi-save me-1"></i> Save Draft
                            </button>
                            <button type="submit" name="action" value="send" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Send Email
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </div>

            <div class="col-lg-4">
                <!-- Templates -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Email Templates
                    </x-slot>
                    
                    <div class="list-group list-group-flush">
                        @forelse($templates ?? [] as $template)
                            <a href="#" class="list-group-item list-group-item-action" onclick="loadTemplate({{ $template->id }})">
                                <div class="fw-medium">{{ $template->name }}</div>
                                <small class="text-muted">{{ Str::limit($template->subject ?? '', 30) }}</small>
                            </a>
                        @empty
                            <p class="text-muted small mb-0">No templates available</p>
                        @endforelse
                    </div>
                    
                    <x-slot name="footer">
                        <a href="{{ route('admin.communication.email-template-create') }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-plus-lg me-1"></i> Create Template
                        </a>
                    </x-slot>
                </x-card>

                <!-- Tips -->
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-lightbulb me-2"></i>
                        Quick Tips
                    </x-slot>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use templates for common emails
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Preview before sending
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Attachments max 10MB each
                        </li>
                    </ul>
                </x-card>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('recipientType').addEventListener('change', function() {
    const classSelect = document.getElementById('classSelect');
    const individualSelect = document.getElementById('individualSelect');
    
    classSelect.style.display = this.value === 'class' ? 'block' : 'none';
    individualSelect.style.display = this.value === 'individual' ? 'block' : 'none';
});

function loadTemplate(id) {
    fetch('/admin/communication/email-templates/' + id)
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="subject"]').value = data.subject || '';
            document.querySelector('textarea[name="message"]').value = data.body || '';
        });
}
</script>
@endpush
