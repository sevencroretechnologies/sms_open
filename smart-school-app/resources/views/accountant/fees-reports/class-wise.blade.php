@extends('layouts.app')

@section('title', 'Class-wise Summary')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.fees-reports.index') }}">Fee Reports</a></li>
            <li class="breadcrumb-item active">Class-wise Summary</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Class-wise Fee Summary</h1>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>

    <x-card title="Select Month">
        <form action="{{ route('accountant.fees-reports.class-wise') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Generate Report
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Summary for {{ \Carbon\Carbon::parse($month)->format('F Y') }}" class="mt-4">
        @if($classSummary->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Students</th>
                            <th class="text-end">Total Fees</th>
                            <th class="text-end">Collected (This Month)</th>
                            <th class="text-end">Pending</th>
                            <th class="text-end">Collection %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotalFees = 0;
                            $grandCollected = 0;
                            $grandPending = 0;
                        @endphp
                        @foreach($classSummary as $item)
                            @php
                                $grandTotalFees += $item['total_fees'];
                                $grandCollected += $item['collected'];
                                $grandPending += $item['pending'];
                                $percentage = $item['total_fees'] > 0 ? ($item['collected'] / $item['total_fees']) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $item['class']->name }}</td>
                                <td>{{ $item['class']->students_count }}</td>
                                <td class="text-end">{{ number_format($item['total_fees'], 2) }}</td>
                                <td class="text-end text-success">{{ number_format($item['collected'], 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($item['pending'], 2) }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <span>{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td>-</td>
                            <td class="text-end">{{ number_format($grandTotalFees, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($grandCollected, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($grandPending, 2) }}</td>
                            <td class="text-end">
                                @php
                                    $grandPercentage = $grandTotalFees > 0 ? ($grandCollected / $grandTotalFees) * 100 : 0;
                                @endphp
                                {{ number_format($grandPercentage, 1) }}%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <x-empty-state 
                icon="bi-bar-chart"
                title="No Data"
                description="No fee data available for the selected month."
            />
        @endif
    </x-card>
</div>
@endsection
