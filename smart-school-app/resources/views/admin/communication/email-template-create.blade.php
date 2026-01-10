@extends('layouts.app')

@section('title', isset($template) ? 'Edit Email Template' : 'Create Email Template')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($template) ? 'Edit Email Template' : 'Create Email Template' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.emails.templates') }}">Email Templates</a></li>
                    <li class="breadcrumb-item active">{{ isset($template) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ isset($template) ? route('admin.emails.templates.update', $template->id) : route('admin.emails.templates.store') }}">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-file-text me-2"></i>Template Details
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
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $template->subject ?? '') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message Body <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="12" required>{{ old('body', $template->body ?? '') }}</textarea>
                            @error('body')
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
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{student_name}')">{student_name}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{parent_name}')">{parent_name}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{class_name}')">{class_name}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{school_name}')">{school_name}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{date}')">{date}</span>
                            <span class="badge bg-light text-dark cursor-pointer" onclick="copyVariable('{amount}')">{amount}</span>
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
            <a href="{{ route('admin.emails.templates') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ isset($template) ? 'Update' : 'Save' }} Template
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function copyVariable(variable) {
    navigator.clipboard.writeText(variable);
}
</script>
@endpush
@endsection
