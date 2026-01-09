<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryIssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = collect([]);
        return view('admin.library.issues', compact('issues'));
    }

    public function issueForm()
    {
        return view('admin.library.issue-book');
    }

    public function issue(Request $request)
    {
        return redirect()->route('admin.library-issues.index')->with('success', 'Book issued successfully.');
    }

    public function returnForm($id)
    {
        return view('admin.library.return-book', ['issue' => null]);
    }

    public function returnBook(Request $request, $id)
    {
        return redirect()->route('admin.library-issues.index')->with('success', 'Book returned successfully.');
    }

    public function overdue()
    {
        return view('admin.library.issues', ['issues' => collect([])]);
    }

    public function report()
    {
        return view('admin.library.issues', ['issues' => collect([])]);
    }
}
