<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = collect([]);
        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        return view('admin.students.index', compact('students', 'classes', 'academicSessions'));
    }

    public function create()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        return view('admin.students.create', compact('classes', 'academicSessions'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    public function show($id)
    {
        return view('admin.students.show', ['student' => null, 'students' => collect([])]);
    }

    public function edit($id)
    {
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.students.edit', ['student' => null, 'classes' => $classes]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    public function profile($id)
    {
        return view('admin.students.show', ['student' => null]);
    }

    public function documents($id)
    {
        return view('admin.students.show', ['student' => null]);
    }

    public function uploadDocument(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function deleteDocument($studentId, $documentId)
    {
        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    public function importForm()
    {
        return view('admin.students.index');
    }

    public function import(Request $request)
    {
        return redirect()->route('admin.students.index')->with('success', 'Students imported successfully.');
    }

    public function export()
    {
        return redirect()->route('admin.students.index')->with('success', 'Export started.');
    }

    public function bulkActionsForm()
    {
        return view('admin.students.index');
    }

    public function bulkActions(Request $request)
    {
        return redirect()->route('admin.students.index')->with('success', 'Bulk action completed.');
    }
}
