@extends('layouts.app')

@section('title', 'Send SMS')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Send SMS</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sms.index') }}">SMS</a></li>
                    <li class="breadcrumb-item active">Send</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.sms.send') }}">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-chat-dots me-2"></i>Message
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" maxlength="160" required>{{ old('message') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Max 160 characters per SMS</small>
                                <small class="text-muted"><span id="charCount">0</span>/160</small>
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
                                <option value="individual">Individual Numbers</option>
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
                            <label class="form-label">Phone Numbers</label>
                            <textarea name="phone_numbers" class="form-control" rows="4" placeholder="Enter phone numbers, one per line"></textarea>
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
                        <i class="bi bi-info-circle me-2"></i>SMS Credits
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Available Credits:</span>
                            <strong>0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Estimated Cost:</span>
                            <strong id="estimatedCost">0 credits</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.sms.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Send SMS
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

document.querySelector('select[name="recipient_type"]').addEventListener('change', function() {
    document.getElementById('classSelect').style.display = this.value === 'class' ? 'block' : 'none';
    document.getElementById('individualSelect').style.display = this.value === 'individual' ? 'block' : 'none';
});
</script>
@endpush
@endsection
