<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ExamController
 * 
 * Handles exam schedules and marks entry for teachers.
 */
class ExamController extends Controller
{
    /**
     * Display list of exams.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $currentSession = AcademicSession::getCurrentSession();
        $classIds = $this->getTeacherClassIds($teacher);
        
        $exams = Exam::with(['examSchedules' => function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            }])
            ->when($currentSession, fn($q) => $q->where('academic_session_id', $currentSession->id))
            ->orderBy('start_date', 'desc')
            ->paginate(15);
        
        return view('teacher.exams.index', compact('exams', 'currentSession'));
    }

    /**
     * Display exam schedules for a specific exam.
     */
    public function show($id)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        
        $exam = Exam::with(['examSchedules' => function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds)
                ->with(['schoolClass', 'section', 'subject']);
        }])->findOrFail($id);
        
        $schedules = $exam->examSchedules->groupBy('class_id');
        
        return view('teacher.exams.show', compact('exam', 'schedules'));
    }

    /**
     * Show marks entry form for an exam schedule.
     */
    public function marksForm($scheduleId)
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $schedule = ExamSchedule::with(['exam', 'schoolClass', 'section', 'subject'])
            ->whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->findOrFail($scheduleId);
        
        $students = Student::where('class_id', $schedule->class_id)
            ->where('section_id', $schedule->section_id)
            ->active()
            ->with('user')
            ->orderBy('roll_number')
            ->get();
        
        $existingMarks = ExamMark::where('exam_schedule_id', $scheduleId)
            ->pluck('marks_obtained', 'student_id')
            ->toArray();
        
        return view('teacher.exams.marks', compact('schedule', 'students', 'existingMarks'));
    }

    /**
     * Store marks for students.
     */
    public function storeMarks(Request $request, $scheduleId)
    {
        $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0',
            'marks.*.is_absent' => 'nullable|boolean',
        ]);

        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $schedule = ExamSchedule::whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->findOrFail($scheduleId);

        DB::beginTransaction();
        try {
            foreach ($request->marks as $mark) {
                ExamMark::updateOrCreate(
                    [
                        'exam_schedule_id' => $scheduleId,
                        'student_id' => $mark['student_id'],
                    ],
                    [
                        'marks_obtained' => $mark['is_absent'] ?? false ? null : ($mark['marks_obtained'] ?? null),
                        'is_absent' => $mark['is_absent'] ?? false,
                        'remarks' => $mark['remarks'] ?? null,
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('teacher.exams.show', $schedule->exam_id)
                ->with('success', 'Marks saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save marks. Please try again.');
        }
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
}
