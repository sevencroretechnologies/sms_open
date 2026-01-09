<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamGradeController extends Controller
{
    public function index(Request $request)
    {
        $grades = collect([]);
        return view('admin.exam-grades.index', compact('grades'));
    }

    public function create()
    {
        return view('admin.exam-grades.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.exam-grades.index')->with('success', 'Grade created successfully.');
    }

    public function show($id)
    {
        return view('admin.exam-grades.index', ['grades' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.exam-grades.create', ['grade' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.exam-grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.exam-grades.index')->with('success', 'Grade deleted successfully.');
    }
}
