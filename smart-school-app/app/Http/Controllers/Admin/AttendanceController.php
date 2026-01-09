<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        $attendanceTypes = [];
        
        return view('admin.attendance.index', compact('academicSessions', 'classes', 'attendanceTypes'));
    }

    public function markForm()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        $attendanceTypes = [];
        
        return view('admin.attendance.index', compact('academicSessions', 'classes', 'attendanceTypes'));
    }

    public function mark(Request $request)
    {
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance marked successfully.');
    }

    public function edit($id)
    {
        return view('admin.attendance.edit', ['attendance' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function report()
    {
        return view('admin.attendance.report');
    }

    public function calendar($student = null)
    {
        return view('admin.attendance.calendar', ['student' => null]);
    }

    public function export()
    {
        return view('admin.attendance.export');
    }

    public function print()
    {
        return view('admin.attendance.print');
    }

    public function smsForm()
    {
        return view('admin.attendance.sms');
    }

    public function sendSms(Request $request)
    {
        return redirect()->route('admin.attendance.sms')->with('success', 'SMS sent successfully.');
    }
}
