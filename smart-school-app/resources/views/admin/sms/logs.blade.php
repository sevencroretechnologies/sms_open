{{-- SMS Logs View --}}
{{-- Prompt 250: SMS logs view with status and delivery information --}}

@extends('layouts.app')

@section('title', 'SMS Logs')

@section('content')
<div x-data="smsLogsManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">SMS Logs</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Communication</a></li>
                    <li class="breadcrumb-item active">SMS Logs</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-success" @click="exportLogs()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('sms.send') }}" class="btn btn-primary">
                <i class="bi bi-chat-dots me-1"></i> Send SMS
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
                    <i class="bi bi-chat-dots fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total'] ?? count($logs ?? []) }}</h3>
                    <small class="text-muted">Total SMS Sent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['delivered'] ?? 0 }}</h3>
                    <small class="text-muted">Delivered</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-clock fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-danger bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-x-circle fs-3 text-danger mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['failed'] ?? 0 }}</h3>
                    <small class="text-muted">Failed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Credits Info -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle fs-4 me-3"></i>
        <div>
            <strong>SMS Credits:</strong> {{ $smsCredits ?? 'N/A' }} remaining
            <span class="mx-2">|</span>
            <strong>This Month:</strong> {{ $stats['this_month'] ?? 0 }} SMS sent
            <a href="{{ route('sms.settings') }}" class="ms-3 text-decoration-none">
                <i class="bi bi-gear me-1"></i> SMS Settings
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Search by phone, message..."
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
                    <option value="">All Status</option>
                    <option value="delivered">Delivered</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                    <option value="queued">Queued</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Type</label>
                <select class="form-select" x-model="filters.type">
                    <option value="">All Types</option>
                    <option value="notice">Notice</option>
                    <option value="attendance">Attendance</option>
                    <option value="fee">Fee Reminder</option>
                    <option value="exam">Exam</option>
                    <option value="general">General</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="resetFilters()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </x-card>

    <!-- SMS Logs Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-list-ul me-2"></i>
                    SMS Logs
                    <span class="badge bg-primary ms-2">{{ count($logs ?? []) }}</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-info btn-sm" @click="refreshStatus()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date/Time</th>
                        <th>Recipient</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Type</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Credits</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                        <tr x-show="matchesFilters({{ json_encode([
                            'phone' => strtolower($log->phone ?? ''),
                            'message' => strtolower($log->message ?? ''),
                            'recipient_name' => strtolower($log->recipient->name ?? ''),
                            'sent_at' => $log->created_at ?? '',
                            'status' => $log->status ?? '',
                            'type' => $log->type ?? ''
                        ]) }})">
                            <td>
                                <span class="text-nowrap">
                                    {{ isset($log->created_at) ? \Carbon\Carbon::parse($log->created_at)->format('d M Y') : 'N/A' }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ isset($log->created_at) ? \Carbon\Carbon::parse($log->created_at)->format('H:i:s') : '' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle avatar-sm bg-primary bg-opacity-10 text-primary">
                                        {{ strtoupper(substr($log->recipient->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span>{{ $log->recipient->name ?? 'Unknown' }}</span>
                                        <br>
                                        <small class="text-muted">{{ ucfirst($log->recipient_type ?? 'user') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace">{{ $log->phone ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span title="{{ $log->message ?? '' }}">
                                    {{ Str::limit($log->message ?? 'No message', 40) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'notice' => 'bg-info',
                                        'attendance' => 'bg-primary',
                                        'fee' => 'bg-warning text-dark',
                                        'exam' => 'bg-success',
                                        'general' => 'bg-secondary'
                                    ];
                                @endphp
                                <span class="badge {{ $typeColors[$log->type ?? 'general'] ?? 'bg-secondary' }}">
                                    {{ ucfirst($log->type ?? 'General') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColors = [
                                        'delivered' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'failed' => 'bg-danger',
                                        'queued' => 'bg-info'
                                    ];
                                    $statusIcons = [
                                        'delivered' => 'bi-check-circle',
                                        'pending' => 'bi-clock',
                                        'failed' => 'bi-x-circle',
                                        'queued' => 'bi-hourglass-split'
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$log->status ?? 'pending'] ?? 'bg-secondary' }}">
                                    <i class="bi {{ $statusIcons[$log->status ?? 'pending'] ?? 'bi-question-circle' }} me-1"></i>
                                    {{ ucfirst($log->status ?? 'Pending') }}
                                </span>
                                @if(($log->status ?? '') === 'failed' && ($log->error_message ?? false))
                                    <br>
                                    <small class="text-danger" title="{{ $log->error_message }}">
                                        {{ Str::limit($log->error_message, 20) }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $log->credits_used ?? 1 }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-primary" 
                                        title="View Details"
                                        @click="viewDetails({{ $log->id }})"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if(($log->status ?? '') === 'failed')
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-warning" 
                                            title="Retry"
                                            @click="retrySms({{ $log->id }})"
                                        >
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No SMS logs found</p>
                                    <a href="{{ route('sms.send') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-chat-dots me-1"></i> Send SMS
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($logs) && $logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                </div>
                {{ $logs->links() }}
            </div>
        </x-slot>
        @endif
    </x-card>

    <!-- Quick Links -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <a href="{{ route('sms.send') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-chat-dots fs-1 text-primary mb-2 d-block"></i>
                    <h6 class="mb-0">Send SMS</h6>
                    <small class="text-muted">Send new SMS message</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('sms.templates') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-file-text fs-1 text-success mb-2 d-block"></i>
                    <h6 class="mb-0">SMS Templates</h6>
                    <small class="text-muted">Manage message templates</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('sms.settings') }}" class="card text-decoration-none h-100">
                <div class="card-body text-center">
                    <i class="bi bi-gear fs-1 text-warning mb-2 d-block"></i>
                    <h6 class="mb-0">SMS Settings</h6>
                    <small class="text-muted">Configure SMS gateway</small>
                </div>
            </a>
        </div>
    </div>

    <!-- SMS Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" x-ref="detailsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-chat-dots me-2"></i>
                        SMS Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div x-show="selectedLog">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Log ID:</td>
                                <td x-text="selectedLog?.id"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Recipient:</td>
                                <td x-text="selectedLog?.recipient_name"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone:</td>
                                <td x-text="selectedLog?.phone"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type:</td>
                                <td x-text="selectedLog?.type"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    <span class="badge" :class="getStatusClass(selectedLog?.status)" x-text="selectedLog?.status"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sent At:</td>
                                <td x-text="selectedLog?.sent_at"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Delivered At:</td>
                                <td x-text="selectedLog?.delivered_at || 'N/A'"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Credits Used:</td>
                                <td x-text="selectedLog?.credits_used"></td>
                            </tr>
                        </table>
                        <div class="border-top pt-3 mt-3">
                            <label class="text-muted small">Message:</label>
                            <p class="mb-0" x-text="selectedLog?.message"></p>
                        </div>
                        <div x-show="selectedLog?.error_message" class="alert alert-danger mt-3 mb-0">
                            <strong>Error:</strong> <span x-text="selectedLog?.error_message"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" x-show="selectedLog?.status === 'failed'" @click="retrySms(selectedLog?.id)">
                        <i class="bi bi-arrow-repeat me-1"></i> Retry
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Retry Form (Hidden) -->
    <form id="retryForm" method="POST" style="display: none;">
        @csrf
    </form>
</div>
@endsection

@push('scripts')
<script>
function smsLogsManager() {
    return {
        filters: {
            search: '',
            dateFrom: '',
            dateTo: '',
            status: '',
            type: ''
        },
        selectedLog: null,

        matchesFilters(log) {
            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!log.phone.includes(searchLower) && 
                    !log.message.includes(searchLower) && 
                    !log.recipient_name.includes(searchLower)) {
                    return false;
                }
            }

            // Date range filter
            if (this.filters.dateFrom && log.sent_at < this.filters.dateFrom) {
                return false;
            }
            if (this.filters.dateTo && log.sent_at > this.filters.dateTo) {
                return false;
            }

            // Status filter
            if (this.filters.status && log.status !== this.filters.status) {
                return false;
            }

            // Type filter
            if (this.filters.type && log.type !== this.filters.type) {
                return false;
            }

            return true;
        },

        resetFilters() {
            this.filters = {
                search: '',
                dateFrom: '',
                dateTo: '',
                status: '',
                type: ''
            };
        },

        viewDetails(id) {
            // In production, this would fetch from API
            this.selectedLog = {
                id: id,
                recipient_name: 'John Doe',
                phone: '+1234567890',
                type: 'General',
                status: 'delivered',
                sent_at: '2026-01-08 10:30:00',
                delivered_at: '2026-01-08 10:30:05',
                credits_used: 1,
                message: 'This is a sample SMS message content.',
                error_message: null
            };
            const modal = new bootstrap.Modal(this.$refs.detailsModal);
            modal.show();
        },

        getStatusClass(status) {
            const classes = {
                'delivered': 'bg-success',
                'pending': 'bg-warning text-dark',
                'failed': 'bg-danger',
                'queued': 'bg-info'
            };
            return classes[status] || 'bg-secondary';
        },

        retrySms(id) {
            if (confirm('Are you sure you want to retry sending this SMS?')) {
                const form = document.getElementById('retryForm');
                form.action = `/sms/${id}/retry`;
                form.submit();
            }
        },

        refreshStatus() {
            window.location.reload();
        },

        exportLogs() {
            const params = new URLSearchParams();
            if (this.filters.dateFrom) params.append('date_from', this.filters.dateFrom);
            if (this.filters.dateTo) params.append('date_to', this.filters.dateTo);
            if (this.filters.status) params.append('status', this.filters.status);
            if (this.filters.type) params.append('type', this.filters.type);
            
            window.location.href = '/sms/export?' + params.toString();
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

.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
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

[dir="rtl"] .me-3 {
    margin-right: 0 !important;
    margin-left: 1rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

[dir="rtl"] .ms-3 {
    margin-left: 0 !important;
    margin-right: 1rem !important;
}
</style>
@endpush
