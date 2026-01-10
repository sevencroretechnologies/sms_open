<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * StudentController
 * 
 * Handles student listing and viewing for teachers.
 */
class StudentController extends Controller
{
    /**
     * Display list of students in teacher's classes.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $students = Student::whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->with(['user', 'schoolClass', 'section'])
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($request->section_id, fn($q, $v) => $q->where('section_id', $v))
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%");
            })
            ->active()
            ->orderBy('roll_number')
            ->paginate(25);
        
        $classes = $this->getTeacherClasses($teacher);
        
        return view('teacher.students.index', compact('students', 'classes'));
    }

    /**
     * Display student details.
     */
    public function show($id)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $student = Student::with(['user', 'schoolClass', 'section', 'category'])
            ->whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->findOrFail($id);
        
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->with('attendanceType')
            ->orderBy('attendance_date', 'desc')
            ->take(10)
            ->get();
        
        $recentMarks = ExamMark::where('student_id', $student->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('teacher.students.show', compact('student', 'recentAttendance', 'recentMarks'));
    }

    /**
     * Display student attendance history.
     */
    public function attendance($id)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $student = Student::with(['user', 'schoolClass', 'section'])
            ->whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->findOrFail($id);
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with('attendanceType')
            ->orderBy('attendance_date', 'desc')
            ->paginate(30);
        
        $summary = [
            'total' => $attendances->total(),
            'present' => Attendance::where('student_id', $student->id)
                ->whereHas('attendanceType', fn($q) => $q->where('is_present', true))->count(),
            'absent' => Attendance::where('student_id', $student->id)
                ->whereHas('attendanceType', fn($q) => $q->where('code', 'absent'))->count(),
        ];
        
        return view('teacher.students.attendance', compact('student', 'attendances', 'summary'));
    }

    /**
     * Display student marks history.
     */
    public function marks($id)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $student = Student::with(['user', 'schoolClass', 'section'])
            ->whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->findOrFail($id);
        
        $marks = ExamMark::where('student_id', $student->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        
        return view('teacher.students.marks', compact('student', 'marks'));
    }

    protected function getTeacherClassIds($teacher): array
    {
        $classTeacherClasses = Section::where('class_teacher_id', $teacher->id)->pluck('class_id')->toArray();
        $subjectClasses = DB::table('class_subjects')->where('teacher_id', $teacher->id)->pluck('class_id')->toArray();
        return array_unique(array_merge($classTeacherClasses, $subjectClasses));
    }

    protected function getTeacherSectionIds($teacher): array
    {
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)->pluck('id')->toArray();
        $subjectSections = DB::table('class_subjects')->where('teacher_id', $teacher->id)->pluck('section_id')->toArray();
        return array_unique(array_merge($classTeacherSections, $subjectSections));
    }

    protected function getTeacherClasses($teacher)
    {
        $classIds = $this->getTeacherClassIds($teacher);
        return SchoolClass::whereIn('id', $classIds)->active()->ordered()->get();
    }
}
