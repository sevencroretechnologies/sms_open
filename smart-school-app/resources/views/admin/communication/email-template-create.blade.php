{{-- Email Template Create View --}}
{{-- Admin email template creation form --}}

@extends('layouts.app')

@section('title', isset($template) ? 'Edit Email Template' : 'Create Email Template')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($template) ? 'Edit Email Template' : 'Create Email Template' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.emails') }}">Emails</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.email-templates') }}">Templates</a></li>
                    <li class="breadcrumb-item active">{{ isset($template) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.email-templates') }}" class="btn btn-outline-secondary">
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
    <form action="{{ isset($template) ? route('admin.communication.email-template-update', $template) : route('admin.communication.email-template-store') }}" method="POST">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif
        
        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Template Details
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name ?? '') }}" placeholder="e.g., Welcome Email" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $template->subject ?? '') }}" placeholder="e.g., Welcome to Our School" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Body <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="12" required>{{ old('body', $template->body ?? '') }}</textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.communication.email-templates') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> {{ isset($template) ? 'Update Template' : 'Create Template' }}
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </div>

            <div class="col-lg-4">
                <!-- Variables -->
                <x-card class="mb-4">
                    <x-slot name="header">
                        <i class="bi bi-code me-2"></i>
                        Available Variables
                    </x-slot>
                    
                    <p class="small text-muted mb-3">Click to copy. Use these in your template:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVariable('{name}')">
                            <code>{name}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVariable('{email}')">
                            <code>{email}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVariable('{school_name}')">
                            <code>{school_name}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVariable('{class}')">
                            <code>{class}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyVariable('{date}')">
                            <code>{date}</code>
                        </button>
                    </div>
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
                            Use variables for personalization
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Keep subject lines concise
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Preview before saving
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
function copyVariable(variable) {
    navigator.clipboard.writeText(variable);
    const textarea = document.querySelector('textarea[name="body"]');
    if (textarea) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + variable + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    }
}
</script>
@endpush
