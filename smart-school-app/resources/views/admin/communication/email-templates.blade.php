{{-- Email Templates List View --}}
{{-- Admin email templates listing page --}}

@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')
<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Email Templates</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.communication.emails') }}">Emails</a></li>
                    <li class="breadcrumb-item active">Templates</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.communication.emails') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.communication.email-template-create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create Template
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

    <!-- Templates Grid -->
    <div class="row g-4">
        @forelse($templates ?? [] as $template)
            <div class="col-md-6 col-lg-4">
                <x-card class="h-100">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-text fs-5"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.communication.email-template-edit', $template) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.communication.compose-email', ['template_id' => $template->id]) }}"><i class="bi bi-envelope me-2"></i>Use Template</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $template->id }})"><i class="bi bi-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="mb-2">{{ $template->name ?? 'Template Name' }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit($template->subject ?? 'No subject', 50) }}</p>
                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <small class="text-muted">{{ isset($template->updated_at) ? $template->updated_at->diffForHumans() : 'N/A' }}</small>
                        <a href="{{ route('admin.communication.compose-email', ['template_id' => $template->id]) }}" class="btn btn-sm btn-outline-primary">
                            Use
                        </a>
                    </div>
                </x-card>
            </div>
        @empty
            <div class="col-12">
                <x-card>
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text fs-1 text-muted d-block mb-3"></i>
                        <h5>No Templates Yet</h5>
                        <p class="text-muted mb-3">Create email templates to save time on repetitive emails</p>
                        <a href="{{ route('admin.communication.email-template-create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Create First Template
                        </a>
                    </div>
                </x-card>
            </div>
        @endforelse
    </div>

    @if(isset($templates) && $templates->hasPages())
        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
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
                <p>Are you sure you want to delete this template?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/admin/communication/email-templates/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
