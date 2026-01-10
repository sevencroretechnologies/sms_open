@extends('layouts.app')

@section('title', 'Librarian Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Library Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'Librarian' }}!</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success fs-6 me-2">
                <i class="bi bi-arrow-left-right me-1"></i>Today: {{ $todayTransactions['issues'] ?? 0 }} issued, {{ $todayTransactions['returns'] ?? 0 }} returned
            </span>
            <span class="badge bg-primary fs-6">
                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Library Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Books</p>
                            <h3 class="mb-0 text-primary">{{ number_format($statistics['total_books'] ?? 0) }}</h3>
                            <small class="text-muted">{{ number_format($statistics['total_titles'] ?? 0) }} titles</small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Books Issued</p>
                            <h3 class="mb-0 text-success">{{ number_format($statistics['issued_books'] ?? 0) }}</h3>
                            <small class="text-muted">{{ number_format($statistics['available_books'] ?? 0) }} available</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-bookmark-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Overdue Books</p>
                            <h3 class="mb-0 text-warning">{{ number_format($statistics['overdue_count'] ?? 0) }}</h3>
                            <small class="text-warning"><i class="bi bi-exclamation-circle"></i> Need attention</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stat-card h-100 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Active Members</p>
                            <h3 class="mb-0 text-info">{{ number_format($statistics['active_members'] ?? 0) }}</h3>
                            <small class="text-muted">of {{ number_format($statistics['total_members'] ?? 0) }} total</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-auto">
                            <a href="{{ route('librarian.issues.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Issue Book
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('librarian.issues.index') }}" class="btn btn-success">
                                <i class="bi bi-arrow-return-left me-2"></i>Return Book
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('librarian.books.create') }}" class="btn btn-info text-white">
                                <i class="bi bi-book me-2"></i>Add New Book
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('librarian.members.create') }}" class="btn btn-warning">
                                <i class="bi bi-person-plus me-2"></i>Add Member
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('librarian.books.index') }}" class="btn btn-secondary">
                                <i class="bi bi-search me-2"></i>Search Catalog
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Book Circulation Trend</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active">Monthly</button>
                        <button class="btn btn-outline-primary">Weekly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="circulationChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Books by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Issues & Overdue Books -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Issues</h6>
                    <a href="{{ route('librarian.issues.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Book Title</th>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIssues ?? [] as $issue)
                                    <tr>
                                        <td><strong>{{ $issue['book_title'] }}</strong></td>
                                        <td>{{ $issue['member_name'] }}{{ $issue['member_class'] ? ' (' . $issue['member_class'] . ')' : '' }}</td>
                                        <td>{{ $issue['issue_date'] ? \Carbon\Carbon::parse($issue['issue_date'])->format('M d') : 'N/A' }}</td>
                                        <td>{{ $issue['due_date'] ? \Carbon\Carbon::parse($issue['due_date'])->format('M d') : 'N/A' }}</td>
                                        <td>
                                            @if($issue['return_date'])
                                                <span class="badge bg-success">Returned</span>
                                            @elseif($issue['is_overdue'])
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-primary">Issued</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-book fs-3 d-block mb-2"></i>
                                            No recent issues
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Overdue Books</h6>
                    <span class="badge bg-danger">{{ count($overdueBooks ?? []) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($overdueBooks ?? [] as $overdue)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-1">{{ $overdue['book_title'] }}</h6>
                                    <small class="text-muted">{{ $overdue['member_name'] }}{{ $overdue['member_class'] ? ' (' . $overdue['member_class'] . ')' : '' }}</small>
                                    <small class="d-block text-{{ $overdue['urgency'] }}">Overdue by {{ $overdue['days_overdue'] }} days</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $overdue['urgency'] }}">Fine: {{ number_format($overdue['fine_amount']) }}</span>
                                    <a href="{{ route('librarian.issues.index') }}" class="btn btn-sm btn-outline-primary d-block mt-1">Notify</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4 text-muted">
                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                No overdue books
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Books -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-star me-2"></i>Popular Books</h6>
                    <a href="{{ route('librarian.books.index') }}" class="btn btn-sm btn-link">View All Books</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Times Issued</th>
                                    <th>Availability</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularBooks ?? [] as $book)
                                    <tr>
                                        <td><strong>{{ $book['title'] }}</strong></td>
                                        <td>{{ $book['author'] }}</td>
                                        <td><span class="badge bg-secondary">{{ $book['category'] }}</span></td>
                                        <td><span class="badge bg-primary">{{ $book['issue_count'] }}</span></td>
                                        <td>
                                            @if($book['available'] > 0)
                                                <span class="text-success">{{ $book['available'] }}/{{ $book['total'] }} available</span>
                                            @else
                                                <span class="text-danger">Not available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-book fs-3 d-block mb-2"></i>
                                            No books in library
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const circulationTrend = @json($chartData['circulationTrend'] ?? ['labels' => [], 'issues' => [], 'returns' => []]);
    const categoryDistribution = @json($chartData['categoryDistribution'] ?? ['labels' => [], 'data' => [], 'colors' => []]);

    const circCtx = document.getElementById('circulationChart');
    if (circCtx) {
        new Chart(circCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: circulationTrend.labels,
                datasets: [{
                    label: 'Books Issued',
                    data: circulationTrend.issues,
                    backgroundColor: '#4f46e5'
                }, {
                    label: 'Books Returned',
                    data: circulationTrend.returns,
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryDistribution.labels,
                datasets: [{
                    data: categoryDistribution.data,
                    backgroundColor: categoryDistribution.colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
</script>
@endpush
@endsection
