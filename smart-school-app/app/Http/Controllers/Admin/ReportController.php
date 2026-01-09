<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.index');
    }

    public function students()
    {
        return view('admin.reports.students');
    }

    public function attendance()
    {
        return view('admin.reports.attendance');
    }

    public function exams()
    {
        return view('admin.reports.exams');
    }

    public function fees()
    {
        return view('admin.reports.fees');
    }

    public function financial()
    {
        return view('admin.reports.financial');
    }
}
