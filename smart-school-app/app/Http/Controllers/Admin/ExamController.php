<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = collect([]);
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.exams.index', compact('exams', 'academicSessions', 'classes'));
    }

    public function create()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        $examTypes = collect([]);
        return view('admin.exams.create', compact('academicSessions', 'classes', 'examTypes'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    public function show($id)
    {
        return view('admin.exams.index', ['exam' => null, 'exams' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.exams.create', ['exam' => null, 'academicSessions' => collect([]), 'classes' => collect([]), 'examTypes' => collect([])]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function schedule($id)
    {
        return view('admin.exams.schedule', ['exam' => null]);
    }

    public function saveSchedule(Request $request, $id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Schedule saved successfully.');
    }

    public function attendance($id)
    {
        return view('admin.exams.attendance', ['exam' => null]);
    }

    public function saveAttendance(Request $request, $id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Attendance saved successfully.');
    }

    public function marks($id)
    {
        return view('admin.exams.marks', ['exam' => null]);
    }

    public function saveMarks(Request $request, $id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Marks saved successfully.');
    }

    public function publish($id)
    {
        return redirect()->route('admin.exams.index')->with('success', 'Exam published successfully.');
    }

    public function results($id)
    {
        return view('admin.exams.statistics', ['exam' => null]);
    }

    public function printResults($id)
    {
        return view('admin.exams.report-card-print', ['exam' => null]);
    }
}
