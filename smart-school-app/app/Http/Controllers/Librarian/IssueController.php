<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryIssue;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * IssueController
 * 
 * Handles book issue/return for librarians.
 */
class IssueController extends Controller
{
    /**
     * Display issues list.
     */
    public function index(Request $request)
    {
        $query = LibraryIssue::with(['book', 'member.user', 'issuedBy']);
        
        if ($request->status) {
            if ($request->status === 'issued') {
                $query->whereNull('return_date');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('return_date');
            } elseif ($request->status === 'overdue') {
                $query->whereNull('return_date')->where('due_date', '<', now());
            }
        }
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('book', fn($b) => $b->where('title', 'like', "%{$request->search}%"))
                  ->orWhereHas('member.user', fn($m) => $m->where('name', 'like', "%{$request->search}%"));
            });
        }
        
        $issues = $query->orderBy('issue_date', 'desc')->paginate(20);
        
        return view('librarian.issues.index', compact('issues'));
    }

    /**
     * Show issue book form.
     */
    public function create()
    {
        $books = LibraryBook::where('is_active', true)
            ->where('available_quantity', '>', 0)
            ->orderBy('title')
            ->get();
        
        $students = Student::with('user')->get();
        $teachers = Teacher::with('user')->get();
        
        return view('librarian.issues.create', compact('books', 'students', 'teachers'));
    }

    /**
     * Issue a book.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:library_books,id',
            'member_type' => 'required|in:student,teacher',
            'member_id' => 'required|integer',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $book = LibraryBook::findOrFail($request->book_id);
        
        if (!$book->isAvailable()) {
            return back()->with('error', 'This book is not available for issue.');
        }

        $member = LibraryMember::where('member_type', $request->member_type)
            ->where('member_id', $request->member_id)
            ->first();
        
        if (!$member) {
            $member = LibraryMember::create([
                'member_type' => $request->member_type,
                'member_id' => $request->member_id,
                'user_id' => $request->member_type === 'student' 
                    ? Student::find($request->member_id)->user_id 
                    : Teacher::find($request->member_id)->user_id,
                'is_active' => true,
            ]);
        }

        DB::beginTransaction();
        try {
            LibraryIssue::create([
                'book_id' => $request->book_id,
                'member_id' => $member->id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'remarks' => $request->remarks,
                'issued_by' => Auth::id(),
            ]);

            $book->issue();

            DB::commit();
            return redirect()->route('librarian.issues.index')->with('success', 'Book issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to issue book. Please try again.');
        }
    }

    /**
     * Show issue details.
     */
    public function show($id)
    {
        $issue = LibraryIssue::with(['book', 'member.user', 'issuedBy', 'returnedBy'])->findOrFail($id);
        return view('librarian.issues.show', compact('issue'));
    }

    /**
     * Show return book form.
     */
    public function returnForm($id)
    {
        $issue = LibraryIssue::with(['book', 'member.user'])->findOrFail($id);
        
        if ($issue->isReturned()) {
            return redirect()->route('librarian.issues.index')->with('error', 'This book has already been returned.');
        }
        
        $finePerDay = 1.00;
        $calculatedFine = $issue->calculateFine($finePerDay);
        
        return view('librarian.issues.return', compact('issue', 'calculatedFine', 'finePerDay'));
    }

    /**
     * Process book return.
     */
    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'fine_amount' => 'nullable|numeric|min:0',
            'fine_paid' => 'boolean',
            'remarks' => 'nullable|string|max:500',
        ]);

        $issue = LibraryIssue::findOrFail($id);
        
        if ($issue->isReturned()) {
            return redirect()->route('librarian.issues.index')->with('error', 'This book has already been returned.');
        }

        DB::beginTransaction();
        try {
            $issue->return_date = now();
            $issue->returned_by = Auth::id();
            $issue->fine_amount = $request->fine_amount ?? 0;
            $issue->fine_paid = $request->boolean('fine_paid', false);
            
            if ($request->remarks) {
                $issue->remarks = ($issue->remarks ? $issue->remarks . "\n" : '') . "Return: " . $request->remarks;
            }
            
            $issue->save();
            $issue->book->return();

            DB::commit();
            return redirect()->route('librarian.issues.index')->with('success', 'Book returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process return. Please try again.');
        }
    }

    /**
     * Display overdue books.
     */
    public function overdue()
    {
        $overdueIssues = LibraryIssue::with(['book', 'member.user'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->paginate(20);
        
        return view('librarian.issues.overdue', compact('overdueIssues'));
    }
}
