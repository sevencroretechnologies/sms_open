<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = collect([]);
        return view('admin.reports.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.reports.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function show($id)
    {
        return view('admin.reports.index', ['teacher' => null]);
    }

    public function edit($id)
    {
        return view('admin.reports.index', ['teacher' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
