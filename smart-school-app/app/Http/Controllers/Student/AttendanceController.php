<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AttendanceController
 * 
 * Handles attendance viewing for students.
 */
class AttendanceController extends Controller
{
    /**
     * Display attendance history.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $month = $request->month ?? Carbon::now()->format('Y-m');
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        $attendances = Attendance::where('student_id', $student->id)
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
        
        $calendarData = $attendances->keyBy(fn($a) => $a->attendance_date->format('Y-m-d'));
        
        return view('student.attendance.index', compact('attendances', 'summary', 'month', 'calendarData', 'startDate', 'endDate'));
    }
}
