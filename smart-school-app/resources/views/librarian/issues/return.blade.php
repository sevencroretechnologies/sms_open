@extends('layouts.app')

@section('title', 'Return Book')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.issues.index') }}">Book Issues</a></li>
            <li class="breadcrumb-item active">Return Book</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Return Book</h1>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <div class="row">
        <div class="col-md-8">
            <x-card title="Issue Details">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Book</p>
                        <p class="fw-semibold">{{ $issue->book->title ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted">Member</p>
                        <p class="fw-semibold">{{ $issue->member->user->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Issue Date</p>
                        <p class="fw-semibold">{{ $issue->issue_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Due Date</p>
                        <p class="fw-semibold {{ $issue->due_date < now() ? 'text-danger' : '' }}">
                            {{ $issue->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted">Days Overdue</p>
                        <p class="fw-semibold {{ $daysOverdue > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $daysOverdue > 0 ? $daysOverdue . ' days' : 'Not overdue' }}
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card title="Return Details" class="mt-4">
                <form action="{{ route('librarian.issues.processReturn', $issue->id) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Return Date <span class="text-danger">*</span></label>
                                <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', date('Y-m-d')) }}" required>
                                @error('return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Book Condition</label>
                                <select name="condition" class="form-select @error('condition') is-invalid @enderror">
                                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    <option value="lost" {{ old('condition') == 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-left me-1"></i> Process Return
                        </button>
                        <a href="{{ route('librarian.issues.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-md-4">
            @if($daysOverdue > 0)
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 opacity-75">Calculated Fine</h6>
                        <h3 class="card-title mb-0">${{ number_format($calculatedFine, 2) }}</h3>
                        <small class="opacity-75">{{ $daysOverdue }} days x $1.00/day</small>
                    </div>
                </div>
            @else
                <div class="card bg-success text-white mb-4">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 opacity-75">Status</h6>
                        <h4 class="card-title mb-0">On Time</h4>
                        <small class="opacity-75">No fine applicable</small>
                    </div>
                </div>
            @endif

            <x-card title="Fine Policy">
                <p class="mb-2">Late returns are subject to fines:</p>
                <ul class="mb-0">
                    <li>$1.00 per day overdue</li>
                    <li>Maximum fine: Book price</li>
                    <li>Lost books: Full replacement cost</li>
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
