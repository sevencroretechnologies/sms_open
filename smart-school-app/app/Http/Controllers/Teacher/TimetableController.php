<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassTimetable;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * TimetableController
 * 
 * Handles timetable viewing for teachers.
 */
class TimetableController extends Controller
{
    /**
     * Display the teacher's timetable.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        $timetable = ClassTimetable::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'section', 'subject'])
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)
            ->with(['schoolClass', 'students' => function ($query) {
                $query->active();
            }])
            ->active()
            ->get();
        
        return view('teacher.timetable.index', compact('timetable', 'dayNames', 'classTeacherSections'));
    }

    /**
     * Print the teacher's timetable.
     */
    public function print(Request $request)
    {
        $teacher = Auth::user();
        $dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        $timetable = ClassTimetable::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'section', 'subject'])
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        
        return view('teacher.timetable.print', compact('timetable', 'dayNames', 'teacher'));
    }
}
