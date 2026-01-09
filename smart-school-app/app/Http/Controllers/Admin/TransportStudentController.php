<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportStudentController extends Controller
{
    public function index(Request $request)
    {
        $students = collect([]);
        return view('admin.transport.students', compact('students'));
    }

    public function assignForm()
    {
        return view('admin.transport.assign');
    }

    public function assign(Request $request)
    {
        return redirect()->route('admin.transport-students.index')->with('success', 'Student assigned successfully.');
    }

    public function unassign($id)
    {
        return redirect()->route('admin.transport-students.index')->with('success', 'Student unassigned successfully.');
    }

    public function report()
    {
        return view('admin.transport.report');
    }
}
