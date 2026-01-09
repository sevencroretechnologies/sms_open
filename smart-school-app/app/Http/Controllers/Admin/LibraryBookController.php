<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryBookController extends Controller
{
    public function index(Request $request)
    {
        $books = collect([]);
        $categories = collect([]);
        return view('admin.library.books', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = collect([]);
        return view('admin.library.books-create', compact('categories'));
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.library-books.index')->with('success', 'Book added successfully.');
    }

    public function show($id)
    {
        return view('admin.library.books-show', ['book' => null]);
    }

    public function edit($id)
    {
        $categories = collect([]);
        return view('admin.library.books-create', ['book' => null, 'categories' => $categories]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.library-books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.library-books.index')->with('success', 'Book deleted successfully.');
    }

    public function importForm()
    {
        return view('admin.library.books', ['books' => collect([]), 'categories' => collect([])]);
    }

    public function import(Request $request)
    {
        return redirect()->route('admin.library-books.index')->with('success', 'Books imported successfully.');
    }

    public function export()
    {
        return redirect()->route('admin.library-books.index')->with('success', 'Export started.');
    }
}
