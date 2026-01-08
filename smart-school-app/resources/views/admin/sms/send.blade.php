{{-- SMS Send View --}}
{{-- Prompt 251: Send SMS view with recipient selection and templates --}}

@extends('layouts.app')

@section('title', 'Send SMS')

@section('content')
<div x-data="smsSender()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Send SMS</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sms.logs') }}">SMS</a></li>
                    <li class="breadcrumb-item active">Send</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('sms.logs') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i> SMS Logs
            </a>
            <a href="{{ route('sms.templates') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-text me-1"></i> Templates
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

    <!-- SMS Credits Info -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle fs-4 me-3"></i>
        <div>
            <strong>SMS Credits Available:</strong> {{ $smsCredits ?? 'N/A' }}
            <span class="mx-2">|</span>
            <strong>Cost per SMS:</strong> 1 credit
            <span class="mx-2">|</span>
            <span x-show="estimatedCredits > 0">
                <strong>Estimated Cost:</strong> <span x-text="estimatedCredits"></span> credits
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form action="{{ route('sms.store') }}" method="POST" @submit="handleSubmit">
                @csrf
                
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-chat-dots me-2"></i>
                        Compose SMS
                    </x-slot>

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
                            <input type="radio" class="btn-check" name="selection_mode" id="mode_manual" value="manual" x-model="selectionMode">
                            <label class="btn btn-outline-primary" for="mode_manual">
                                <i class="bi bi-telephone me-1"></i> Manual
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
                                    placeholder="Search users by name or phone..."
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
                                                <small class="text-muted font-monospace" x-text="user.phone"></small>
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
                                        <small class="opacity-75" x-text="'(' + recipient.phone + ')'"></small>
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
                                                @change="updateEstimatedCredits()"
                                            >
                                            <label class="form-check-label" for="role_{{ $role }}">
                                                {{ $label }}
                                                <small class="text-muted">({{ $roleCounts[$role] ?? 0 }})</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="selectAllRoles()">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="selectedRoles = []; updateEstimatedCredits()">Clear</button>
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
                                                @change="updateEstimatedCredits()"
                                            >
                                            <label class="form-check-label" for="class_{{ $class->id }}">
                                                {{ $class->name }}
                                                <small class="text-muted">({{ $class->students_count ?? 0 }})</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="selectAllClasses()">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="selectedClasses = []; updateEstimatedCredits()">Clear</button>
                            </div>
                            <div class="mt-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="include_parents" name="include_parents" x-model="includeParents" @change="updateEstimatedCredits()">
                                    <label class="form-check-label" for="include_parents">Also send to parents of selected classes</label>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Phone Entry -->
                        <div x-show="selectionMode === 'manual'" x-cloak>
                            <textarea 
                                class="form-control" 
                                name="manual_phones" 
                                rows="4" 
                                placeholder="Enter phone numbers (one per line or comma-separated)&#10;Example:&#10;+1234567890&#10;+0987654321"
                                x-model="manualPhones"
                                @input="updateEstimatedCredits()"
                            ></textarea>
                            <small class="text-muted">
                                <span x-text="getManualPhoneCount()"></span> phone number(s) entered
                            </small>
                        </div>

                        @error('recipients')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SMS Type -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">SMS Type</label>
                        <select class="form-select" name="type" x-model="smsType">
                            <option value="general">General</option>
                            <option value="notice">Notice</option>
                            <option value="attendance">Attendance Alert</option>
                            <option value="fee">Fee Reminder</option>
                            <option value="exam">Exam Notification</option>
                        </select>
                    </div>

                    <!-- Template Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Use Template</label>
                        <select class="form-select" x-model="selectedTemplate" @change="applyTemplate()">
                            <option value="">-- Select Template --</option>
                            @foreach($templates ?? [] as $template)
                                <option value="{{ $template->id }}" data-content="{{ $template->content }}">
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label for="message" class="form-label fw-medium">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            class="form-control @error('message') is-invalid @enderror" 
                            id="message" 
                            name="message" 
                            rows="5"
                            x-model="message"
                            maxlength="160"
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">
                                <span :class="message.length > 160 ? 'text-danger' : ''">
                                    <span x-text="message.length"></span>/160 characters
                                </span>
                                <span x-show="message.length > 160" class="text-danger">
                                    (Will use <span x-text="Math.ceil(message.length / 160)"></span> SMS credits per recipient)
                                </span>
                            </small>
                            <small class="text-muted">
                                Variables: {name}, {class}, {date}
                            </small>
                        </div>
                    </div>

                    <!-- Schedule Options -->
                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="schedule_sms" x-model="scheduleSms">
                            <label class="form-check-label" for="schedule_sms">
                                <i class="bi bi-clock me-1"></i> Schedule for later
                            </label>
                        </div>
                        <div x-show="scheduleSms" x-cloak class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label class="form-label small">Date</label>
                                <input type="date" class="form-control" name="schedule_date" x-model="scheduleDate" :required="scheduleSms">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Time</label>
                                <input type="time" class="form-control" name="schedule_time" x-model="scheduleTime" :required="scheduleSms">
                            </div>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">
                                    Estimated: <strong x-text="estimatedCredits"></strong> credits
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" @click="previewSms()">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </button>
                                <a href="{{ route('sms.logs') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" :disabled="isSubmitting || !canSend()">
                                    <span x-show="!isSubmitting">
                                        <i class="bi bi-send me-1"></i> 
                                        <span x-text="scheduleSms ? 'Schedule SMS' : 'Send SMS'"></span>
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
            <!-- Preview Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-phone me-2"></i>
                    SMS Preview
                </div>
                <div class="card-body">
                    <div class="sms-preview bg-light rounded p-3">
                        <div class="sms-bubble bg-primary text-white rounded p-3">
                            <p class="mb-0 small" x-text="message || 'Your message will appear here...'"></p>
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted" x-text="new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Summary -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>
                    Recipients Summary
                </div>
                <div class="card-body">
                    <div x-show="selectionMode === 'individual'">
                        <p class="mb-0">
                            <strong x-text="selectedRecipients.length"></strong> recipient(s) selected
                        </p>
                    </div>
                    <div x-show="selectionMode === 'role'">
                        <p class="mb-0">
                            <strong x-text="selectedRoles.length"></strong> role(s) selected
                        </p>
                        <template x-for="role in selectedRoles" :key="role">
                            <span class="badge bg-info me-1" x-text="role"></span>
                        </template>
                    </div>
                    <div x-show="selectionMode === 'class'">
                        <p class="mb-0">
                            <strong x-text="selectedClasses.length"></strong> class(es) selected
                            <span x-show="includeParents" class="text-muted">(+ parents)</span>
                        </p>
                    </div>
                    <div x-show="selectionMode === 'manual'">
                        <p class="mb-0">
                            <strong x-text="getManualPhoneCount()"></strong> phone number(s)
                        </p>
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
                            Keep messages under 160 characters
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Use templates for common messages
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Schedule SMS for optimal delivery time
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Use variables like {name} for personalization
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Check SMS credits before bulk sending
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Templates -->
            @if(isset($templates) && count($templates) > 0)
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-text me-2"></i>
                    Quick Templates
                </div>
                <div class="list-group list-group-flush">
                    @foreach($templates->take(5) as $template)
                        <a href="#" class="list-group-item list-group-item-action" @click.prevent="message = '{{ addslashes($template->content) }}'">
                            <div class="d-flex justify-content-between">
                                <span>{{ $template->name }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" x-ref="previewModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>
                        SMS Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-muted small">Recipients:</label>
                        <p class="mb-0" x-text="getRecipientsPreview()"></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Message:</label>
                        <div class="bg-light rounded p-3">
                            <p class="mb-0" x-text="message"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Character Count:</label>
                        <p class="mb-0" x-text="message.length + ' / 160'"></p>
                    </div>
                    <div>
                        <label class="text-muted small">Estimated Credits:</label>
                        <p class="mb-0" x-text="estimatedCredits"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function smsSender() {
    return {
        selectionMode: 'individual',
        userSearch: '',
        searchResults: [],
        selectedRecipients: [],
        selectedRoles: [],
        selectedClasses: [],
        includeParents: false,
        manualPhones: '',
        smsType: 'general',
        selectedTemplate: '',
        message: '',
        scheduleSms: false,
        scheduleDate: '',
        scheduleTime: '',
        isSubmitting: false,
        estimatedCredits: 0,

        // Role counts for estimation
        roleCounts: @json($roleCounts ?? []),
        classCounts: @json(collect($classes ?? [])->pluck('students_count', 'id')->toArray()),

        searchUsers() {
            if (this.userSearch.length < 2) {
                this.searchResults = [];
                return;
            }
            
            // Simulated search results - in production, this would be an API call
            this.searchResults = [
                { id: 1, name: 'John Doe', phone: '+1234567890', role: 'Teacher' },
                { id: 2, name: 'Jane Smith', phone: '+0987654321', role: 'Student' },
                { id: 3, name: 'Bob Wilson', phone: '+1122334455', role: 'Parent' }
            ].filter(u => 
                u.name.toLowerCase().includes(this.userSearch.toLowerCase()) ||
                u.phone.includes(this.userSearch)
            );
        },

        addRecipient(user) {
            if (!this.selectedRecipients.find(r => r.id === user.id)) {
                this.selectedRecipients.push(user);
                this.updateEstimatedCredits();
            }
            this.userSearch = '';
            this.searchResults = [];
        },

        removeRecipient(id) {
            this.selectedRecipients = this.selectedRecipients.filter(r => r.id !== id);
            this.updateEstimatedCredits();
        },

        selectAllRoles() {
            this.selectedRoles = ['admin', 'teacher', 'student', 'parent', 'accountant', 'librarian'];
            this.updateEstimatedCredits();
        },

        selectAllClasses() {
            this.selectedClasses = @json(collect($classes ?? [])->pluck('id')->toArray());
            this.updateEstimatedCredits();
        },

        getManualPhoneCount() {
            if (!this.manualPhones.trim()) return 0;
            const phones = this.manualPhones.split(/[\n,]+/).filter(p => p.trim());
            return phones.length;
        },

        updateEstimatedCredits() {
            let count = 0;
            const smsCount = Math.ceil(Math.max(this.message.length, 1) / 160);

            if (this.selectionMode === 'individual') {
                count = this.selectedRecipients.length;
            } else if (this.selectionMode === 'role') {
                this.selectedRoles.forEach(role => {
                    count += this.roleCounts[role] || 0;
                });
            } else if (this.selectionMode === 'class') {
                this.selectedClasses.forEach(classId => {
                    count += this.classCounts[classId] || 0;
                });
                if (this.includeParents) {
                    count *= 2; // Approximate: assume each student has one parent
                }
            } else if (this.selectionMode === 'manual') {
                count = this.getManualPhoneCount();
            }

            this.estimatedCredits = count * smsCount;
        },

        applyTemplate() {
            if (!this.selectedTemplate) return;
            const select = document.querySelector('select[x-model="selectedTemplate"]');
            const option = select.options[select.selectedIndex];
            const content = option.dataset.content;
            if (content) {
                this.message = content;
                this.updateEstimatedCredits();
            }
        },

        canSend() {
            if (!this.message.trim()) return false;
            
            if (this.selectionMode === 'individual' && this.selectedRecipients.length === 0) return false;
            if (this.selectionMode === 'role' && this.selectedRoles.length === 0) return false;
            if (this.selectionMode === 'class' && this.selectedClasses.length === 0) return false;
            if (this.selectionMode === 'manual' && this.getManualPhoneCount() === 0) return false;

            if (this.scheduleSms && (!this.scheduleDate || !this.scheduleTime)) return false;

            return true;
        },

        getRecipientsPreview() {
            if (this.selectionMode === 'individual') {
                return this.selectedRecipients.map(r => r.name).join(', ') || 'None selected';
            } else if (this.selectionMode === 'role') {
                return this.selectedRoles.join(', ') || 'None selected';
            } else if (this.selectionMode === 'class') {
                return this.selectedClasses.length + ' class(es)' + (this.includeParents ? ' + parents' : '');
            } else if (this.selectionMode === 'manual') {
                return this.getManualPhoneCount() + ' phone number(s)';
            }
            return 'None';
        },

        previewSms() {
            const modal = new bootstrap.Modal(this.$refs.previewModal);
            modal.show();
        },

        handleSubmit(event) {
            if (!this.canSend()) {
                event.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }

            this.isSubmitting = true;
            return true;
        },

        init() {
            this.$watch('message', () => this.updateEstimatedCredits());
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

.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.sms-preview {
    min-height: 150px;
}

.sms-bubble {
    max-width: 80%;
    position: relative;
}

.sms-bubble::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: -10px;
    width: 0;
    height: 0;
    border: 10px solid transparent;
    border-left-color: var(--bs-primary);
    border-bottom: 0;
    margin-bottom: -5px;
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

[dir="rtl"] .me-3 {
    margin-right: 0 !important;
    margin-left: 1rem !important;
}

[dir="rtl"] .ms-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}
</style>
@endpush
