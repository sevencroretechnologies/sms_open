<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ExamController
 * 
 * Handles exam schedule and results viewing for students.
 */
class ExamController extends Controller
{
    /**
     * Display exam schedules and results.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $currentSession = AcademicSession::getCurrentSession();
        
        $exams = Exam::when($currentSession, fn($q) => $q->where('academic_session_id', $currentSession->id))
            ->orderBy('start_date', 'desc')
            ->get();
        
        $schedules = ExamSchedule::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->with(['exam', 'subject'])
            ->orderBy('exam_date', 'desc')
            ->get()
            ->groupBy('exam_id');
        
        return view('student.exams.index', compact('exams', 'schedules', 'currentSession'));
    }

    /**
     * Display results for a specific exam.
     */
    public function results(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $marks = ExamMark::where('student_id', $student->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($m) => $m->examSchedule->exam_id ?? 0);
        
        return view('student.exams.results', compact('marks'));
    }

    /**
     * Display results for a specific exam.
     */
    public function show($examId)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $exam = Exam::findOrFail($examId);
        
        $schedules = ExamSchedule::where('exam_id', $examId)
            ->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->with('subject')
            ->orderBy('exam_date')
            ->get();
        
        $marks = ExamMark::where('student_id', $student->id)
            ->whereIn('exam_schedule_id', $schedules->pluck('id'))
            ->get()
            ->keyBy('exam_schedule_id');
        
        return view('student.exams.show', compact('exam', 'schedules', 'marks'));
    }
}
