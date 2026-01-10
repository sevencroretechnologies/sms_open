@extends('layouts.app')

@section('title', 'My Issued Books')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.library.index') }}">Library</a></li>
                    <li class="breadcrumb-item active">My Issued Books</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('student.library.index') }}">Browse Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('student.library.issued') }}">My Issued Books</a>
                </li>
            </ul>
        </div>
    </div>

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Fine</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issuedBooks as $issue)
                        @php
                            $isOverdue = !$issue->return_date && $issue->due_date && $issue->due_date->isPast();
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($issue->book && $issue->book->cover_image)
                                        <img src="{{ asset('storage/' . $issue->book->cover_image) }}" alt="" class="me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 60px;">
                                            <i class="fas fa-book text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $issue->book->title ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $issue->book->author ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $issue->issue_date ? $issue->issue_date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                {{ $issue->due_date ? $issue->due_date->format('d M Y') : 'N/A' }}
                                @if($isOverdue)
                                    <br><small class="text-danger">Overdue</small>
                                @endif
                            </td>
                            <td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
                            <td>
                                @if($issue->return_date)
                                    <span class="badge bg-success">Returned</span>
                                @elseif($isOverdue)
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-primary">Issued</span>
                                @endif
                            </td>
                            <td>
                                @if($issue->fine_amount && $issue->fine_amount > 0)
                                    <span class="text-danger">{{ number_format($issue->fine_amount, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-0">You haven't borrowed any books yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($issuedBooks->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $issuedBooks->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
