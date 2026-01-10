@extends('layouts.app')

@section('title', 'View Sent Message')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.messages.sent') }}">Sent Messages</a></li>
                    <li class="breadcrumb-item active">View Message</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h4 class="mb-0">{{ $message->subject ?? 'No Subject' }}</h4>
                    <span class="badge bg-info">Sent</span>
                </div>

                <div class="mb-4 p-3 bg-light rounded">
                    <div class="mb-2">
                        <strong>To:</strong>
                        @foreach($message->recipients as $recipient)
                            <span class="badge bg-secondary">{{ $recipient->recipient->name ?? 'Unknown' }}</span>
                        @endforeach
                    </div>
                    <small class="text-muted">
                        Sent on {{ $message->created_at ? $message->created_at->format('d M Y h:i A') : '' }}
                    </small>
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
                    <a href="{{ route('parent.messages.sent') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Sent Messages
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
