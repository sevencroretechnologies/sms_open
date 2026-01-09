<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $parents = collect([]);
        return view('admin.parents.index', compact('parents'));
    }

    public function create()
    {
        return view('admin.parents.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.parents.index')->with('success', 'Parent created successfully.');
    }

    public function show($id)
    {
        return view('admin.parents.show', ['parent' => null]);
    }

    public function edit($id)
    {
        return view('admin.parents.create', ['parent' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.parents.index')->with('success', 'Parent updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.parents.index')->with('success', 'Parent deleted successfully.');
    }
}
