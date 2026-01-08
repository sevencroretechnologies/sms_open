{{-- Messages Inbox View --}}
{{-- Prompt 246: Messages inbox view with search, filter, and actions --}}

@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div x-data="inboxManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inbox</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Messages</a></li>
                    <li class="breadcrumb-item active">Inbox</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-success" @click="exportMessages()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('messages.compose') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Compose
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

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-envelope fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total'] ?? count($messages ?? []) }}</h3>
                    <small class="text-muted">Total Messages</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-envelope-fill fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['unread'] ?? 0 }}</h3>
                    <small class="text-muted">Unread</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-envelope-open fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['read'] ?? 0 }}</h3>
                    <small class="text-muted">Read</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-paperclip fs-3 text-info mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['with_attachments'] ?? 0 }}</h3>
                    <small class="text-muted">With Attachments</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Search by subject, sender..."
                        x-model="filters.search"
                    >
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Date From</label>
                <input type="date" class="form-control" x-model="filters.dateFrom">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Date To</label>
                <input type="date" class="form-control" x-model="filters.dateTo">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select" x-model="filters.status">
                    <option value="">All</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="resetFilters()">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </button>
            </div>
        </div>
    </x-card>

    <!-- Messages Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-inbox me-2"></i>
                    Inbox
                    <span class="badge bg-primary ms-2">{{ count($messages ?? []) }}</span>
                    @if(($stats['unread'] ?? 0) > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $stats['unread'] }} unread</span>
                    @endif
                </span>
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAll" x-model="selectAll" @change="toggleSelectAll()">
                        <label class="form-check-label small" for="selectAll">Select All</label>
                    </div>
                    <div class="dropdown" x-show="selectedMessages.length > 0">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions (<span x-text="selectedMessages.length"></span>)
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" @click.prevent="bulkMarkRead()"><i class="bi bi-envelope-open me-2"></i>Mark as Read</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="bulkMarkUnread()"><i class="bi bi-envelope-fill me-2"></i>Mark as Unread</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" @click.prevent="bulkDelete()"><i class="bi bi-trash me-2"></i>Delete Selected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleSelectAll()">
                        </th>
                        <th style="width: 30px;"></th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th style="width: 30px;"></th>
                        <th>Received</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages ?? [] as $index => $message)
                        <tr 
                            class="{{ !($message->is_read ?? false) ? 'table-warning' : '' }}"
                            x-show="matchesFilters({{ json_encode([
                                'subject' => strtolower($message->subject ?? ''),
                                'sender_name' => strtolower($message->sender->name ?? ''),
                                'received_at' => $message->created_at ?? '',
                                'is_read' => $message->is_read ?? false
                            ]) }})"
                        >
                            <td>
                                <input type="checkbox" class="form-check-input" :value="{{ $message->id }}" x-model="selectedMessages">
                            </td>
                            <td>
                                @if(!($message->is_read ?? false))
                                    <i class="bi bi-circle-fill text-primary small" title="Unread"></i>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                                        {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="{{ !($message->is_read ?? false) ? 'fw-bold' : '' }}">
                                            {{ $message->sender->name ?? 'Unknown' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ ucfirst($message->sender->role ?? 'user') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('messages.show', $message->id) }}" class="text-decoration-none {{ !($message->is_read ?? false) ? 'fw-bold' : '' }}">
                                    {{ Str::limit($message->subject ?? 'No Subject', 50) }}
                                </a>
                                <br>
                                <small class="text-muted">{{ Str::limit($message->body ?? '', 60) }}</small>
                            </td>
                            <td>
                                @if($message->attachment ?? false)
                                    <i class="bi bi-paperclip text-muted" title="Has attachment"></i>
                                @endif
                            </td>
                            <td>
                                <span class="text-nowrap small">
                                    {{ isset($message->created_at) ? \Carbon\Carbon::parse($message->created_at)->diffForHumans() : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a 
                                        href="{{ route('messages.show', $message->id) }}" 
                                        class="btn btn-outline-primary" 
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a 
                                        href="{{ route('messages.compose', ['reply_to' => $message->id]) }}" 
                                        class="btn btn-outline-success" 
                                        title="Reply"
                                    >
                                        <i class="bi bi-reply"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Delete"
                                        @click="confirmDelete({{ $message->id }}, '{{ addslashes($message->subject ?? 'No Subject') }}')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-2">Your inbox is empty</p>
                                    <a href="{{ route('messages.compose') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Compose Message
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($messages) && $messages instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $messages->firstItem() ?? 0 }} to {{ $messages->lastItem() ?? 0 }} of {{ $messages->total() }} entries
                </div>
                {{ $messages->links() }}
            </div>
        </x-slot>
        @endif
    </x-card>

    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('messages.sent') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-send fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="mb-0">Sent Messages</h6>
                    <small class="text-muted">View sent messages</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('messages.compose') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-pencil-square fs-1 text-success mb-2 d-block"></i>
                    <h6 class="mb-0">Compose</h6>
                    <small class="text-muted">Write new message</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('notices.index') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-megaphone fs-1 text-warning mb-2 d-block"></i>
                    <h6 class="mb-0">Notices</h6>
                    <small class="text-muted">View school notices</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" x-ref="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this message?</p>
                    <p class="fw-medium" x-text="deleteMessageSubject"></p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form :action="deleteUrl" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function inboxManager() {
    return {
        filters: {
            search: '',
            dateFrom: '',
            dateTo: '',
            status: ''
        },
        selectedMessages: [],
        selectAll: false,
        deleteMessageId: null,
        deleteMessageSubject: '',
        deleteUrl: '',

        matchesFilters(message) {
            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!message.subject.includes(searchLower) && !message.sender_name.includes(searchLower)) {
                    return false;
                }
            }

            // Date range filter
            if (this.filters.dateFrom && message.received_at < this.filters.dateFrom) {
                return false;
            }
            if (this.filters.dateTo && message.received_at > this.filters.dateTo) {
                return false;
            }

            // Status filter
            if (this.filters.status) {
                if (this.filters.status === 'read' && !message.is_read) {
                    return false;
                }
                if (this.filters.status === 'unread' && message.is_read) {
                    return false;
                }
            }

            return true;
        },

        resetFilters() {
            this.filters = {
                search: '',
                dateFrom: '',
                dateTo: '',
                status: ''
            };
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedMessages = Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).map(cb => parseInt(cb.value));
            } else {
                this.selectedMessages = [];
            }
        },

        confirmDelete(id, subject) {
            this.deleteMessageId = id;
            this.deleteMessageSubject = subject;
            this.deleteUrl = `/messages/${id}`;
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        bulkMarkRead() {
            if (this.selectedMessages.length === 0) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/messages/bulk-mark-read';
            form.innerHTML = `@csrf<input type="hidden" name="ids" value="${this.selectedMessages.join(',')}">`;
            document.body.appendChild(form);
            form.submit();
        },

        bulkMarkUnread() {
            if (this.selectedMessages.length === 0) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/messages/bulk-mark-unread';
            form.innerHTML = `@csrf<input type="hidden" name="ids" value="${this.selectedMessages.join(',')}">`;
            document.body.appendChild(form);
            form.submit();
        },

        bulkDelete() {
            if (this.selectedMessages.length === 0) return;
            if (confirm('Are you sure you want to delete ' + this.selectedMessages.length + ' messages? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/messages/bulk-delete';
                form.innerHTML = `@csrf@method('DELETE')<input type="hidden" name="ids" value="${this.selectedMessages.join(',')}">`;
                document.body.appendChild(form);
                form.submit();
            }
        },

        exportMessages() {
            window.location.href = '/messages/export?type=inbox';
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

[dir="rtl"] .table th,
[dir="rtl"] .table td {
    text-align: right;
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

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}
</style>
@endpush
