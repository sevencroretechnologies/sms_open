<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.timetable.index', compact('academicSessions', 'classes'));
    }

    public function create()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.timetable.create', compact('academicSessions', 'classes'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.timetables.index')->with('success', 'Timetable created successfully.');
    }

    public function show($id)
    {
        return view('admin.timetable.show', ['timetable' => null]);
    }

    public function edit($id)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.timetable.create', compact('academicSessions', 'classes') + ['timetable' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.timetables.index')->with('success', 'Timetable updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.timetables.index')->with('success', 'Timetable deleted successfully.');
    }

    public function byClass($classId)
    {
        return view('admin.timetable.index', ['academicSessions' => collect([]), 'classes' => collect([])]);
    }

    public function print($id)
    {
        return view('admin.timetable.print', ['timetable' => null]);
    }
}
