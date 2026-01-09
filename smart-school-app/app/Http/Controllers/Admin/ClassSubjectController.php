<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();
        return view('admin.classes.subjects', compact('classes', 'subjects'));
    }

    public function assignForm()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $subjects = Subject::active()->ordered()->get();
        return view('admin.classes.assign-subjects', compact('classes', 'subjects'));
    }

    public function assign(Request $request)
    {
        return redirect()->route('admin.class-subjects.index')->with('success', 'Subjects assigned successfully.');
    }

    public function unassign(Request $request, $classId, $subjectId)
    {
        return redirect()->route('admin.class-subjects.index')->with('success', 'Subject unassigned successfully.');
    }
}
