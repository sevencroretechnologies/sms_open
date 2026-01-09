<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function index(Request $request)
    {
        $examTypes = collect([]);
        return view('admin.exam-types.index', compact('examTypes'));
    }

    public function create()
    {
        return view('admin.exam-types.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type created successfully.');
    }

    public function show($id)
    {
        return view('admin.exam-types.index', ['examTypes' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.exam-types.create', ['examType' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.exam-types.index')->with('success', 'Exam type deleted successfully.');
    }
}
