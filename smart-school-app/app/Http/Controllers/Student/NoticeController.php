<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * NoticeController
 * 
 * Handles notice viewing for students.
 */
class NoticeController extends Controller
{
    /**
     * Display list of notices.
     */
    public function index(Request $request)
    {
        $notices = Notice::where(function ($query) {
                $query->where('audience', 'all')
                    ->orWhere('audience', 'students');
            })
            ->where('is_published', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy('publish_date', 'desc')
            ->paginate(15);
        
        return view('student.notices.index', compact('notices'));
    }

    /**
     * Display a specific notice.
     */
    public function show($id)
    {
        $notice = Notice::where(function ($query) {
                $query->where('audience', 'all')
                    ->orWhere('audience', 'students');
            })
            ->where('is_published', true)
            ->findOrFail($id);
        
        return view('student.notices.show', compact('notice'));
    }
}
