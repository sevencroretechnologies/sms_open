<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $assignments = collect([]);
        return view('admin.hostels.students', compact('assignments'));
    }

    public function assignForm()
    {
        return view('admin.hostels.assign');
    }

    public function assign(Request $request)
    {
        return redirect()->route('admin.hostel-assignments.index')->with('success', 'Student assigned successfully.');
    }

    public function unassign($id)
    {
        return redirect()->route('admin.hostel-assignments.index')->with('success', 'Student unassigned successfully.');
    }
}
