<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.students.promotions', compact('academicSessions', 'classes'));
    }

    public function promoteForm()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $classes = SchoolClass::active()->ordered()->get();
        return view('admin.students.promote', compact('academicSessions', 'classes'));
    }

    public function promote(Request $request)
    {
        return redirect()->route('admin.promotions.index')->with('success', 'Students promoted successfully.');
    }

    public function history()
    {
        return view('admin.students.promotions', ['academicSessions' => collect([]), 'classes' => collect([])]);
    }
}
