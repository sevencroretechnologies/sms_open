<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\AttendanceType;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * AttendanceController
 * 
 * Handles attendance marking and reporting for teachers.
 */
class AttendanceController extends Controller
{
    /**
     * Display attendance history/overview.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $currentSession = AcademicSession::getCurrentSession();
        
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $attendances = collect();
        if (!empty($classIds) && !empty($sectionIds)) {
            $attendances = Attendance::whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->with(['student.user', 'schoolClass', 'section', 'attendanceType'])
                ->when($request->date, function ($query, $date) {
                    return $query->whereDate('attendance_date', $date);
                })
                ->when($request->class_id, function ($query, $classId) {
                    return $query->where('class_id', $classId);
                })
                ->when($request->section_id, function ($query, $sectionId) {
                    return $query->where('section_id', $sectionId);
                })
                ->orderBy('attendance_date', 'desc')
                ->paginate(50);
        }
        
        $classes = $this->getTeacherClasses($teacher);
        $attendanceTypes = AttendanceType::active()->get();
        
        return view('teacher.attendance.index', compact(
            'attendances',
            'classes',
            'attendanceTypes',
            'currentSession'
        ));
    }

    /**
     * Show the attendance marking form.
     */
    public function markForm(Request $request)
    {
        $teacher = Auth::user();
        $currentSession = AcademicSession::getCurrentSession();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = $this->getTeacherClasses($teacher);
        $attendanceTypes = AttendanceType::active()->get();
        
        return view('teacher.attendance.mark', compact(
            'academicSessions',
            'classes',
            'attendanceTypes',
            'currentSession'
        ));
    }

    /**
     * Store attendance records.
     */
    public function mark(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.attendance_type_id' => 'required|exists:attendance_types,id',
        ]);

        $teacher = Auth::user();
        
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        if (!in_array($request->class_id, $classIds) || !in_array($request->section_id, $sectionIds)) {
            return back()->with('error', 'You do not have permission to mark attendance for this class.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $record) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'attendance_date' => $request->date,
                    ],
                    [
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'attendance_type_id' => $record['attendance_type_id'],
                        'remarks' => $record['remarks'] ?? null,
                        'marked_by' => $teacher->id,
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('teacher.attendance.index')
                ->with('success', 'Attendance marked successfully for ' . count($request->attendance) . ' students.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark attendance. Please try again.');
        }
    }

    /**
     * Show attendance edit form.
     */
    public function edit($id)
    {
        $attendance = Attendance::with(['student.user', 'schoolClass', 'section', 'attendanceType'])
            ->findOrFail($id);
        
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        if (!in_array($attendance->class_id, $classIds) || !in_array($attendance->section_id, $sectionIds)) {
            abort(403, 'You do not have permission to edit this attendance record.');
        }
        
        $attendanceTypes = AttendanceType::active()->get();
        
        return view('teacher.attendance.edit', compact('attendance', 'attendanceTypes'));
    }

    /**
     * Update attendance record.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'attendance_type_id' => 'required|exists:attendance_types,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($id);
        
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        if (!in_array($attendance->class_id, $classIds) || !in_array($attendance->section_id, $sectionIds)) {
            abort(403, 'You do not have permission to update this attendance record.');
        }

        $attendance->update([
            'attendance_type_id' => $request->attendance_type_id,
            'remarks' => $request->remarks,
            'marked_by' => $teacher->id,
        ]);

        return redirect()->route('teacher.attendance.index')
            ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Display attendance report.
     */
    public function report(Request $request)
    {
        $teacher = Auth::user();
        $currentSession = AcademicSession::getCurrentSession();
        
        $classes = $this->getTeacherClasses($teacher);
        $attendanceTypes = AttendanceType::active()->get();
        
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');
        
        $reportData = [];
        
        if ($request->class_id && $request->section_id) {
            $students = Student::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->active()
                ->with('user')
                ->get();
            
            foreach ($students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereBetween('attendance_date', [$startDate, $endDate])
                    ->with('attendanceType')
                    ->get();
                
                $summary = [
                    'student' => $student,
                    'total_days' => $attendances->count(),
                    'present' => $attendances->filter(fn($a) => $a->attendanceType && $a->attendanceType->is_present)->count(),
                    'absent' => $attendances->filter(fn($a) => $a->attendanceType && !$a->attendanceType->is_present && $a->attendanceType->code === 'absent')->count(),
                    'late' => $attendances->filter(fn($a) => $a->attendanceType && $a->attendanceType->code === 'late')->count(),
                    'leave' => $attendances->filter(fn($a) => $a->attendanceType && $a->attendanceType->code === 'leave')->count(),
                ];
                
                $summary['percentage'] = $summary['total_days'] > 0 
                    ? round(($summary['present'] / $summary['total_days']) * 100, 1) 
                    : 0;
                
                $reportData[] = $summary;
            }
        }
        
        return view('teacher.attendance.report', compact(
            'classes',
            'attendanceTypes',
            'reportData',
            'startDate',
            'endDate',
            'currentSession'
        ));
    }

    /**
     * Get class IDs for the teacher.
     */
    protected function getTeacherClassIds($teacher): array
    {
        $classTeacherClasses = Section::where('class_teacher_id', $teacher->id)
            ->pluck('class_id')
            ->toArray();

        $subjectClasses = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->toArray();

        return array_unique(array_merge($classTeacherClasses, $subjectClasses));
    }

    /**
     * Get section IDs for the teacher.
     */
    protected function getTeacherSectionIds($teacher): array
    {
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        $subjectSections = DB::table('class_subjects')
            ->where('teacher_id', $teacher->id)
            ->pluck('section_id')
            ->toArray();

        return array_unique(array_merge($classTeacherSections, $subjectSections));
    }

    /**
     * Get classes assigned to the teacher.
     */
    protected function getTeacherClasses($teacher)
    {
        $classIds = $this->getTeacherClassIds($teacher);
        return SchoolClass::whereIn('id', $classIds)->active()->ordered()->get();
    }
}
