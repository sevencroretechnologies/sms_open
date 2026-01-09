@extends('layouts.app')

@section('title', 'Compose Email')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Compose Email</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Emails</a></li>
                    <li class="breadcrumb-item active">Compose</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.emails.send') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-envelope me-2"></i>Email Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="10" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">Max 5 files, 10MB each. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-people me-2"></i>Recipients
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Send To <span class="text-danger">*</span></label>
                            <select name="recipient_type" class="form-select" required>
                                <option value="">Select Recipients</option>
                                <option value="all_students">All Students</option>
                                <option value="all_teachers">All Teachers</option>
                                <option value="all_parents">All Parents</option>
                                <option value="class">Specific Class</option>
                                <option value="individual">Individual</option>
                            </select>
                        </div>

                        <div class="mb-3" id="classSelect" style="display: none;">
                            <label class="form-label">Select Class</label>
                            <select name="class_id" class="form-select">
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" id="individualSelect" style="display: none;">
                            <label class="form-label">Email Addresses</label>
                            <textarea name="emails" class="form-control" rows="4" placeholder="Enter email addresses, one per line"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-file-text me-2"></i>Template
                    </div>
                    <div class="card-body">
                        <select name="template_id" class="form-select">
                            <option value="">Select Template (Optional)</option>
                            @foreach($templates ?? [] as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock me-2"></i>Schedule
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="schedule" id="schedule" value="1">
                            <label class="form-check-label" for="schedule">Schedule for later</label>
                        </div>
                        <div id="scheduleDateTime" style="display: none;">
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                <i class="bi bi-save me-1"></i> Save Draft
            </button>
            <button type="submit" name="action" value="send" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Send Email
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('select[name="recipient_type"]').addEventListener('change', function() {
    document.getElementById('classSelect').style.display = this.value === 'class' ? 'block' : 'none';
    document.getElementById('individualSelect').style.display = this.value === 'individual' ? 'block' : 'none';
});

document.getElementById('schedule').addEventListener('change', function() {
    document.getElementById('scheduleDateTime').style.display = this.checked ? 'block' : 'none';
});
</script>
@endpush
@endsection
