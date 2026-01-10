@extends('layouts.app')

@section('title', 'Fees - ' . ($child->user->name ?? 'Child'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.index') }}">My Children</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.children.show', $child->id) }}">{{ $child->user->name ?? 'Child' }}</a></li>
                    <li class="breadcrumb-item active">Fees</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-primary">{{ number_format($summary['total_fees'], 2) }}</h3>
                    <small class="text-muted">Total Fees</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 text-success">{{ number_format($summary['paid'], 2) }}</h3>
                    <small class="text-muted">Paid Amount</small>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <div class="text-center">
                    <h3 class="mb-0 {{ $summary['balance'] > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($summary['balance'], 2) }}</h3>
                    <small class="text-muted">Balance Due</small>
                </div>
            </x-card>
        </div>
    </div>

    <x-card>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">{{ $child->user->name ?? 'Child' }}'s Fee Details</h5>
            <span class="badge bg-primary">{{ $child->schoolClass->name ?? '' }} - {{ $child->section->name ?? '' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fee Group</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allotments as $allotment)
                        <tr>
                            <td>
                                <strong>{{ $allotment->feeGroup->name ?? 'N/A' }}</strong>
                                @if($allotment->feesMasters && $allotment->feesMasters->count() > 0)
                                    <br>
                                    <small class="text-muted">
                                        {{ $allotment->feesMasters->pluck('feeType.name')->filter()->implode(', ') }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $allotment->due_date ? $allotment->due_date->format('d M Y') : 'N/A' }}</td>
                            <td>{{ number_format($allotment->total_amount, 2) }}</td>
                            <td class="text-success">{{ number_format($allotment->paid_amount, 2) }}</td>
                            <td class="{{ $allotment->balance > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($allotment->balance, 2) }}
                            </td>
                            <td>
                                @if($allotment->balance <= 0)
                                    <span class="badge bg-success">Paid</span>
                                @elseif($allotment->paid_amount > 0)
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-0">No fee allotments found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-4">
        <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Profile
        </a>
    </div>
</div>
@endsection
