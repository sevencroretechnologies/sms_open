@extends('layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Sent Messages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">Sent</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('teacher.messages.create') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Compose
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="list-group list-group-flush">
                    <a href="{{ route('teacher.messages.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-inbox me-2"></i> Inbox
                    </a>
                    <a href="{{ route('teacher.messages.sent') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-send me-2"></i> Sent
                    </a>
                    <a href="{{ route('teacher.messages.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil-square me-2"></i> Compose
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <x-card :noPadding="true">
                <div class="list-group list-group-flush">
                    @forelse($messages as $message)
                        <a href="{{ route('teacher.messages.show', $message->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $message->subject ?? 'No Subject' }}</h6>
                                    <p class="mb-1 text-muted small">
                                        To: 
                                        @foreach($message->recipients->take(3) as $recipient)
                                            {{ $recipient->recipient->name ?? 'Unknown' }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        @if($message->recipients->count() > 3)
                                            <span class="text-primary">+{{ $message->recipients->count() - 3 }} more</span>
                                        @endif
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        {{ Str::limit(strip_tags($message->body ?? ''), 80) }}
                                    </p>
                                </div>
                                <small class="text-muted">
                                    {{ $message->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-send fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No Sent Messages</h5>
                            <p class="text-muted mb-0">You haven't sent any messages yet.</p>
                            <a href="{{ route('teacher.messages.create') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-pencil-square me-1"></i> Compose Message
                            </a>
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
