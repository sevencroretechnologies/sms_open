@extends('layouts.app')

@section('title', 'View Message')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">View Message</li>
                </ol>
            </nav>
        </div>
    </div>

    @php $message = $messageRecipient->message; @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h4 class="mb-0">{{ $message->subject ?? 'No Subject' }}</h4>
                </div>

                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                    @if($message->sender && $message->sender->photo)
                        <img src="{{ asset('storage/' . $message->sender->photo) }}" alt="" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <span class="text-white fs-5">{{ substr($message->sender->name ?? 'U', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-0">{{ $message->sender->name ?? 'Unknown Sender' }}</h6>
                        <small class="text-muted">{{ $message->sender->email ?? '' }}</small>
                        <br>
                        <small class="text-muted">
                            {{ $message->created_at ? $message->created_at->format('d M Y h:i A') : '' }}
                        </small>
                    </div>
                </div>

                <hr>

                <div class="message-body py-3">
                    {!! nl2br(e($message->body)) !!}
                </div>

                @if($message->attachments && $message->attachments->count() > 0)
                    <hr>
                    <h6 class="mb-3">Attachments</h6>
                    <div class="list-group">
                        @foreach($message->attachments as $attachment)
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-file me-2"></i>
                                    {{ $attachment->original_name ?? 'Attachment' }}
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('parent.messages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Messages
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
