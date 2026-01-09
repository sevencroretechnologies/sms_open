<?php

namespace App\Services;

use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\LibraryMember;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Library Report Service
 * 
 * Prompt 429: Create Library Report Service
 * 
 * Generates library reports in PDF format.
 * Supports inventory, issue, and member reports.
 * 
 * Features:
 * - Generate book inventory report
 * - Generate issue/return report
 * - Generate overdue books report
 * - Generate member activity report
 * - Include library statistics
 */
class LibraryReportService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate book inventory report.
     *
     * @param array $filters
     * @return Response
     */
    public function generateInventoryReport(array $filters = []): Response
    {
        $books = $this->getInventoryData($filters);
        $statistics = $this->calculateInventoryStatistics($books);
        
        $html = $this->buildInventoryReportHtml($books, $statistics, $filters);
        $filename = 'library_inventory_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'landscape');
    }

    /**
     * Generate overdue books report.
     *
     * @return Response
     */
    public function generateOverdueReport(): Response
    {
        $overdueIssues = $this->getOverdueData();
        $statistics = $this->calculateOverdueStatistics($overdueIssues);
        
        $html = $this->buildOverdueReportHtml($overdueIssues, $statistics);
        $filename = 'overdue_books_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate issue/return report for date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Response
     */
    public function generateIssueReturnReport(string $startDate, string $endDate): Response
    {
        $issues = $this->getIssueReturnData($startDate, $endDate);
        $statistics = $this->calculateIssueReturnStatistics($issues);
        
        $html = $this->buildIssueReturnReportHtml($issues, $statistics, $startDate, $endDate);
        $filename = "library_issues_{$startDate}_to_{$endDate}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate member activity report.
     *
     * @param int|null $memberId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Response
     */
    public function generateMemberActivityReport(?int $memberId = null, ?string $startDate = null, ?string $endDate = null): Response
    {
        $data = $this->getMemberActivityData($memberId, $startDate, $endDate);
        
        $html = $this->buildMemberActivityReportHtml($data, $startDate, $endDate);
        $filename = 'member_activity_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate library summary report.
     *
     * @return Response
     */
    public function generateSummaryReport(): Response
    {
        $statistics = $this->getLibrarySummaryStatistics();
        
        $html = $this->buildSummaryReportHtml($statistics);
        $filename = 'library_summary_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Get inventory data.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getInventoryData(array $filters = []): Collection
    {
        $query = LibraryBook::with(['category']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('title')->get();
    }

    /**
     * Get overdue data.
     *
     * @return Collection
     */
    protected function getOverdueData(): Collection
    {
        return LibraryIssue::with(['book', 'member'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get issue/return data for date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    protected function getIssueReturnData(string $startDate, string $endDate): Collection
    {
        return LibraryIssue::with(['book', 'member'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('issue_date', [$startDate, $endDate])
                  ->orWhereBetween('return_date', [$startDate, $endDate]);
            })
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    /**
     * Get member activity data.
     *
     * @param int|null $memberId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    protected function getMemberActivityData(?int $memberId, ?string $startDate, ?string $endDate): Collection
    {
        $query = LibraryMember::with(['issues.book']);

        if ($memberId) {
            $query->where('id', $memberId);
        }

        $members = $query->get();

        return $members->map(function ($member) use ($startDate, $endDate) {
            $issueQuery = LibraryIssue::where('member_id', $member->id);
            
            if ($startDate) {
                $issueQuery->whereDate('issue_date', '>=', $startDate);
            }
            if ($endDate) {
                $issueQuery->whereDate('issue_date', '<=', $endDate);
            }

            $issues = $issueQuery->with('book')->get();
            $totalIssued = $issues->count();
            $returned = $issues->whereNotNull('return_date')->count();
            $currentlyIssued = $issues->whereNull('return_date')->count();
            $overdue = $issues->whereNull('return_date')->where('due_date', '<', now())->count();
            $totalFine = $issues->sum('fine_amount');

            return [
                'member' => $member,
                'total_issued' => $totalIssued,
                'returned' => $returned,
                'currently_issued' => $currentlyIssued,
                'overdue' => $overdue,
                'total_fine' => $totalFine,
                'issues' => $issues,
            ];
        });
    }

    /**
     * Calculate inventory statistics.
     *
     * @param Collection $books
     * @return array
     */
    protected function calculateInventoryStatistics(Collection $books): array
    {
        $totalBooks = $books->count();
        $totalQuantity = $books->sum('quantity');
        $totalAvailable = $books->sum('available_quantity');
        $totalIssued = $totalQuantity - $totalAvailable;
        $totalValue = $books->sum(fn($b) => ($b->price ?? 0) * ($b->quantity ?? 0));

        return [
            'total_titles' => $totalBooks,
            'total_quantity' => $totalQuantity,
            'total_available' => $totalAvailable,
            'total_issued' => $totalIssued,
            'total_value' => number_format($totalValue, 2),
        ];
    }

    /**
     * Calculate overdue statistics.
     *
     * @param Collection $overdueIssues
     * @return array
     */
    protected function calculateOverdueStatistics(Collection $overdueIssues): array
    {
        $totalOverdue = $overdueIssues->count();
        $totalFine = $overdueIssues->sum('fine_amount');
        $avgDaysOverdue = $overdueIssues->avg(fn($i) => $i->due_date ? $i->due_date->diffInDays(now()) : 0);

        return [
            'total_overdue' => $totalOverdue,
            'total_fine' => number_format($totalFine, 2),
            'avg_days_overdue' => round($avgDaysOverdue ?? 0, 1),
        ];
    }

    /**
     * Calculate issue/return statistics.
     *
     * @param Collection $issues
     * @return array
     */
    protected function calculateIssueReturnStatistics(Collection $issues): array
    {
        $totalIssued = $issues->count();
        $returned = $issues->whereNotNull('return_date')->count();
        $pending = $issues->whereNull('return_date')->count();
        $totalFine = $issues->sum('fine_amount');

        return [
            'total_issued' => $totalIssued,
            'returned' => $returned,
            'pending' => $pending,
            'total_fine' => number_format($totalFine, 2),
        ];
    }

    /**
     * Get library summary statistics.
     *
     * @return array
     */
    protected function getLibrarySummaryStatistics(): array
    {
        $totalBooks = LibraryBook::count();
        $activeBooks = LibraryBook::where('is_active', true)->count();
        $totalQuantity = LibraryBook::sum('quantity');
        $totalAvailable = LibraryBook::sum('available_quantity');
        $totalMembers = LibraryMember::count();
        $activeMembers = LibraryMember::where('is_active', true)->count();
        $currentlyIssued = LibraryIssue::whereNull('return_date')->count();
        $overdue = LibraryIssue::whereNull('return_date')->where('due_date', '<', now())->count();
        $totalFineCollected = LibraryIssue::where('fine_paid', true)->sum('fine_amount');
        $pendingFine = LibraryIssue::where('fine_paid', false)->sum('fine_amount');

        $thisMonthIssued = LibraryIssue::whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->count();
        $thisMonthReturned = LibraryIssue::whereMonth('return_date', now()->month)
            ->whereYear('return_date', now()->year)
            ->count();

        return [
            'total_books' => $totalBooks,
            'active_books' => $activeBooks,
            'total_quantity' => $totalQuantity,
            'total_available' => $totalAvailable,
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'currently_issued' => $currentlyIssued,
            'overdue' => $overdue,
            'total_fine_collected' => number_format($totalFineCollected, 2),
            'pending_fine' => number_format($pendingFine, 2),
            'this_month_issued' => $thisMonthIssued,
            'this_month_returned' => $thisMonthReturned,
        ];
    }

    /**
     * Build inventory report HTML.
     *
     * @param Collection $books
     * @param array $statistics
     * @param array $filters
     * @return string
     */
    protected function buildInventoryReportHtml(Collection $books, array $statistics, array $filters): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($books as $book) {
            $issued = ($book->quantity ?? 0) - ($book->available_quantity ?? 0);
            $statusClass = $book->is_active ? 'status-active' : 'status-inactive';
            $statusText = $book->is_active ? 'Active' : 'Inactive';
            $categoryName = $book->category?->name ?? '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$book->isbn}</td>
    <td>{$book->title}</td>
    <td>{$book->author}</td>
    <td>{$categoryName}</td>
    <td class="text-center">{$book->quantity}</td>
    <td class="text-center">{$book->available_quantity}</td>
    <td class="text-center">{$issued}</td>
    <td class="text-right">{$book->price}</td>
    <td>{$book->rack_number}</td>
    <td class="text-center {$statusClass}">{$statusText}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Inventory Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; padding: 15px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; color: #4f46e5; }
        .header h2 { font-size: 14px; color: #333; margin-top: 5px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; padding: 5px 15px; }
        .stat-value { font-size: 14px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 8px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 6px 4px; text-align: left; font-size: 8px; }
        td { padding: 5px 4px; border: 1px solid #ddd; font-size: 8px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Library Inventory Report</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_titles']}</div>
            <div class="stat-label">Total Titles</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_quantity']}</div>
            <div class="stat-label">Total Books</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_available']}</div>
            <div class="stat-label">Available</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_issued']}</div>
            <div class="stat-label">Issued</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">₹{$statistics['total_value']}</div>
            <div class="stat-label">Total Value</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Avail</th>
                <th class="text-center">Issued</th>
                <th class="text-right">Price</th>
                <th>Rack</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build overdue report HTML.
     *
     * @param Collection $overdueIssues
     * @param array $statistics
     * @return string
     */
    protected function buildOverdueReportHtml(Collection $overdueIssues, array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($overdueIssues as $issue) {
            $daysOverdue = $issue->due_date ? $issue->due_date->diffInDays(now()) : 0;
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$issue->book?->title}</td>
    <td>{$issue->book?->isbn}</td>
    <td>{$issue->member?->name}</td>
    <td>{$issue->member?->membership_number}</td>
    <td>{$issue->issue_date?->format('Y-m-d')}</td>
    <td>{$issue->due_date?->format('Y-m-d')}</td>
    <td class="text-center text-danger">{$daysOverdue}</td>
    <td class="text-right">₹{$issue->fine_amount}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Overdue Books Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #dc3545; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #dc3545; margin-top: 10px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #f8d7da; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #dc3545; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #dc3545; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>OVERDUE BOOKS REPORT</h2>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_overdue']}</div>
            <div class="stat-label">Total Overdue</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">₹{$statistics['total_fine']}</div>
            <div class="stat-label">Total Fine</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['avg_days_overdue']}</div>
            <div class="stat-label">Avg Days Overdue</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Book Title</th>
                <th>ISBN</th>
                <th>Member Name</th>
                <th>Member ID</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th class="text-center">Days Overdue</th>
                <th class="text-right">Fine</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build issue/return report HTML.
     *
     * @param Collection $issues
     * @param array $statistics
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    protected function buildIssueReturnReportHtml(Collection $issues, array $statistics, string $startDate, string $endDate): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($issues as $issue) {
            $status = $issue->return_date ? 'Returned' : 'Issued';
            $statusClass = $issue->return_date ? 'status-returned' : 'status-issued';
            $bookTitle = $issue->book?->title ?? '';
            $memberName = $issue->member?->name ?? '';
            $issueDate = $issue->issue_date?->format('Y-m-d') ?? '';
            $dueDate = $issue->due_date?->format('Y-m-d') ?? '';
            $returnDate = $issue->return_date?->format('Y-m-d') ?? '-';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$bookTitle}</td>
    <td>{$memberName}</td>
    <td>{$issueDate}</td>
    <td>{$dueDate}</td>
    <td>{$returnDate}</td>
    <td class="text-center {$statusClass}">{$status}</td>
    <td class="text-right">₹{$issue->fine_amount}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Issue/Return Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-returned { color: #28a745; font-weight: bold; }
        .status-issued { color: #ffc107; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Library Issue/Return Report</h2>
        <p style="margin-top: 5px; color: #666;">Period: {$startDate} to {$endDate}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_issued']}</div>
            <div class="stat-label">Total Transactions</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['returned']}</div>
            <div class="stat-label">Returned</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #ffc107;">{$statistics['pending']}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">₹{$statistics['total_fine']}</div>
            <div class="stat-label">Total Fine</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Book Title</th>
                <th>Member</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th class="text-center">Status</th>
                <th class="text-right">Fine</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build member activity report HTML.
     *
     * @param Collection $data
     * @param string|null $startDate
     * @param string|null $endDate
     * @return string
     */
    protected function buildMemberActivityReportHtml(Collection $data, ?string $startDate, ?string $endDate): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');
        $dateRange = $startDate && $endDate ? "{$startDate} to {$endDate}" : 'All Time';

        $rows = '';
        $sn = 1;
        foreach ($data as $item) {
            $member = $item['member'];
            $overdueClass = $item['overdue'] > 0 ? 'text-danger' : '';
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$member->membership_number}</td>
    <td>{$member->name}</td>
    <td>{$member->member_type}</td>
    <td class="text-center">{$item['total_issued']}</td>
    <td class="text-center">{$item['returned']}</td>
    <td class="text-center">{$item['currently_issued']}</td>
    <td class="text-center {$overdueClass}">{$item['overdue']}</td>
    <td class="text-right">₹{$item['total_fine']}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Member Activity Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Member Activity Report</h2>
        <p style="margin-top: 5px; color: #666;">Period: {$dateRange}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Member ID</th>
                <th>Name</th>
                <th>Type</th>
                <th class="text-center">Total Issued</th>
                <th class="text-center">Returned</th>
                <th class="text-center">Current</th>
                <th class="text-center">Overdue</th>
                <th class="text-right">Fine</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build summary report HTML.
     *
     * @param array $statistics
     * @return string
     */
    protected function buildSummaryReportHtml(array $statistics): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y \a\t g:i A');
        $monthName = now()->format('F Y');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Summary Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: #4f46e5; }
        .header h2 { font-size: 18px; color: #333; margin-top: 10px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 14px; color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; margin-bottom: 15px; }
        .stats-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .stat-card { width: 30%; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #4f46e5; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-label { font-size: 10px; color: #666; margin-bottom: 5px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #333; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Library Summary Report</h2>
        <p style="margin-top: 5px; color: #666;">As of {$generatedAt}</p>
    </div>

    <div class="section">
        <h3 class="section-title">Book Collection</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Book Titles</div>
                <div class="stat-value">{$statistics['total_books']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Books</div>
                <div class="stat-value">{$statistics['active_books']}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Copies</div>
                <div class="stat-value">{$statistics['total_quantity']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Available Copies</div>
                <div class="stat-value">{$statistics['total_available']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Membership</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Members</div>
                <div class="stat-value">{$statistics['total_members']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Active Members</div>
                <div class="stat-value">{$statistics['active_members']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Circulation Status</h3>
        <div class="stats-grid">
            <div class="stat-card warning">
                <div class="stat-label">Currently Issued</div>
                <div class="stat-value">{$statistics['currently_issued']}</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-label">Overdue Books</div>
                <div class="stat-value">{$statistics['overdue']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">This Month ({$monthName})</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Books Issued</div>
                <div class="stat-value">{$statistics['this_month_issued']}</div>
            </div>
            <div class="stat-card success">
                <div class="stat-label">Books Returned</div>
                <div class="stat-value">{$statistics['this_month_returned']}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Fine Collection</h3>
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-label">Total Fine Collected</div>
                <div class="stat-value">₹{$statistics['total_fine_collected']}</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-label">Pending Fine</div>
                <div class="stat-value">₹{$statistics['pending_fine']}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Generated by {$schoolName} Management System</p>
    </div>
</body>
</html>
HTML;
    }
}
