@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inbox</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Messages</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.messages.create') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Compose
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="list-group list-group-flush">
                    <a href="{{ route('teacher.messages.index') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-inbox me-2"></i> Inbox
                        @if($unreadCount > 0)
                            <span class="badge bg-danger float-end">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('teacher.messages.sent') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-send me-2"></i> Sent
                    </a>
                    <a href="{{ route('teacher.messages.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil-square me-2"></i> Compose
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <x-card class="mb-4">
                <form method="GET" action="{{ route('teacher.messages.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search messages..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </x-card>

            <x-card :noPadding="true">
                <div class="list-group list-group-flush">
                    @forelse($messages as $messageRecipient)
                        <a href="{{ route('teacher.messages.show', $messageRecipient->message_id) }}" 
                           class="list-group-item list-group-item-action {{ !$messageRecipient->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $messageRecipient->message->sender->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($messageRecipient->message->sender->name ?? 'U') . '&background=4f46e5&color=fff' }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1 {{ !$messageRecipient->is_read ? 'fw-bold' : '' }}">
                                            {{ $messageRecipient->message->subject ?? 'No Subject' }}
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            From: {{ $messageRecipient->message->sender->name ?? 'Unknown' }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            {{ Str::limit(strip_tags($messageRecipient->message->body ?? ''), 80) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">
                                        {{ $messageRecipient->created_at->diffForHumans() }}
                                    </small>
                                    @if(!$messageRecipient->is_read)
                                        <br><span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No Messages</h5>
                            <p class="text-muted mb-0">Your inbox is empty.</p>
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="card-footer">
                        {{ $messages->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
