@extends('layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">Sent</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="d-grid gap-2">
                    <a href="{{ route('parent.messages.create') }}" class="btn btn-primary">
                        <i class="fas fa-pen me-2"></i>Compose
                    </a>
                    <hr>
                    <a href="{{ route('parent.messages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-inbox me-2"></i>Inbox
                    </a>
                    <a href="{{ route('parent.messages.sent') }}" class="btn btn-outline-primary">
                        <i class="fas fa-paper-plane me-2"></i>Sent
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <x-card title="Sent Messages">
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search messages..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <div class="list-group">
                    @forelse($messages as $message)
                        <a href="{{ route('parent.messages.show', $message->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $message->subject ?? 'No Subject' }}</h6>
                                    <p class="mb-1 text-muted small">
                                        To: 
                                        @if($message->recipients->count() > 0)
                                            {{ $message->recipients->first()->recipient->name ?? 'Unknown' }}
                                            @if($message->recipients->count() > 1)
                                                <span class="badge bg-secondary">+{{ $message->recipients->count() - 1 }} more</span>
                                            @endif
                                        @else
                                            Unknown
                                        @endif
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        {{ Str::limit(strip_tags($message->body), 80) }}
                                    </p>
                                </div>
                                <small class="text-muted">
                                    {{ $message->created_at ? $message->created_at->diffForHumans() : '' }}
                                </small>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No sent messages.</p>
                            <a href="{{ route('parent.messages.create') }}" class="btn btn-primary mt-3">
                                Compose Message
                            </a>
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
