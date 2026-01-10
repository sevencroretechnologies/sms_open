@extends('layouts.app')

@section('title', 'Category-wise Report')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Category-wise</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Category-wise Report</h1>
        <a href="{{ route('librarian.reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <x-card title="Books by Category">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Total Books</th>
                            <th>Total Copies</th>
                            <th>Available</th>
                            <th>Issued</th>
                            <th>Availability %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $item)
                            @php
                                $availabilityPercent = $item['total_copies'] > 0 
                                    ? round(($item['available_copies'] / $item['total_copies']) * 100, 1) 
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('librarian.categories.show', $item['category']->id) }}" class="text-decoration-none fw-semibold">
                                        {{ $item['category']->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($item['category']->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item['total_books'] }}</td>
                                <td>{{ $item['total_copies'] }}</td>
                                <td class="text-success">{{ $item['available_copies'] }}</td>
                                <td class="text-warning">{{ $item['issued_copies'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $availabilityPercent >= 70 ? 'bg-success' : ($availabilityPercent >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $availabilityPercent }}%"
                                             aria-valuenow="{{ $availabilityPercent }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $availabilityPercent }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        @php
                            $totalBooks = $categories->sum('total_books');
                            $totalCopies = $categories->sum('total_copies');
                            $totalAvailable = $categories->sum('available_copies');
                            $totalIssued = $categories->sum('issued_copies');
                            $overallPercent = $totalCopies > 0 ? round(($totalAvailable / $totalCopies) * 100, 1) : 0;
                        @endphp
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td>-</td>
                            <td>{{ $totalBooks }}</td>
                            <td>{{ $totalCopies }}</td>
                            <td class="text-success">{{ $totalAvailable }}</td>
                            <td class="text-warning">{{ $totalIssued }}</td>
                            <td>{{ $overallPercent }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <x-empty-state 
                icon="bi-folder"
                title="No Categories Found"
                description="No book categories have been created yet."
            />
        @endif
    </x-card>
</div>
@endsection
