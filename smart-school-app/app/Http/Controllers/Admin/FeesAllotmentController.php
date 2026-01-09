<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeesAllotmentController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        $allotments = collect([]);
        return view('admin.fee-allotments.index', compact('academicSessions', 'classes', 'allotments'));
    }

    public function create()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.fee-allotments.create', compact('academicSessions', 'classes'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-allotments.index')->with('success', 'Fees allotted successfully.');
    }

    public function show($id)
    {
        return view('admin.fee-allotments.index', ['allotments' => collect([]), 'academicSessions' => collect([]), 'classes' => collect([])]);
    }

    public function edit($id)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.fee-allotments.create', compact('academicSessions', 'classes') + ['allotment' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-allotments.index')->with('success', 'Allotment updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-allotments.index')->with('success', 'Allotment deleted successfully.');
    }
}
