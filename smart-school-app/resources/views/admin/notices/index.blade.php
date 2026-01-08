{{-- Notices List View --}}
{{-- Prompt 242: Notices listing page with search, filter, and CRUD operations --}}

@extends('layouts.app')

@section('title', 'Notices')

@section('content')
<div x-data="noticesManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notices</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Communication</a></li>
                    <li class="breadcrumb-item active">Notices</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-success" @click="exportNotices()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <a href="{{ route('notices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Notice
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
                    <i class="bi bi-megaphone fs-3 text-primary mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['total'] ?? count($notices ?? []) }}</h3>
                    <small class="text-muted">Total Notices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-check-circle fs-3 text-success mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['published'] ?? 0 }}</h3>
                    <small class="text-muted">Published</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-file-earmark fs-3 text-warning mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['draft'] ?? 0 }}</h3>
                    <small class="text-muted">Drafts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 bg-danger bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <i class="bi bi-calendar-x fs-3 text-danger mb-2 d-block"></i>
                    <h3 class="mb-0">{{ $stats['expired'] ?? 0 }}</h3>
                    <small class="text-muted">Expired</small>
                </div>
            </div>
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
                        placeholder="Search by title, content..."
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
                <label class="form-label small text-muted">Target Role</label>
                <select class="form-select" x-model="filters.role">
                    <option value="">All Roles</option>
                    @foreach($roles ?? [] as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select" x-model="filters.status">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="resetFilters()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </x-card>

    <!-- Notices Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-megaphone me-2"></i>
                    Notices
                    <span class="badge bg-primary ms-2">{{ count($notices ?? []) }}</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAll" x-model="selectAll" @change="toggleSelectAll()">
                        <label class="form-check-label small" for="selectAll">Select All</label>
                    </div>
                    <div class="dropdown" x-show="selectedNotices.length > 0">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Bulk Actions (<span x-text="selectedNotices.length"></span>)
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" @click.prevent="bulkPublish()"><i class="bi bi-check-circle me-2"></i>Publish Selected</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="bulkUnpublish()"><i class="bi bi-x-circle me-2"></i>Unpublish Selected</a></li>
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
                        <th>Notice Date</th>
                        <th>Title</th>
                        <th>Target Roles</th>
                        <th>Target Classes</th>
                        <th>Expiry Date</th>
                        <th class="text-center">Status</th>
                        <th>Published By</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices ?? [] as $index => $notice)
                        <tr x-show="matchesFilters({{ json_encode([
                            'title' => strtolower($notice->title ?? ''),
                            'content' => strtolower($notice->content ?? ''),
                            'notice_date' => $notice->notice_date ?? '',
                            'expiry_date' => $notice->expiry_date ?? '',
                            'target_roles' => $notice->target_roles ?? [],
                            'is_published' => $notice->is_published ?? false
                        ]) }})">
                            <td>
                                <input type="checkbox" class="form-check-input" :value="{{ $notice->id }}" x-model="selectedNotices">
                            </td>
                            <td>
                                <span class="text-nowrap">{{ \Carbon\Carbon::parse($notice->notice_date)->format('d M Y') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('notices.show', $notice->id) }}" class="text-decoration-none fw-medium">
                                    {{ Str::limit($notice->title, 40) }}
                                </a>
                                @if($notice->attachment)
                                    <i class="bi bi-paperclip text-muted ms-1" title="Has attachment"></i>
                                @endif
                            </td>
                            <td>
                                @if($notice->target_roles)
                                    @foreach(array_slice($notice->target_roles, 0, 2) as $role)
                                        <span class="badge bg-info">{{ ucfirst($role) }}</span>
                                    @endforeach
                                    @if(count($notice->target_roles) > 2)
                                        <span class="badge bg-secondary">+{{ count($notice->target_roles) - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">All</span>
                                @endif
                            </td>
                            <td>
                                @if($notice->target_classes)
                                    @foreach(array_slice($notice->target_classes, 0, 2) as $class)
                                        <span class="badge bg-secondary">{{ $class }}</span>
                                    @endforeach
                                    @if(count($notice->target_classes) > 2)
                                        <span class="badge bg-secondary">+{{ count($notice->target_classes) - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">All</span>
                                @endif
                            </td>
                            <td>
                                @if($notice->expiry_date)
                                    @php
                                        $isExpired = \Carbon\Carbon::parse($notice->expiry_date)->isPast();
                                    @endphp
                                    <span class="{{ $isExpired ? 'text-danger' : '' }}">
                                        {{ \Carbon\Carbon::parse($notice->expiry_date)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">No expiry</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($notice->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($notice->publisher)
                                    <span class="small">{{ $notice->publisher->name ?? 'N/A' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a 
                                        href="{{ route('notices.show', $notice->id) }}" 
                                        class="btn btn-outline-primary" 
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a 
                                        href="{{ route('notices.edit', $notice->id) }}" 
                                        class="btn btn-outline-warning" 
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($notice->is_published)
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-secondary" 
                                            title="Unpublish"
                                            @click="togglePublish({{ $notice->id }}, false)"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @else
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-success" 
                                            title="Publish"
                                            @click="togglePublish({{ $notice->id }}, true)"
                                        >
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    @endif
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Delete"
                                        @click="confirmDelete({{ $notice->id }}, '{{ addslashes($notice->title) }}')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-megaphone fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No notices found</p>
                                    <a href="{{ route('notices.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Notice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($notices) && $notices instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $notices->firstItem() ?? 0 }} to {{ $notices->lastItem() ?? 0 }} of {{ $notices->total() }} entries
                </div>
                {{ $notices->links() }}
            </div>
        </x-slot>
        @endif
    </x-card>

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
                    <p>Are you sure you want to delete the notice "<strong x-text="deleteNoticeTitle"></strong>"?</p>
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

    <!-- Publish/Unpublish Form (Hidden) -->
    <form id="togglePublishForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="is_published" x-model="publishValue">
    </form>
</div>
@endsection

@push('scripts')
<script>
function noticesManager() {
    return {
        filters: {
            search: '',
            dateFrom: '',
            dateTo: '',
            role: '',
            status: ''
        },
        selectedNotices: [],
        selectAll: false,
        deleteNoticeId: null,
        deleteNoticeTitle: '',
        deleteUrl: '',
        publishValue: true,

        matchesFilters(notice) {
            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!notice.title.includes(searchLower) && !notice.content.includes(searchLower)) {
                    return false;
                }
            }

            // Date range filter
            if (this.filters.dateFrom && notice.notice_date < this.filters.dateFrom) {
                return false;
            }
            if (this.filters.dateTo && notice.notice_date > this.filters.dateTo) {
                return false;
            }

            // Role filter
            if (this.filters.role && notice.target_roles) {
                if (!notice.target_roles.includes(this.filters.role)) {
                    return false;
                }
            }

            // Status filter
            if (this.filters.status) {
                if (this.filters.status === 'published' && !notice.is_published) {
                    return false;
                }
                if (this.filters.status === 'unpublished' && notice.is_published) {
                    return false;
                }
                if (this.filters.status === 'expired' && notice.expiry_date) {
                    const expiryDate = new Date(notice.expiry_date);
                    if (expiryDate >= new Date()) {
                        return false;
                    }
                }
            }

            return true;
        },

        resetFilters() {
            this.filters = {
                search: '',
                dateFrom: '',
                dateTo: '',
                role: '',
                status: ''
            };
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedNotices = Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).map(cb => parseInt(cb.value));
            } else {
                this.selectedNotices = [];
            }
        },

        confirmDelete(id, title) {
            this.deleteNoticeId = id;
            this.deleteNoticeTitle = title;
            this.deleteUrl = `/notices/${id}`;
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },

        togglePublish(id, publish) {
            this.publishValue = publish;
            const form = document.getElementById('togglePublishForm');
            form.action = `/notices/${id}/toggle-publish`;
            form.submit();
        },

        bulkPublish() {
            if (this.selectedNotices.length === 0) return;
            if (confirm('Are you sure you want to publish ' + this.selectedNotices.length + ' notices?')) {
                // Submit bulk publish form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/notices/bulk-publish';
                form.innerHTML = `@csrf<input type="hidden" name="ids" value="${this.selectedNotices.join(',')}">`;
                document.body.appendChild(form);
                form.submit();
            }
        },

        bulkUnpublish() {
            if (this.selectedNotices.length === 0) return;
            if (confirm('Are you sure you want to unpublish ' + this.selectedNotices.length + ' notices?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/notices/bulk-unpublish';
                form.innerHTML = `@csrf<input type="hidden" name="ids" value="${this.selectedNotices.join(',')}">`;
                document.body.appendChild(form);
                form.submit();
            }
        },

        bulkDelete() {
            if (this.selectedNotices.length === 0) return;
            if (confirm('Are you sure you want to delete ' + this.selectedNotices.length + ' notices? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/notices/bulk-delete';
                form.innerHTML = `@csrf@method('DELETE')<input type="hidden" name="ids" value="${this.selectedNotices.join(',')}">`;
                document.body.appendChild(form);
                form.submit();
            }
        },

        exportNotices() {
            window.location.href = '/notices/export';
        }
    }
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .table th,
[dir="rtl"] .table td {
    text-align: right;
}

[dir="rtl"] .text-center {
    text-align: center !important;
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
