<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $statistics = $this->getStatistics();
        $todayTransactions = $this->getTodayTransactions();
        $recentIssues = $this->getRecentIssues();
        $overdueBooks = $this->getOverdueBooks();
        $popularBooks = $this->getPopularBooks();
        $chartData = $this->getChartData();

        return view('librarian.dashboard', compact(
            'statistics',
            'todayTransactions',
            'recentIssues',
            'overdueBooks',
            'popularBooks',
            'chartData'
        ));
    }

    protected function getStatistics(): array
    {
        $totalBooks = LibraryBook::sum('quantity');
        $totalTitles = LibraryBook::count();
        $availableBooks = LibraryBook::sum('available_quantity');
        $issuedBooks = $totalBooks - $availableBooks;

        $overdueCount = LibraryIssue::overdue()->count();

        $activeMembers = LibraryIssue::whereNull('return_date')
            ->distinct('member_id')
            ->count('member_id');

        $totalMembers = Student::count() + Teacher::count();

        return [
            'total_books' => $totalBooks,
            'total_titles' => $totalTitles,
            'available_books' => $availableBooks,
            'issued_books' => $issuedBooks,
            'overdue_count' => $overdueCount,
            'active_members' => $activeMembers,
            'total_members' => $totalMembers,
        ];
    }

    protected function getTodayTransactions(): array
    {
        $today = Carbon::today();

        $todayIssues = LibraryIssue::whereDate('issue_date', $today)->count();
        $todayReturns = LibraryIssue::whereDate('return_date', $today)->count();

        return [
            'issues' => $todayIssues,
            'returns' => $todayReturns,
            'total' => $todayIssues + $todayReturns,
        ];
    }

    protected function getRecentIssues()
    {
        return LibraryIssue::with(['book', 'member.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($issue) {
                $memberName = 'N/A';
                $memberClass = '';
                
                if ($issue->member) {
                    if ($issue->member->user) {
                        $memberName = $issue->member->user->name;
                    } elseif (method_exists($issue->member, 'first_name')) {
                        $memberName = $issue->member->first_name . ' ' . ($issue->member->last_name ?? '');
                    }
                    
                    if (isset($issue->member->schoolClass)) {
                        $memberClass = $issue->member->schoolClass->name ?? '';
                        if (isset($issue->member->section)) {
                            $memberClass .= '-' . ($issue->member->section->name ?? '');
                        }
                    }
                }

                return [
                    'id' => $issue->id,
                    'book_title' => $issue->book->title ?? 'N/A',
                    'book_author' => $issue->book->author ?? '',
                    'member_name' => $memberName,
                    'member_class' => $memberClass,
                    'issue_date' => $issue->issue_date,
                    'due_date' => $issue->due_date,
                    'return_date' => $issue->return_date,
                    'status' => $issue->status,
                    'is_overdue' => $issue->isOverdue(),
                ];
            });
    }

    protected function getOverdueBooks()
    {
        return LibraryIssue::with(['book', 'member.user'])
            ->overdue()
            ->orderBy('due_date')
            ->take(10)
            ->get()
            ->map(function ($issue) {
                $memberName = 'N/A';
                $memberClass = '';
                
                if ($issue->member) {
                    if ($issue->member->user) {
                        $memberName = $issue->member->user->name;
                    } elseif (method_exists($issue->member, 'first_name')) {
                        $memberName = $issue->member->first_name . ' ' . ($issue->member->last_name ?? '');
                    }
                    
                    if (isset($issue->member->schoolClass)) {
                        $memberClass = $issue->member->schoolClass->name ?? '';
                        if (isset($issue->member->section)) {
                            $memberClass .= '-' . ($issue->member->section->name ?? '');
                        }
                    }
                }

                $daysOverdue = $issue->due_date ? Carbon::parse($issue->due_date)->diffInDays(Carbon::now()) : 0;
                $fineAmount = $daysOverdue * 1;

                return [
                    'id' => $issue->id,
                    'book_title' => $issue->book->title ?? 'N/A',
                    'member_name' => $memberName,
                    'member_class' => $memberClass,
                    'due_date' => $issue->due_date,
                    'days_overdue' => $daysOverdue,
                    'fine_amount' => $fineAmount,
                    'urgency' => $daysOverdue > 14 ? 'danger' : ($daysOverdue > 7 ? 'warning' : 'info'),
                ];
            });
    }

    protected function getPopularBooks()
    {
        return LibraryBook::withCount('issues')
            ->orderBy('issues_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author ?? 'Unknown',
                    'category' => $book->category->name ?? 'Uncategorized',
                    'issue_count' => $book->issues_count,
                    'available' => $book->available_quantity,
                    'total' => $book->quantity,
                ];
            });
    }

    protected function getChartData(): array
    {
        return [
            'circulationTrend' => $this->getCirculationTrendChart(),
            'categoryDistribution' => $this->getCategoryDistributionChart(),
        ];
    }

    protected function getCirculationTrendChart(): array
    {
        $labels = [];
        $issuesData = [];
        $returnsData = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');
            
            $monthIssues = LibraryIssue::whereMonth('issue_date', $date->month)
                ->whereYear('issue_date', $date->year)
                ->count();
            
            $monthReturns = LibraryIssue::whereMonth('return_date', $date->month)
                ->whereYear('return_date', $date->year)
                ->count();
            
            $issuesData[] = $monthIssues;
            $returnsData[] = $monthReturns;
        }

        return [
            'labels' => $labels,
            'issues' => $issuesData,
            'returns' => $returnsData,
        ];
    }

    protected function getCategoryDistributionChart(): array
    {
        $categories = LibraryBook::select('category_id', DB::raw('SUM(quantity) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

        foreach ($categories as $index => $category) {
            $labels[] = $category->category->name ?? 'Uncategorized';
            $data[] = $category->total;
        }

        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [1];
            $colors = ['#e5e7eb'];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }
}
