{{-- SMS Template Create View --}}
{{-- Admin SMS template creation form --}}

@extends('layouts.app')

@section('title', isset($template) ? 'Edit SMS Template' : 'Create SMS Template')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($template) ? 'Edit SMS Template' : 'Create SMS Template' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.sms') }}">SMS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.sms-templates') }}">Templates</a></li>
                    <li class="breadcrumb-item active">{{ isset($template) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.sms-templates') }}" class="btn btn-outline-secondary">
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
    <form action="{{ isset($template) ? route('admin.communication.sms-template-update', $template) : route('admin.communication.sms-template-store') }}" method="POST">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif
        
        <div class="row">
            <div class="col-lg-8">
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-chat-dots me-2"></i>
                        Template Details
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name ?? '') }}" placeholder="e.g., Fee Reminder" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="smsMessage" class="form-control @error('message') is-invalid @enderror" rows="6" maxlength="160" required>{{ old('message', $template->message ?? '') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Max 160 characters per SMS</small>
                                <small class="text-muted"><span id="charCount">{{ strlen(old('message', $template->message ?? '')) }}</span>/160</small>
                            </div>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.communication.sms-templates') }}" class="btn btn-outline-secondary">
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
                    
                    <p class="small text-muted mb-3">Click to insert. Use these in your template:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('{name}')">
                            <code>{name}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('{class}')">
                            <code>{class}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('{date}')">
                            <code>{date}</code>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('{amount}')">
                            <code>{amount}</code>
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
                            Keep under 160 characters
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use variables for personalization
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Be clear and concise
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
document.getElementById('smsMessage').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

function insertVariable(variable) {
    const textarea = document.getElementById('smsMessage');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    if (text.length + variable.length <= 160) {
        textarea.value = text.substring(0, start) + variable + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
        document.getElementById('charCount').textContent = textarea.value.length;
    }
}
</script>
@endpush
