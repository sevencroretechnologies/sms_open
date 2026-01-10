@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.messages.index') }}">Messages</a></li>
                    <li class="breadcrumb-item active">Compose</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card title="Compose Message">
                <form action="{{ route('parent.messages.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="recipient_id" class="form-label">To (Teacher)</label>
                        <select class="form-select @error('recipient_id') is-invalid @enderror" id="recipient_id" name="recipient_id" required>
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                @if($teacher->user)
                                    <option value="{{ $teacher->user->id }}" {{ old('recipient_id') == $teacher->user->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">Message</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="8" required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('parent.messages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
