{{-- Messages Compose View --}}
{{-- Prompt 248: Compose message view with recipient selection --}}

@extends('layouts.app')

@section('title', isset($replyTo) ? 'Reply to Message' : 'Compose Message')

@section('content')
<div x-data="composeMessage()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($replyTo) ? 'Reply to Message' : 'Compose Message' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('messages.inbox') }}">Messages</a></li>
                    <li class="breadcrumb-item active">{{ isset($replyTo) ? 'Reply' : 'Compose' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary">
                <i class="bi bi-inbox me-1"></i> Inbox
            </a>
            <a href="{{ route('messages.sent') }}" class="btn btn-outline-secondary">
                <i class="bi bi-send me-1"></i> Sent
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
                @csrf
                
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-pencil-square me-2"></i>
                        {{ isset($replyTo) ? 'Reply' : 'New Message' }}
                    </x-slot>

                    <!-- Reply To Info -->
                    @if(isset($replyTo))
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-reply fs-4"></i>
                                <div>
                                    <strong>Replying to:</strong> {{ $replyTo->sender->name ?? 'Unknown' }}
                                    <br>
                                    <small class="text-muted">Subject: {{ $replyTo->subject ?? 'No Subject' }}</small>
                                    <input type="hidden" name="reply_to_id" value="{{ $replyTo->id }}">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recipients Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">
                            Recipients <span class="text-danger">*</span>
                        </label>
                        
                        <!-- Selection Mode -->
                        <div class="btn-group mb-3 w-100" role="group">
                            <input type="radio" class="btn-check" name="selection_mode" id="mode_individual" value="individual" x-model="selectionMode">
                            <label class="btn btn-outline-primary" for="mode_individual">
                                <i class="bi bi-person me-1"></i> Individual
                            </label>
                            <input type="radio" class="btn-check" name="selection_mode" id="mode_role" value="role" x-model="selectionMode">
                            <label class="btn btn-outline-primary" for="mode_role">
                                <i class="bi bi-people me-1"></i> By Role
                            </label>
                            <input type="radio" class="btn-check" name="selection_mode" id="mode_class" value="class" x-model="selectionMode">
                            <label class="btn btn-outline-primary" for="mode_class">
                                <i class="bi bi-mortarboard me-1"></i> By Class
                            </label>
                        </div>

                        <!-- Individual Selection -->
                        <div x-show="selectionMode === 'individual'" x-cloak>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Search users by name or email..."
                                    x-model="userSearch"
                                    @input.debounce.300ms="searchUsers()"
                                >
                            </div>
                            
                            <!-- Search Results -->
                            <div class="border rounded mb-2" x-show="searchResults.length > 0" style="max-height: 200px; overflow-y: auto;">
                                <template x-for="user in searchResults" :key="user.id">
                                    <div 
                                        class="p-2 border-bottom d-flex align-items-center justify-content-between cursor-pointer hover-bg-light"
                                        @click="addRecipient(user)"
                                    >
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle avatar-sm bg-primary bg-opacity-10 text-primary">
                                                <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div>
                                                <span x-text="user.name"></span>
                                                <br>
                                                <small class="text-muted" x-text="user.email"></small>
                                            </div>
                                        </div>
                                        <span class="badge bg-info" x-text="user.role"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Selected Recipients -->
                            <div class="d-flex flex-wrap gap-2" x-show="selectedRecipients.length > 0">
                                <template x-for="recipient in selectedRecipients" :key="recipient.id">
                                    <span class="badge bg-primary d-flex align-items-center gap-1">
                                        <span x-text="recipient.name"></span>
                                        <button type="button" class="btn-close btn-close-white btn-sm" @click="removeRecipient(recipient.id)"></button>
                                        <input type="hidden" name="recipients[]" :value="recipient.id">
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div x-show="selectionMode === 'role'" x-cloak>
                            <div class="row g-2">
                                @foreach(['admin' => 'Administrators', 'teacher' => 'Teachers', 'student' => 'Students', 'parent' => 'Parents', 'accountant' => 'Accountants', 'librarian' => 'Librarians'] as $role => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input" 
                                                id="role_{{ $role }}" 
                                                name="roles[]" 
                                                value="{{ $role }}"
                                                x-model="selectedRoles"
                                            >
                                            <label class="form-check-label" for="role_{{ $role }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="selectAllRoles()">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="selectedRoles = []">Clear</button>
                            </div>
                        </div>

                        <!-- Class Selection -->
                        <div x-show="selectionMode === 'class'" x-cloak>
                            <div class="row g-2">
                                @foreach($classes ?? [] as $class)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input" 
                                                id="class_{{ $class->id }}" 
                                                name="classes[]" 
                                                value="{{ $class->id }}"
                                                x-model="selectedClasses"
                                            >
                                            <label class="form-check-label" for="class_{{ $class->id }}">{{ $class->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="selectAllClasses()">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="selectedClasses = []">Clear</button>
                            </div>
                            <div class="mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="include_parents" name="include_parents" x-model="includeParents">
                                    <label class="form-check-label" for="include_parents">Also send to parents of selected classes</label>
                                </div>
                            </div>
                        </div>

                        @error('recipients')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject" class="form-label fw-medium">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('subject') is-invalid @enderror" 
                            id="subject" 
                            name="subject" 
                            value="{{ old('subject', isset($replyTo) ? 'Re: ' . ($replyTo->subject ?? '') : '') }}"
                            x-model="subject"
                            required
                        >
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Message Body -->
                    <div class="mb-4">
                        <label for="body" class="form-label fw-medium">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            class="form-control @error('body') is-invalid @enderror" 
                            id="body" 
                            name="body" 
                            rows="8"
                            x-model="body"
                            required
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">You can use basic formatting</small>
                            <small class="text-muted"><span x-text="body.length"></span> characters</small>
                        </div>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-4">
                        <label for="attachment" class="form-label fw-medium">Attachment</label>
                        <input 
                            type="file" 
                            class="form-control @error('attachment') is-invalid @enderror" 
                            id="attachment" 
                            name="attachment"
                            @change="handleFileChange"
                        >
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max file size: 10MB. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</small>
                        
                        <!-- File Preview -->
                        <div x-show="attachmentPreview" class="mt-2 p-2 bg-light rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-paperclip"></i>
                                <span x-text="attachmentName"></span>
                                <small class="text-muted" x-text="attachmentSize"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="removeAttachment()">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Priority</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="priority" id="priority_low" value="low" x-model="priority">
                            <label class="btn btn-outline-secondary" for="priority_low">
                                <i class="bi bi-arrow-down me-1"></i> Low
                            </label>
                            <input type="radio" class="btn-check" name="priority" id="priority_normal" value="normal" x-model="priority" checked>
                            <label class="btn btn-outline-secondary" for="priority_normal">
                                <i class="bi bi-dash me-1"></i> Normal
                            </label>
                            <input type="radio" class="btn-check" name="priority" id="priority_high" value="high" x-model="priority">
                            <label class="btn btn-outline-danger" for="priority_high">
                                <i class="bi bi-exclamation-triangle me-1"></i> High
                            </label>
                        </div>
                    </div>

                    <!-- Send Options -->
                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="send_email" name="send_email" x-model="sendEmail">
                            <label class="form-check-label" for="send_email">
                                <i class="bi bi-envelope me-1"></i> Also send as email notification
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="send_sms" name="send_sms" x-model="sendSms">
                            <label class="form-check-label" for="send_sms">
                                <i class="bi bi-chat-dots me-1"></i> Also send SMS notification
                            </label>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary" @click="saveDraft()">
                                <i class="bi bi-file-earmark me-1"></i> Save Draft
                            </button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('messages.inbox') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                    <span x-show="!isSubmitting">
                                        <i class="bi bi-send me-1"></i> Send Message
                                    </span>
                                    <span x-show="isSubmitting">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Sending...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </x-slot>
                </x-card>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Message Preview -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-eye me-2"></i>
                    Preview
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">To:</small>
                        <div x-show="selectionMode === 'individual' && selectedRecipients.length > 0">
                            <template x-for="r in selectedRecipients.slice(0, 3)" :key="r.id">
                                <span class="badge bg-secondary me-1" x-text="r.name"></span>
                            </template>
                            <span x-show="selectedRecipients.length > 3" class="text-muted small" x-text="'+' + (selectedRecipients.length - 3) + ' more'"></span>
                        </div>
                        <div x-show="selectionMode === 'role' && selectedRoles.length > 0">
                            <template x-for="role in selectedRoles" :key="role">
                                <span class="badge bg-info me-1" x-text="role"></span>
                            </template>
                        </div>
                        <div x-show="selectionMode === 'class' && selectedClasses.length > 0">
                            <span class="text-muted" x-text="selectedClasses.length + ' class(es) selected'"></span>
                        </div>
                        <div x-show="(selectionMode === 'individual' && selectedRecipients.length === 0) || (selectionMode === 'role' && selectedRoles.length === 0) || (selectionMode === 'class' && selectedClasses.length === 0)">
                            <span class="text-muted">No recipients selected</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Subject:</small>
                        <p class="mb-0 fw-medium" x-text="subject || 'No subject'"></p>
                    </div>
                    <div>
                        <small class="text-muted">Message:</small>
                        <p class="mb-0 text-break" x-text="body ? body.substring(0, 200) + (body.length > 200 ? '...' : '') : 'No message content'"></p>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-lightbulb me-2"></i>
                    Quick Tips
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Use clear and concise subject lines
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Select appropriate recipients to avoid spam
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Use high priority only for urgent messages
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Attachments are limited to 10MB
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Save drafts to continue later
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Drafts -->
            @if(isset($drafts) && count($drafts) > 0)
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark me-2"></i>
                    Recent Drafts
                </div>
                <div class="list-group list-group-flush">
                    @foreach($drafts as $draft)
                        <a href="{{ route('messages.compose', ['draft' => $draft->id]) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <span>{{ Str::limit($draft->subject ?? 'No Subject', 25) }}</span>
                                <small class="text-muted">{{ $draft->updated_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function composeMessage() {
    return {
        selectionMode: 'individual',
        userSearch: '',
        searchResults: [],
        selectedRecipients: @json(isset($replyTo) ? [['id' => $replyTo->sender->id ?? 0, 'name' => $replyTo->sender->name ?? 'Unknown', 'email' => $replyTo->sender->email ?? '']] : []),
        selectedRoles: [],
        selectedClasses: [],
        includeParents: false,
        subject: '{{ old('subject', isset($replyTo) ? 'Re: ' . addslashes($replyTo->subject ?? '') : '') }}',
        body: '{{ old('body', '') }}',
        priority: 'normal',
        sendEmail: false,
        sendSms: false,
        attachmentPreview: false,
        attachmentName: '',
        attachmentSize: '',
        isSubmitting: false,

        searchUsers() {
            if (this.userSearch.length < 2) {
                this.searchResults = [];
                return;
            }
            
            // Simulated search results - in production, this would be an API call
            this.searchResults = [
                { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Teacher' },
                { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'Student' },
                { id: 3, name: 'Bob Wilson', email: 'bob@example.com', role: 'Parent' }
            ].filter(u => 
                u.name.toLowerCase().includes(this.userSearch.toLowerCase()) ||
                u.email.toLowerCase().includes(this.userSearch.toLowerCase())
            );
        },

        addRecipient(user) {
            if (!this.selectedRecipients.find(r => r.id === user.id)) {
                this.selectedRecipients.push(user);
            }
            this.userSearch = '';
            this.searchResults = [];
        },

        removeRecipient(id) {
            this.selectedRecipients = this.selectedRecipients.filter(r => r.id !== id);
        },

        selectAllRoles() {
            this.selectedRoles = ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian'];
        },

        selectAllClasses() {
            this.selectedClasses = @json(collect($classes ?? [])->pluck('id')->toArray());
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.attachmentPreview = true;
                this.attachmentName = file.name;
                this.attachmentSize = this.formatFileSize(file.size);
            }
        },

        removeAttachment() {
            document.getElementById('attachment').value = '';
            this.attachmentPreview = false;
            this.attachmentName = '';
            this.attachmentSize = '';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        saveDraft() {
            const form = document.querySelector('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'save_draft';
            input.value = '1';
            form.appendChild(input);
            form.submit();
        },

        handleSubmit(event) {
            // Validate recipients
            if (this.selectionMode === 'individual' && this.selectedRecipients.length === 0) {
                event.preventDefault();
                alert('Please select at least one recipient');
                return false;
            }
            if (this.selectionMode === 'role' && this.selectedRoles.length === 0) {
                event.preventDefault();
                alert('Please select at least one role');
                return false;
            }
            if (this.selectionMode === 'class' && this.selectedClasses.length === 0) {
                event.preventDefault();
                alert('Please select at least one class');
                return false;
            }

            this.isSubmitting = true;
            return true;
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.avatar-circle.avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 0.7rem;
}

.cursor-pointer {
    cursor: pointer;
}

.hover-bg-light:hover {
    background-color: #f8f9fa;
}

[x-cloak] {
    display: none !important;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .ms-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}
</style>
@endpush
