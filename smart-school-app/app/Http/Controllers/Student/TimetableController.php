<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassTimetable;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TimetableController
 * 
 * Handles timetable viewing for students.
 */
class TimetableController extends Controller
{
    /**
     * Display the student's class timetable.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section'])
            ->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        $timetable = ClassTimetable::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->with(['subject', 'teacher.user'])
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        
        return view('student.timetable.index', compact('timetable', 'dayNames', 'student'));
    }
}
