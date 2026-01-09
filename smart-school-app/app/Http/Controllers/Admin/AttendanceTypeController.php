<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceTypeController extends Controller
{
    public function index(Request $request)
    {
        $attendanceTypes = collect([]);
        return view('admin.attendance.types', compact('attendanceTypes'));
    }

    public function create()
    {
        return view('admin.attendance.types-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.attendance-types.index')->with('success', 'Attendance type created successfully.');
    }

    public function show($id)
    {
        return view('admin.attendance.types', ['attendanceTypes' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.attendance.types-create', ['attendanceType' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.attendance-types.index')->with('success', 'Attendance type updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.attendance-types.index')->with('success', 'Attendance type deleted successfully.');
    }
}
