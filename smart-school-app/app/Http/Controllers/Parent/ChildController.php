<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\FeesAllotment;
use App\Models\ParentModel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ChildController
 * 
 * Handles viewing children's information for parents.
 */
class ChildController extends Controller
{
    /**
     * Display list of children.
     */
    public function index()
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Parent profile not found.');
        }
        
        $children = Student::where('parent_id', $parent->id)
            ->with(['schoolClass', 'section', 'user'])
            ->get();
        
        return view('parent.children.index', compact('children'));
    }

    /**
     * Display a specific child's details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Parent profile not found.');
        }
        
        $child = Student::where('id', $id)
            ->where('parent_id', $parent->id)
            ->with(['schoolClass', 'section', 'user'])
            ->firstOrFail();
        
        $recentAttendance = Attendance::where('student_id', $child->id)
            ->with('attendanceType')
            ->orderBy('attendance_date', 'desc')
            ->take(10)
            ->get();
        
        $recentMarks = ExamMark::where('student_id', $child->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $feesSummary = FeesAllotment::where('student_id', $child->id)
            ->selectRaw('SUM(total_amount) as total, SUM(paid_amount) as paid, SUM(balance) as balance')
            ->first();
        
        return view('parent.children.show', compact('child', 'recentAttendance', 'recentMarks', 'feesSummary'));
    }

    /**
     * Display child's attendance.
     */
    public function attendance($id, Request $request)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Parent profile not found.');
        }
        
        $child = Student::where('id', $id)
            ->where('parent_id', $parent->id)
            ->with(['schoolClass', 'section'])
            ->firstOrFail();
        
        $month = $request->month ?? Carbon::now()->format('Y-m');
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        $attendances = Attendance::where('student_id', $child->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->with('attendanceType')
            ->orderBy('attendance_date', 'desc')
            ->get();
        
        $summary = [
            'total' => $attendances->count(),
            'present' => $attendances->filter(fn($a) => $a->attendanceType && $a->attendanceType->is_present)->count(),
            'absent' => $attendances->filter(fn($a) => $a->attendanceType && !$a->attendanceType->is_present)->count(),
        ];
        
        $summary['percentage'] = $summary['total'] > 0 
            ? round(($summary['present'] / $summary['total']) * 100, 1) 
            : 0;
        
        return view('parent.children.attendance', compact('child', 'attendances', 'summary', 'month'));
    }

    /**
     * Display child's exam results.
     */
    public function exams($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Parent profile not found.');
        }
        
        $child = Student::where('id', $id)
            ->where('parent_id', $parent->id)
            ->with(['schoolClass', 'section'])
            ->firstOrFail();
        
        $marks = ExamMark::where('student_id', $child->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($m) => $m->examSchedule->exam_id ?? 0);
        
        return view('parent.children.exams', compact('child', 'marks'));
    }

    /**
     * Display child's fees.
     */
    public function fees($id)
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Parent profile not found.');
        }
        
        $child = Student::where('id', $id)
            ->where('parent_id', $parent->id)
            ->with(['schoolClass', 'section'])
            ->firstOrFail();
        
        $allotments = FeesAllotment::where('student_id', $child->id)
            ->with(['feeGroup', 'feesMasters.feeType'])
            ->orderBy('due_date', 'desc')
            ->get();
        
        $summary = [
            'total_fees' => $allotments->sum('total_amount'),
            'paid' => $allotments->sum('paid_amount'),
            'balance' => $allotments->sum('balance'),
        ];
        
        return view('parent.children.fees', compact('child', 'allotments', 'summary'));
    }
}
