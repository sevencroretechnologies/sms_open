<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryIssue;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * MemberController
 * 
 * Handles library member management for librarians.
 */
class MemberController extends Controller
{
    /**
     * Display members list.
     */
    public function index(Request $request)
    {
        $query = LibraryMember::with('user');
        
        if ($request->type) {
            $query->where('member_type', $request->type);
        }
        
        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }
        
        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        
        $members = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('librarian.members.index', compact('members'));
    }

    /**
     * Show add member form.
     */
    public function create()
    {
        $students = Student::with('user')
            ->whereDoesntHave('libraryMember')
            ->get();
        
        $teachers = Teacher::with('user')
            ->whereDoesntHave('libraryMember')
            ->get();
        
        return view('librarian.members.create', compact('students', 'teachers'));
    }

    /**
     * Store new member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_type' => 'required|in:student,teacher',
            'member_id' => 'required|integer',
        ]);

        $existingMember = LibraryMember::where('member_type', $request->member_type)
            ->where('member_id', $request->member_id)
            ->first();
        
        if ($existingMember) {
            return back()->with('error', 'This person is already a library member.');
        }

        $userId = $request->member_type === 'student' 
            ? Student::find($request->member_id)->user_id 
            : Teacher::find($request->member_id)->user_id;

        DB::beginTransaction();
        try {
            LibraryMember::create([
                'member_type' => $request->member_type,
                'member_id' => $request->member_id,
                'user_id' => $userId,
                'is_active' => true,
            ]);

            DB::commit();
            return redirect()->route('librarian.members.index')->with('success', 'Member added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add member. Please try again.');
        }
    }

    /**
     * Show member details.
     */
    public function show($id)
    {
        $member = LibraryMember::with('user')->findOrFail($id);
        
        $issues = LibraryIssue::with('book')
            ->where('member_id', $id)
            ->orderBy('issue_date', 'desc')
            ->paginate(10);
        
        $activeIssues = LibraryIssue::where('member_id', $id)
            ->whereNull('return_date')
            ->count();
        
        $totalIssues = LibraryIssue::where('member_id', $id)->count();
        $totalFines = LibraryIssue::where('member_id', $id)->sum('fine_amount');
        $unpaidFines = LibraryIssue::where('member_id', $id)
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');
        
        return view('librarian.members.show', compact('member', 'issues', 'activeIssues', 'totalIssues', 'totalFines', 'unpaidFines'));
    }

    /**
     * Toggle member status.
     */
    public function toggleStatus($id)
    {
        $member = LibraryMember::findOrFail($id);
        
        $activeIssues = LibraryIssue::where('member_id', $id)
            ->whereNull('return_date')
            ->count();
        
        if ($member->is_active && $activeIssues > 0) {
            return back()->with('error', 'Cannot deactivate member with active book issues.');
        }
        
        $member->is_active = !$member->is_active;
        $member->save();
        
        $status = $member->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Member {$status} successfully.");
    }

    /**
     * Delete member.
     */
    public function destroy($id)
    {
        $member = LibraryMember::findOrFail($id);
        
        $hasIssues = LibraryIssue::where('member_id', $id)->exists();
        
        if ($hasIssues) {
            return back()->with('error', 'Cannot delete member with issue history. Deactivate instead.');
        }
        
        DB::beginTransaction();
        try {
            $member->delete();
            DB::commit();
            return redirect()->route('librarian.members.index')->with('success', 'Member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete member. Please try again.');
        }
    }
}
