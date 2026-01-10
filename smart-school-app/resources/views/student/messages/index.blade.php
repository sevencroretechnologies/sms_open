@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Messages</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="d-grid gap-2">
                    <a href="{{ route('student.messages.index') }}" class="btn btn-primary">
                        <i class="fas fa-inbox me-2"></i>Inbox
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <x-card title="Inbox">
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search messages..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <div class="list-group">
                    @forelse($messages as $messageRecipient)
                        @php $message = $messageRecipient->message; @endphp
                        <a href="{{ route('student.messages.show', $message->id) }}" class="list-group-item list-group-item-action {{ !$messageRecipient->read_at ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    @if($message->sender && $message->sender->photo)
                                        <img src="{{ asset('storage/' . $message->sender->photo) }}" alt="" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <span class="text-white">{{ substr($message->sender->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1 {{ !$messageRecipient->read_at ? 'fw-bold' : '' }}">
                                            {{ $message->subject ?? 'No Subject' }}
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            From: {{ $message->sender->name ?? 'Unknown' }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            {{ Str::limit(strip_tags($message->body), 80) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">
                                        {{ $message->created_at ? $message->created_at->diffForHumans() : '' }}
                                    </small>
                                    @if(!$messageRecipient->read_at)
                                        <br><span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No messages in your inbox.</p>
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $messages->withQueryString()->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
