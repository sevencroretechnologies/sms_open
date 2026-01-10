@extends('layouts.app')

@section('title', 'View Message')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Message Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <a href="{{ $isInbox ? route('teacher.messages.index') : route('teacher.messages.sent') }}" class="btn btn-outline-secondary mt-3 mt-md-0">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="list-group list-group-flush">
                    <a href="{{ route('teacher.messages.index') }}" class="list-group-item list-group-item-action {{ $isInbox ? 'active' : '' }}">
                        <i class="bi bi-inbox me-2"></i> Inbox
                    </a>
                    <a href="{{ route('teacher.messages.sent') }}" class="list-group-item list-group-item-action {{ !$isInbox ? 'active' : '' }}">
                        <i class="bi bi-send me-2"></i> Sent
                    </a>
                    <a href="{{ route('teacher.messages.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-pencil-square me-2"></i> Compose
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ $message->sender->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($message->sender->name ?? 'U') . '&background=4f46e5&color=fff' }}" 
                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">{{ $message->sender->name ?? 'Unknown' }}</h6>
                            <small class="text-muted">{{ $message->sender->email ?? '' }}</small>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        {{ $message->created_at->format('d M Y, h:i A') }}
                    </small>
                </div>

                <hr>

                <h4 class="mb-3">{{ $message->subject ?? 'No Subject' }}</h4>

                @if(!$isInbox && $message->recipients)
                    <div class="mb-3">
                        <small class="text-muted">To: </small>
                        @foreach($message->recipients as $recipient)
                            <span class="badge bg-light text-dark me-1">{{ $recipient->recipient->name ?? 'Unknown' }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="message-body py-3">
                    {!! nl2br(e($message->body)) !!}
                </div>

                @if($message->attachments && $message->attachments->count() > 0)
                    <hr>
                    <h6 class="mb-3"><i class="bi bi-paperclip me-1"></i> Attachments</h6>
                    <div class="list-group">
                        @foreach($message->attachments as $attachment)
                            <a href="{{ Storage::url($attachment->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                <span>
                                    <i class="bi bi-file-earmark me-2"></i>
                                    {{ $attachment->original_name ?? 'Attachment' }}
                                </span>
                                <i class="bi bi-download"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <hr>

                <div class="d-flex gap-2">
                    @if($isInbox)
                        <a href="{{ route('teacher.messages.create') }}?reply_to={{ $message->sender_id }}" class="btn btn-primary">
                            <i class="bi bi-reply me-1"></i> Reply
                        </a>
                    @endif
                    <a href="{{ $isInbox ? route('teacher.messages.index') : route('teacher.messages.sent') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
