<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AIToolsController extends Controller
{
    public function index(): View
    {
        return view('admin.ai-tools.index');
    }

    public function performancePredictor(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_name')->get();
        
        return view('admin.ai-tools.performance-predictor', compact('students', 'classes'));
    }

    public function reportCardComments(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.ai-tools.report-card-comments', compact('students', 'subjects'));
    }

    public function parentCommunication(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        
        return view('admin.ai-tools.parent-communication', compact('students'));
    }

    public function assignmentGrader(): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.ai-tools.assignment-grader', compact('subjects'));
    }

    public function studyPlan(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.ai-tools.study-plan', compact('students', 'subjects'));
    }

    public function questionGenerator(): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_name')->get();
        
        return view('admin.ai-tools.question-generator', compact('subjects', 'classes'));
    }

    public function timetableOptimizer(): View
    {
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_name')->get();
        
        return view('admin.ai-tools.timetable-optimizer', compact('teachers', 'subjects', 'classes'));
    }

    public function careerGuidance(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        
        return view('admin.ai-tools.career-guidance', compact('students'));
    }

    public function meetingSummary(): View
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true)
            ->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        
        return view('admin.ai-tools.meeting-summary', compact('students', 'teachers'));
    }

    public function curriculumChecker(): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('numeric_name')->get();
        
        return view('admin.ai-tools.curriculum-checker', compact('subjects', 'classes'));
    }
}
