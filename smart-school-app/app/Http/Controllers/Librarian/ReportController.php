<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryIssue;
use App\Models\LibraryMember;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * ReportController
 * 
 * Handles library reports for librarians.
 */
class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $totalBooks = LibraryBook::count();
        $totalCopies = LibraryBook::sum('quantity');
        $availableCopies = LibraryBook::sum('available_quantity');
        $issuedCopies = $totalCopies - $availableCopies;
        
        $totalMembers = LibraryMember::count();
        $activeMembers = LibraryMember::where('is_active', true)->count();
        
        $todayIssues = LibraryIssue::whereDate('issue_date', Carbon::today())->count();
        $todayReturns = LibraryIssue::whereDate('return_date', Carbon::today())->count();
        
        $overdueCount = LibraryIssue::whereNull('return_date')
            ->where('due_date', '<', now())
            ->count();
        
        $totalFines = LibraryIssue::sum('fine_amount');
        $unpaidFines = LibraryIssue::where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');
        
        return view('librarian.reports.index', compact(
            'totalBooks', 'totalCopies', 'availableCopies', 'issuedCopies',
            'totalMembers', 'activeMembers', 'todayIssues', 'todayReturns',
            'overdueCount', 'totalFines', 'unpaidFines'
        ));
    }

    /**
     * Display book inventory report.
     */
    public function inventory(Request $request)
    {
        $categories = LibraryCategory::where('is_active', true)->orderBy('name')->get();
        
        $query = LibraryBook::with('category');
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $books = $query->orderBy('title')->paginate(20);
        
        $summary = [
            'total_books' => LibraryBook::count(),
            'total_copies' => LibraryBook::sum('quantity'),
            'available_copies' => LibraryBook::sum('available_quantity'),
            'total_value' => LibraryBook::whereNotNull('price')->sum(\DB::raw('price * quantity')),
        ];
        
        return view('librarian.reports.inventory', compact('books', 'categories', 'summary'));
    }

    /**
     * Display issue/return report.
     */
    public function circulation(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        $issues = LibraryIssue::with(['book', 'member.user'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->orderBy('issue_date', 'desc')
            ->paginate(20);
        
        $summary = [
            'total_issues' => LibraryIssue::whereBetween('issue_date', [$startDate, $endDate])->count(),
            'total_returns' => LibraryIssue::whereBetween('return_date', [$startDate, $endDate])->count(),
            'overdue' => LibraryIssue::whereNull('return_date')
                ->where('due_date', '<', now())
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->count(),
        ];
        
        return view('librarian.reports.circulation', compact('issues', 'startDate', 'endDate', 'summary'));
    }

    /**
     * Display overdue report.
     */
    public function overdue()
    {
        $overdueIssues = LibraryIssue::with(['book', 'member.user'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->paginate(20);
        
        $totalOverdue = LibraryIssue::whereNull('return_date')
            ->where('due_date', '<', now())
            ->count();
        
        $totalFinesDue = $overdueIssues->sum(function ($issue) {
            return $issue->calculateFine(1.00);
        });
        
        return view('librarian.reports.overdue', compact('overdueIssues', 'totalOverdue', 'totalFinesDue'));
    }

    /**
     * Display fines report.
     */
    public function fines(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        $query = LibraryIssue::with(['book', 'member.user'])
            ->where('fine_amount', '>', 0);
        
        if ($request->status === 'paid') {
            $query->where('fine_paid', true);
        } elseif ($request->status === 'unpaid') {
            $query->where('fine_paid', false);
        }
        
        $fines = $query->orderBy('return_date', 'desc')->paginate(20);
        
        $summary = [
            'total_fines' => LibraryIssue::where('fine_amount', '>', 0)->sum('fine_amount'),
            'paid_fines' => LibraryIssue::where('fine_amount', '>', 0)->where('fine_paid', true)->sum('fine_amount'),
            'unpaid_fines' => LibraryIssue::where('fine_amount', '>', 0)->where('fine_paid', false)->sum('fine_amount'),
        ];
        
        return view('librarian.reports.fines', compact('fines', 'summary'));
    }

    /**
     * Display category-wise report.
     */
    public function categoryWise()
    {
        $categories = LibraryCategory::withCount('books')
            ->with(['books' => function ($q) {
                $q->select('category_id', \DB::raw('SUM(quantity) as total_copies'), \DB::raw('SUM(available_quantity) as available_copies'))
                  ->groupBy('category_id');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                $totalCopies = LibraryBook::where('category_id', $category->id)->sum('quantity');
                $availableCopies = LibraryBook::where('category_id', $category->id)->sum('available_quantity');
                
                return [
                    'category' => $category,
                    'total_books' => $category->books_count,
                    'total_copies' => $totalCopies,
                    'available_copies' => $availableCopies,
                    'issued_copies' => $totalCopies - $availableCopies,
                ];
            });
        
        return view('librarian.reports.category-wise', compact('categories'));
    }
}
