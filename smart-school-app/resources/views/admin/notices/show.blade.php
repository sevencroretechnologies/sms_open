{{-- Notices Details View --}}
{{-- Prompt 245: Notice details view with content and targeting --}}

@extends('layouts.app')

@section('title', 'Notice Details')

@section('content')
<div x-data="noticeDetails()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notice Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('notices.index') }}">Notices</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('notices.edit', $notice->id ?? 1) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            @if($notice->is_published ?? false)
                <button type="button" class="btn btn-outline-secondary" @click="togglePublish(false)">
                    <i class="bi bi-x-circle me-1"></i> Unpublish
                </button>
            @else
                <button type="button" class="btn btn-success" @click="togglePublish(true)">
                    <i class="bi bi-check-circle me-1"></i> Publish
                </button>
            @endif
            <button type="button" class="btn btn-outline-danger" @click="confirmDelete">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <a href="{{ route('notices.index') }}" class="btn btn-outline-secondary">
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
            <!-- Notice Content Card -->
            <x-card>
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span>
                            <i class="bi bi-megaphone me-2"></i>
                            {{ $notice->title ?? 'Notice Title' }}
                        </span>
                        @if($notice->is_published ?? false)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning text-dark">Draft</span>
                        @endif
                    </div>
                </x-slot>

                <div class="mb-4">
                    <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                        <span>
                            <i class="bi bi-calendar me-1"></i>
                            {{ isset($notice->notice_date) ? \Carbon\Carbon::parse($notice->notice_date)->format('d M Y') : 'N/A' }}
                        </span>
                        @if($notice->expiry_date ?? false)
                            <span>
                                <i class="bi bi-calendar-x me-1"></i>
                                Expires: {{ \Carbon\Carbon::parse($notice->expiry_date)->format('d M Y') }}
                                @if(\Carbon\Carbon::parse($notice->expiry_date)->isPast())
                                    <span class="badge bg-danger ms-1">Expired</span>
                                @endif
                            </span>
                        @endif
                        @if($notice->publisher ?? false)
                            <span>
                                <i class="bi bi-person me-1"></i>
                                By: {{ $notice->publisher->name ?? 'N/A' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Notice Content -->
                <div class="notice-content mb-4">
                    {!! nl2br(e($notice->content ?? 'Notice content will appear here...')) !!}
                </div>

                <!-- Attachment -->
                @if($notice->attachment ?? false)
                    <div class="border-top pt-3">
                        <h6 class="mb-3"><i class="bi bi-paperclip me-2"></i>Attachment</h6>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            @php
                                $extension = pathinfo($notice->attachment, PATHINFO_EXTENSION);
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
                                <span class="fw-medium">{{ basename($notice->attachment) }}</span>
                                <br>
                                <small class="text-muted">{{ strtoupper($extension) }} File</small>
                            </div>
                            <a href="{{ asset('storage/' . $notice->attachment) }}" class="btn btn-outline-primary" target="_blank" download>
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                @endif
            </x-card>

            <!-- Notification Actions -->
            <x-card class="mt-4">
                <x-slot name="header">
                    <i class="bi bi-bell me-2"></i>
                    Send Notifications
                </x-slot>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-primary btn-lg" @click="sendSmsNotification">
                                <i class="bi bi-chat-dots fs-4 d-block mb-2"></i>
                                <span class="d-block">Send SMS Notification</span>
                                <small class="text-muted">Notify via SMS</small>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-info btn-lg" @click="sendEmailNotification">
                                <i class="bi bi-envelope fs-4 d-block mb-2"></i>
                                <span class="d-block">Send Email Notification</span>
                                <small class="text-muted">Notify via Email</small>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    Notifications will be sent to all targeted users based on the roles and classes selected for this notice.
                </div>
            </x-card>

            <!-- Print Notice -->
            <x-card class="mt-4">
                <x-slot name="header">
                    <i class="bi bi-printer me-2"></i>
                    Print Options
                </x-slot>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" @click="printNotice">
                        <i class="bi bi-printer me-1"></i> Print Notice
                    </button>
                    <a href="{{ route('notices.pdf', $notice->id ?? 1) }}" class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                    </a>
                </div>
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notice Info Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>
                    Notice Information
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Notice ID:</td>
                            <td class="text-end">#{{ $notice->id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Notice Date:</td>
                            <td class="text-end">{{ isset($notice->notice_date) ? \Carbon\Carbon::parse($notice->notice_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Expiry Date:</td>
                            <td class="text-end">{{ isset($notice->expiry_date) ? \Carbon\Carbon::parse($notice->expiry_date)->format('d M Y') : 'No expiry' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td class="text-end">
                                @if($notice->is_published ?? false)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td class="text-end">{{ isset($notice->created_at) ? \Carbon\Carbon::parse($notice->created_at)->format('d M Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Updated:</td>
                            <td class="text-end">{{ isset($notice->updated_at) ? \Carbon\Carbon::parse($notice->updated_at)->format('d M Y H:i') : 'N/A' }}</td>
                        </tr>
                        @if($notice->published_at ?? false)
                        <tr>
                            <td class="text-muted">Published At:</td>
                            <td class="text-end">{{ \Carbon\Carbon::parse($notice->published_at)->format('d M Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Target Roles Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>
                    Target Roles
                </div>
                <div class="card-body">
                    @if(empty($notice->target_roles ?? []))
                        <span class="badge bg-primary">All Roles</span>
                    @else
                        @foreach($notice->target_roles ?? [] as $role)
                            <span class="badge bg-info me-1 mb-1">{{ ucfirst($role) }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Target Classes Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-mortarboard me-2"></i>
                    Target Classes
                </div>
                <div class="card-body">
                    @if(empty($notice->target_classes ?? []))
                        <span class="badge bg-primary">All Classes</span>
                    @else
                        @foreach($notice->target_classes ?? [] as $classId)
                            @php
                                $className = collect($classes ?? [])->firstWhere('id', $classId)?->name ?? "Class $classId";
                            @endphp
                            <span class="badge bg-secondary me-1 mb-1">{{ $className }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Recipients Card -->
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-person-check me-2"></i>
                        Recipients
                    </span>
                    <span class="badge bg-primary">{{ $recipientCount ?? 0 }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">This notice targets approximately {{ $recipientCount ?? 0 }} users.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" @click="showRecipients = true">
                        <i class="bi bi-eye me-1"></i> View Recipients
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('notices.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Create New Notice
                        </a>
                        <a href="{{ route('notices.create', ['duplicate' => $notice->id ?? 1]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-copy me-1"></i> Duplicate Notice
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
                    <p>Are you sure you want to delete this notice?</p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('notices.destroy', $notice->id ?? 1) }}" method="POST" class="d-inline">
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

    <!-- Recipients Modal -->
    <div class="modal fade" id="recipientsModal" tabindex="-1" x-ref="recipientsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-people me-2"></i>
                        Notice Recipients
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recipients ?? [] as $recipient)
                                    <tr>
                                        <td>{{ $recipient->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($recipient->role ?? 'N/A') }}</span></td>
                                        <td>{{ $recipient->email ?? 'N/A' }}</td>
                                        <td>{{ $recipient->phone ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recipients found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Publish/Unpublish Form (Hidden) -->
    <form id="togglePublishForm" method="POST" action="{{ route('notices.toggle-publish', $notice->id ?? 1) }}" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="is_published" x-model="publishValue">
    </form>

    <!-- Notification Forms (Hidden) -->
    <form id="smsNotificationForm" method="POST" action="{{ route('notices.send-sms', $notice->id ?? 1) }}" style="display: none;">
        @csrf
    </form>
    <form id="emailNotificationForm" method="POST" action="{{ route('notices.send-email', $notice->id ?? 1) }}" style="display: none;">
        @csrf
    </form>
</div>
@endsection

@push('scripts')
<script>
function noticeDetails() {
    return {
        showRecipients: false,
        publishValue: true,

        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        togglePublish(publish) {
            if (confirm(publish ? 'Are you sure you want to publish this notice?' : 'Are you sure you want to unpublish this notice?')) {
                this.publishValue = publish;
                document.getElementById('togglePublishForm').submit();
            }
        },

        sendSmsNotification() {
            if (confirm('Are you sure you want to send SMS notifications to all targeted users?')) {
                document.getElementById('smsNotificationForm').submit();
            }
        },

        sendEmailNotification() {
            if (confirm('Are you sure you want to send email notifications to all targeted users?')) {
                document.getElementById('emailNotificationForm').submit();
            }
        },

        printNotice() {
            window.print();
        },

        init() {
            this.$watch('showRecipients', (value) => {
                if (value) {
                    const modal = new bootstrap.Modal(this.$refs.recipientsModal);
                    modal.show();
                    this.showRecipients = false;
                }
            });
        }
    }
}
</script>
@endpush

@push('styles')
<style>
.notice-content {
    line-height: 1.8;
    font-size: 1rem;
}

@media print {
    .btn, .card-header, nav, .breadcrumb, .modal {
        display: none !important;
    }
    .notice-content {
        font-size: 12pt;
    }
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

[dir="rtl"] .text-end {
    text-align: left !important;
}
</style>
@endpush
