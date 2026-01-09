<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = collect([]);
        return view('admin.library.categories', compact('categories'));
    }

    public function create()
    {
        return view('admin.library.categories-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.library-categories.index')->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        return view('admin.library.categories', ['categories' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.library.categories-create', ['category' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.library-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.library-categories.index')->with('success', 'Category deleted successfully.');
    }
}
