{{-- Messages View (Show) View --}}
{{-- Prompt 249: Message details view with conversation thread --}}

@extends('layouts.app')

@section('title', 'View Message')

@section('content')
<div x-data="messageView()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">View Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('messages.inbox') }}">Messages</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('messages.compose', ['reply_to' => $message->id ?? 1]) }}" class="btn btn-primary">
                <i class="bi bi-reply me-1"></i> Reply
            </a>
            <a href="{{ route('messages.compose', ['forward' => $message->id ?? 1]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-forward me-1"></i> Forward
            </a>
            <button type="button" class="btn btn-outline-danger" @click="confirmDelete">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <a href="{{ $message->is_sent ?? false ? route('messages.sent') : route('messages.inbox') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
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
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Message Card -->
            <x-card>
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="d-flex align-items-center gap-2">
                            @if(($message->priority ?? 'normal') === 'high')
                                <i class="bi bi-exclamation-triangle text-danger" title="High Priority"></i>
                            @endif
                            <span class="fw-medium">{{ $message->subject ?? 'No Subject' }}</span>
                        </span>
                        <span class="badge {{ ($message->priority ?? 'normal') === 'high' ? 'bg-danger' : (($message->priority ?? 'normal') === 'low' ? 'bg-secondary' : 'bg-primary') }}">
                            {{ ucfirst($message->priority ?? 'normal') }} Priority
                        </span>
                    </div>
                </x-slot>

                <!-- Sender/Recipient Info -->
                <div class="d-flex align-items-start gap-3 mb-4 pb-3 border-bottom">
                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                        {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $message->sender->name ?? 'Unknown Sender' }}</strong>
                                <span class="badge bg-info ms-1">{{ ucfirst($message->sender->role ?? 'user') }}</span>
                                <br>
                                <small class="text-muted">{{ $message->sender->email ?? '' }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    {{ isset($message->created_at) ? \Carbon\Carbon::parse($message->created_at)->format('d M Y, H:i') : 'N/A' }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    {{ isset($message->created_at) ? \Carbon\Carbon::parse($message->created_at)->diffForHumans() : '' }}
                                </small>
                            </div>
                        </div>
                        
                        <!-- Recipients -->
                        <div class="mt-2">
                            <small class="text-muted">To: </small>
                            @if($message->is_sent ?? false)
                                @foreach(($message->recipients ?? []) as $index => $recipient)
                                    <span class="badge bg-secondary">{{ $recipient->name ?? 'Unknown' }}</span>
                                    @if($index < count($message->recipients ?? []) - 1), @endif
                                @endforeach
                            @else
                                <span class="badge bg-secondary">Me</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Message Body -->
                <div class="message-body mb-4">
                    {!! nl2br(e($message->body ?? 'No message content')) !!}
                </div>

                <!-- Attachment -->
                @if($message->attachment ?? false)
                    <div class="border-top pt-3">
                        <h6 class="mb-3"><i class="bi bi-paperclip me-2"></i>Attachment</h6>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            @php
                                $extension = pathinfo($message->attachment, PATHINFO_EXTENSION);
                                $icon = match(strtolower($extension)) {
                                    'pdf' => 'bi-file-earmark-pdf text-danger',
                                    'doc', 'docx' => 'bi-file-earmark-word text-primary',
                                    'xls', 'xlsx' => 'bi-file-earmark-excel text-success',
                                    'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-info',
                                    default => 'bi-file-earmark text-secondary'
                                };
                            @endphp
                            <i class="bi {{ $icon }} fs-2"></i>
                            <div class="flex-grow-1">
                                <span class="fw-medium">{{ basename($message->attachment) }}</span>
                                <br>
                                <small class="text-muted">{{ strtoupper($extension) }} File</small>
                            </div>
                            <a href="{{ asset('storage/' . $message->attachment) }}" class="btn btn-outline-primary" target="_blank" download>
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                @endif
            </x-card>

            <!-- Conversation Thread -->
            @if(isset($thread) && count($thread) > 0)
                <x-card class="mt-4">
                    <x-slot name="header">
                        <i class="bi bi-chat-dots me-2"></i>
                        Conversation Thread
                        <span class="badge bg-primary ms-2">{{ count($thread) }}</span>
                    </x-slot>

                    <div class="conversation-thread">
                        @foreach($thread as $threadMessage)
                            <div class="thread-message mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="avatar-circle avatar-sm {{ $threadMessage->sender_id == auth()->id() ? 'bg-success' : 'bg-primary' }} bg-opacity-10 {{ $threadMessage->sender_id == auth()->id() ? 'text-success' : 'text-primary' }}">
                                        {{ strtoupper(substr($threadMessage->sender->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $threadMessage->sender->name ?? 'Unknown' }}</strong>
                                                @if($threadMessage->sender_id == auth()->id())
                                                    <span class="badge bg-success ms-1">You</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                {{ isset($threadMessage->created_at) ? \Carbon\Carbon::parse($threadMessage->created_at)->format('d M Y, H:i') : 'N/A' }}
                                            </small>
                                        </div>
                                        <p class="mb-0 mt-2">{{ $threadMessage->body ?? '' }}</p>
                                        @if($threadMessage->attachment ?? false)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $threadMessage->attachment) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                    <i class="bi bi-paperclip me-1"></i> Attachment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            <!-- Quick Reply -->
            <x-card class="mt-4">
                <x-slot name="header">
                    <i class="bi bi-reply me-2"></i>
                    Quick Reply
                </x-slot>

                <form action="{{ route('messages.reply', $message->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="reply_to_id" value="{{ $message->id ?? 1 }}">
                    
                    <div class="mb-3">
                        <textarea 
                            class="form-control" 
                            name="body" 
                            rows="4" 
                            placeholder="Type your reply here..."
                            x-model="replyBody"
                            required
                        ></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Attachment (optional)</label>
                        <input type="file" class="form-control form-control-sm" name="attachment">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('messages.compose', ['reply_to' => $message->id ?? 1]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Full Reply
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" :disabled="!replyBody.trim()">
                            <i class="bi bi-send me-1"></i> Send Reply
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Message Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>
                    Message Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Message ID:</td>
                            <td class="text-end">#{{ $message->id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td class="text-end">
                                @if($message->is_read ?? false)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unread</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Priority:</td>
                            <td class="text-end">
                                <span class="badge {{ ($message->priority ?? 'normal') === 'high' ? 'bg-danger' : (($message->priority ?? 'normal') === 'low' ? 'bg-secondary' : 'bg-primary') }}">
                                    {{ ucfirst($message->priority ?? 'normal') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sent:</td>
                            <td class="text-end">{{ isset($message->created_at) ? \Carbon\Carbon::parse($message->created_at)->format('d M Y H:i') : 'N/A' }}</td>
                        </tr>
                        @if($message->read_at ?? false)
                        <tr>
                            <td class="text-muted">Read At:</td>
                            <td class="text-end">{{ \Carbon\Carbon::parse($message->read_at)->format('d M Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Recipients Status (for sent messages) -->
            @if($message->is_sent ?? false)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-people me-2"></i>
                        Recipients Status
                    </span>
                    <span class="badge bg-primary">{{ count($message->recipients ?? []) }}</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($message->recipients ?? [] as $recipient)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle avatar-sm bg-secondary bg-opacity-10 text-secondary">
                                    {{ strtoupper(substr($recipient->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="small">{{ $recipient->name ?? 'Unknown' }}</span>
                                    <br>
                                    <small class="text-muted">{{ ucfirst($recipient->role ?? 'user') }}</small>
                                </div>
                            </div>
                            @if($recipient->pivot->is_read ?? false)
                                <span class="badge bg-success" title="Read at {{ $recipient->pivot->read_at ?? 'N/A' }}">
                                    <i class="bi bi-check-all"></i>
                                </span>
                            @else
                                <span class="badge bg-secondary" title="Not read yet">
                                    <i class="bi bi-check"></i>
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            No recipients
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Sender Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>
                    Sender Information
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                            {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ $message->sender->name ?? 'Unknown' }}</strong>
                            <br>
                            <small class="text-muted">{{ ucfirst($message->sender->role ?? 'user') }}</small>
                        </div>
                    </div>
                    <div class="small">
                        <p class="mb-1">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            {{ $message->sender->email ?? 'N/A' }}
                        </p>
                        @if($message->sender->phone ?? false)
                        <p class="mb-0">
                            <i class="bi bi-telephone me-2 text-muted"></i>
                            {{ $message->sender->phone }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('messages.compose', ['reply_to' => $message->id ?? 1]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-reply me-1"></i> Reply
                        </a>
                        <a href="{{ route('messages.compose', ['forward' => $message->id ?? 1]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-forward me-1"></i> Forward
                        </a>
                        <button type="button" class="btn btn-outline-warning btn-sm" @click="toggleRead">
                            <i class="bi bi-envelope me-1"></i> 
                            {{ ($message->is_read ?? false) ? 'Mark as Unread' : 'Mark as Read' }}
                        </button>
                        <a href="{{ route('messages.compose') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-pencil-square me-1"></i> New Message
                        </a>
                    </div>
                </div>
            </div>
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
                    <p class="fw-medium">{{ $message->subject ?? 'No Subject' }}</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('messages.destroy', $message->id ?? 1) }}" method="POST" class="d-inline">
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

    <!-- Toggle Read Form (Hidden) -->
    <form id="toggleReadForm" method="POST" action="{{ route('messages.toggle-read', $message->id ?? 1) }}" style="display: none;">
        @csrf
        @method('PATCH')
    </form>
</div>
@endsection

@push('scripts')
<script>
function messageView() {
    return {
        replyBody: '',

        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        toggleRead() {
            document.getElementById('toggleReadForm').submit();
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.25rem;
}

.avatar-circle.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

.message-body {
    line-height: 1.8;
    font-size: 1rem;
}

.conversation-thread {
    max-height: 400px;
    overflow-y: auto;
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

[dir="rtl"] .text-end {
    text-align: left !important;
}
</style>
@endpush
