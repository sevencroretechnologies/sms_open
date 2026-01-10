@extends('layouts.app')

@section('title', isset($template) ? 'Edit SMS Template' : 'Create SMS Template')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($template) ? 'Edit SMS Template' : 'Create SMS Template' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.templates') }}">SMS Templates</a></li>
                    <li class="breadcrumb-item active">{{ isset($template) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($template) ? route('admin.sms.templates.update', $template->id) : route('admin.sms.templates.store') }}">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-chat-text me-2"></i>Template Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" maxlength="160" required>{{ old('message', $template->message ?? '') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Max 160 characters per SMS</small>
                                <small class="text-muted"><span id="charCount">{{ strlen(old('message', $template->message ?? '')) }}</span>/160</small>
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-tag me-2"></i>Category
                    </div>
                    <div class="card-body">
                        <select name="category" class="form-select">
                            <option value="general" {{ old('category', $template->category ?? '') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="admission" {{ old('category', $template->category ?? '') == 'admission' ? 'selected' : '' }}>Admission</option>
                            <option value="fees" {{ old('category', $template->category ?? '') == 'fees' ? 'selected' : '' }}>Fees</option>
                            <option value="exam" {{ old('category', $template->category ?? '') == 'exam' ? 'selected' : '' }}>Exam</option>
                            <option value="attendance" {{ old('category', $template->category ?? '') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                            <option value="event" {{ old('category', $template->category ?? '') == 'event' ? 'selected' : '' }}>Event</option>
                        </select>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-code me-2"></i>Available Variables
                    </div>
                    <div class="card-body">
                        <small class="text-muted d-block mb-2">Click to copy:</small>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{name}')">{name}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{class}')">{class}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{date}')">{date}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{amount}')">{amount}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{school}')">{school}</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>Status
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.sms.templates') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($template) ? 'Update' : 'Save' }} Template
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

function copyVariable(variable) {
    navigator.clipboard.writeText(variable);
}
</script>
@endpush
@endsection
