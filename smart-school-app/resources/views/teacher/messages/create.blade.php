@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid" x-data="composeMessageManager()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Compose Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">Compose</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="row">
        <div class="col-lg-3 mb-4">
            <x-card>
                <div class="list-group list-group-flush">
                    <a href="{{ route('teacher.messages.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-inbox me-2"></i> Inbox
                    </a>
                    <a href="{{ route('teacher.messages.sent') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-send me-2"></i> Sent
                    </a>
                    <a href="{{ route('teacher.messages.create') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-pencil-square me-2"></i> Compose
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-lg-9">
            <form method="POST" action="{{ route('teacher.messages.store') }}">
                @csrf
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-pencil-square me-2"></i>New Message
                    </x-slot>

                    <div class="mb-3">
                        <label class="form-label">Recipients <span class="text-danger">*</span></label>
                        <div class="mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" @click="recipientType = 'students'" :class="{ 'active': recipientType === 'students' }">
                                    Students
                                </button>
                                <button type="button" class="btn btn-outline-primary" @click="recipientType = 'parents'" :class="{ 'active': recipientType === 'parents' }">
                                    Parents
                                </button>
                            </div>
                        </div>
                        
                        <div x-show="recipientType === 'students'">
                            <select name="recipient_ids[]" class="form-select" multiple size="6">
                                @foreach($students as $student)
                                    @if($student->user)
                                        <option value="{{ $student->user->id }}">
                                            {{ $student->user->name }} ({{ $student->schoolClass->display_name ?? '' }} - {{ $student->section->display_name ?? '' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple students</small>
                        </div>
                        
                        <div x-show="recipientType === 'parents'">
                            <select name="recipient_ids[]" class="form-select" multiple size="6">
                                @foreach($parents as $parent)
                                    @if($parent->user)
                                        <option value="{{ $parent->user->id }}">
                                            {{ $parent->user->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple parents</small>
                        </div>
                        
                        @error('recipient_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Enter message subject" required>
                        @error('subject')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="8" placeholder="Type your message here..." required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('teacher.messages.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Send Message
                        </button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function composeMessageManager() {
    return {
        recipientType: 'students'
    }
}
</script>
@endpush
