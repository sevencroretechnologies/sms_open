<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryBookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LibraryController
 * 
 * Handles library book browsing and issued books viewing for students.
 */
class LibraryController extends Controller
{
    /**
     * Display available library books.
     */
    public function index(Request $request)
    {
        $books = LibraryBook::when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            })
            ->when($request->category, fn($q, $cat) => $q->where('category_id', $cat))
            ->where('quantity', '>', 0)
            ->orderBy('title')
            ->paginate(20);
        
        return view('student.library.index', compact('books'));
    }

    /**
     * Display books issued to the student.
     */
    public function issued()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $issuedBooks = LibraryBookIssue::where('student_id', $student->id)
            ->with('book')
            ->orderBy('issue_date', 'desc')
            ->paginate(15);
        
        return view('student.library.issued', compact('issuedBooks'));
    }

    /**
     * Display book details.
     */
    public function show($id)
    {
        $book = LibraryBook::findOrFail($id);
        
        return view('student.library.show', compact('book'));
    }
}
