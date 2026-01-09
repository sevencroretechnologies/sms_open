<?php

namespace App\Services;

use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Library Export Service
 * 
 * Prompt 424: Create Library Export Service
 * 
 * Handles exporting library data to various formats with filtering options.
 * Supports book inventory, issue records, and overdue reports.
 * 
 * Features:
 * - Export book inventory
 * - Export issue/return records
 * - Export overdue books report
 * - Export member borrowing history
 * - Support PDF, Excel, CSV formats
 */
class LibraryExportService
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export book inventory.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportBookInventory(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getBookInventoryData($filters);
        $filename = 'library_inventory_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Library Book Inventory');
    }

    /**
     * Export issue records.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportIssueRecords(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getIssueRecordsData($filters);
        $filename = 'library_issues_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Library Issue Records');
    }

    /**
     * Export overdue books report.
     *
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportOverdueBooks(string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getOverdueBooksData();
        $filename = 'overdue_books_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Overdue Books Report');
    }

    /**
     * Export currently issued books.
     *
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportCurrentlyIssued(string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getCurrentlyIssuedData();
        $filename = 'currently_issued_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Currently Issued Books');
    }

    /**
     * Export books by category.
     *
     * @param int|null $categoryId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportByCategory(?int $categoryId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters = $categoryId ? ['category_id' => $categoryId] : [];
        return $this->exportBookInventory($filters, $format);
    }

    /**
     * Export low stock books.
     *
     * @param int $threshold
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportLowStock(int $threshold = 2, string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getLowStockData($threshold);
        $filename = 'low_stock_books_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Low Stock Books Report');
    }

    /**
     * Get book inventory data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getBookInventoryData(array $filters = []): Collection
    {
        $query = LibraryBook::query()
            ->with(['category']);

        $this->applyBookFilters($query, $filters);

        return $query->orderBy('title')
            ->get()
            ->map(function ($book) {
                return [
                    'Book ID' => $book->id,
                    'ISBN' => $book->isbn ?? '',
                    'Title' => $book->title,
                    'Author' => $book->author ?? '',
                    'Publisher' => $book->publisher ?? '',
                    'Category' => $book->category?->name ?? '',
                    'Edition' => $book->edition ?? '',
                    'Publication Year' => $book->publication_year ?? '',
                    'Total Quantity' => $book->quantity ?? 0,
                    'Available' => $book->available_quantity ?? 0,
                    'Issued' => ($book->quantity ?? 0) - ($book->available_quantity ?? 0),
                    'Price' => number_format($book->price ?? 0, 2),
                    'Rack No' => $book->rack_number ?? '',
                    'Status' => $book->is_active ? 'Active' : 'Inactive',
                ];
            });
    }

    /**
     * Get issue records data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getIssueRecordsData(array $filters = []): Collection
    {
        $query = LibraryIssue::query()
            ->with(['book', 'member']);

        $this->applyIssueFilters($query, $filters);

        return $query->orderBy('issue_date', 'desc')
            ->get()
            ->map(function ($issue) {
                $isOverdue = !$issue->return_date && $issue->due_date && $issue->due_date->isPast();
                
                return [
                    'Issue ID' => $issue->id,
                    'Book Title' => $issue->book?->title ?? '',
                    'ISBN' => $issue->book?->isbn ?? '',
                    'Member Type' => ucfirst($issue->member?->member_type ?? ''),
                    'Member Name' => $issue->member?->name ?? '',
                    'Membership No' => $issue->member?->membership_number ?? '',
                    'Issue Date' => $issue->issue_date?->format('Y-m-d') ?? '',
                    'Due Date' => $issue->due_date?->format('Y-m-d') ?? '',
                    'Return Date' => $issue->return_date?->format('Y-m-d') ?? 'Not Returned',
                    'Status' => $issue->return_date ? 'Returned' : ($isOverdue ? 'Overdue' : 'Issued'),
                    'Fine Amount' => number_format($issue->fine_amount ?? 0, 2),
                    'Fine Paid' => $issue->fine_paid ? 'Yes' : 'No',
                ];
            });
    }

    /**
     * Get overdue books data.
     *
     * @return Collection
     */
    protected function getOverdueBooksData(): Collection
    {
        $issues = LibraryIssue::query()
            ->with(['book', 'member'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        return $issues->map(function ($issue) {
            $daysOverdue = $issue->due_date ? $issue->due_date->diffInDays(now()) : 0;
            
            return [
                'Book Title' => $issue->book?->title ?? '',
                'ISBN' => $issue->book?->isbn ?? '',
                'Member Type' => ucfirst($issue->member?->member_type ?? ''),
                'Member Name' => $issue->member?->name ?? '',
                'Membership No' => $issue->member?->membership_number ?? '',
                'Issue Date' => $issue->issue_date?->format('Y-m-d') ?? '',
                'Due Date' => $issue->due_date?->format('Y-m-d') ?? '',
                'Days Overdue' => $daysOverdue,
                'Fine Amount' => number_format($issue->fine_amount ?? 0, 2),
            ];
        });
    }

    /**
     * Get currently issued books data.
     *
     * @return Collection
     */
    protected function getCurrentlyIssuedData(): Collection
    {
        $issues = LibraryIssue::query()
            ->with(['book', 'member'])
            ->whereNull('return_date')
            ->orderBy('due_date')
            ->get();

        return $issues->map(function ($issue) {
            $isOverdue = $issue->due_date && $issue->due_date->isPast();
            $daysRemaining = $issue->due_date && !$isOverdue 
                ? now()->diffInDays($issue->due_date) 
                : 0;
            
            return [
                'Book Title' => $issue->book?->title ?? '',
                'ISBN' => $issue->book?->isbn ?? '',
                'Member Type' => ucfirst($issue->member?->member_type ?? ''),
                'Member Name' => $issue->member?->name ?? '',
                'Membership No' => $issue->member?->membership_number ?? '',
                'Issue Date' => $issue->issue_date?->format('Y-m-d') ?? '',
                'Due Date' => $issue->due_date?->format('Y-m-d') ?? '',
                'Days Remaining' => $isOverdue ? 'Overdue' : $daysRemaining,
                'Status' => $isOverdue ? 'Overdue' : 'Active',
            ];
        });
    }

    /**
     * Get low stock books data.
     *
     * @param int $threshold
     * @return Collection
     */
    protected function getLowStockData(int $threshold): Collection
    {
        $books = LibraryBook::query()
            ->with(['category'])
            ->where('available_quantity', '<=', $threshold)
            ->where('is_active', true)
            ->orderBy('available_quantity')
            ->get();

        return $books->map(function ($book) {
            return [
                'Book ID' => $book->id,
                'ISBN' => $book->isbn ?? '',
                'Title' => $book->title,
                'Author' => $book->author ?? '',
                'Category' => $book->category?->name ?? '',
                'Total Quantity' => $book->quantity ?? 0,
                'Available' => $book->available_quantity ?? 0,
                'Currently Issued' => ($book->quantity ?? 0) - ($book->available_quantity ?? 0),
                'Rack No' => $book->rack_number ?? '',
            ];
        });
    }

    /**
     * Apply filters to book query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyBookFilters($query, array $filters): void
    {
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
    }

    /**
     * Apply filters to issue query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyIssueFilters($query, array $filters): void
    {
        if (!empty($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (!empty($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'issued') {
                $query->whereNull('return_date');
            } elseif ($filters['status'] === 'returned') {
                $query->whereNotNull('return_date');
            } elseif ($filters['status'] === 'overdue') {
                $query->whereNull('return_date')->where('due_date', '<', now());
            }
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['date_to']);
        }
    }

    /**
     * Get export statistics.
     *
     * @return array
     */
    public function getExportStatistics(): array
    {
        $totalBooks = LibraryBook::count();
        $activeBooks = LibraryBook::where('is_active', true)->count();
        $totalIssued = LibraryIssue::whereNull('return_date')->count();
        $overdue = LibraryIssue::whereNull('return_date')->where('due_date', '<', now())->count();

        return [
            'total_books' => $totalBooks,
            'active_books' => $activeBooks,
            'currently_issued' => $totalIssued,
            'overdue' => $overdue,
        ];
    }
}
