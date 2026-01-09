<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = collect([]);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function show($id)
    {
        return view('admin.teachers.show', ['teacher' => null]);
    }

    public function edit($id)
    {
        return view('admin.teachers.edit', ['teacher' => null]);
    }

    public function export()
    {
        return redirect()->route('admin.teachers.index')->with('success', 'Export started.');
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
