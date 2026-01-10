<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * NoticeController
 * 
 * Handles notice viewing for teachers.
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
                    ->orWhere('audience', 'teachers')
                    ->orWhere('audience', 'staff');
            })
            ->where('is_published', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy('publish_date', 'desc')
            ->paginate(15);
        
        return view('teacher.notices.index', compact('notices'));
    }

    /**
     * Display a specific notice.
     */
    public function show($id)
    {
        $notice = Notice::where(function ($query) {
                $query->where('audience', 'all')
                    ->orWhere('audience', 'teachers')
                    ->orWhere('audience', 'staff');
            })
            ->where('is_published', true)
            ->findOrFail($id);
        
        return view('teacher.notices.show', compact('notice'));
    }
}
