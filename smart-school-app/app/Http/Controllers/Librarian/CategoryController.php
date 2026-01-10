<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CategoryController
 * 
 * Handles library category management for librarians.
 */
class CategoryController extends Controller
{
    /**
     * Display categories list.
     */
    public function index(Request $request)
    {
        $query = LibraryCategory::withCount('books');
        
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $categories = $query->orderBy('name')->paginate(20);
        
        return view('librarian.categories.index', compact('categories'));
    }

    /**
     * Show create category form.
     */
    public function create()
    {
        return view('librarian.categories.create');
    }

    /**
     * Store new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:library_categories,name',
            'code' => 'nullable|string|max:50|unique:library_categories,code',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            LibraryCategory::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();
            return redirect()->route('librarian.categories.index')->with('success', 'Category added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add category. Please try again.');
        }
    }

    /**
     * Show category details.
     */
    public function show($id)
    {
        $category = LibraryCategory::withCount('books')->findOrFail($id);
        $books = $category->books()->orderBy('title')->paginate(10);
        
        return view('librarian.categories.show', compact('category', 'books'));
    }

    /**
     * Show edit category form.
     */
    public function edit($id)
    {
        $category = LibraryCategory::findOrFail($id);
        return view('librarian.categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, $id)
    {
        $category = LibraryCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:library_categories,name,' . $id,
            'code' => 'nullable|string|max:50|unique:library_categories,code,' . $id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $category->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();
            return redirect()->route('librarian.categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Delete category.
     */
    public function destroy($id)
    {
        $category = LibraryCategory::withCount('books')->findOrFail($id);
        
        if ($category->books_count > 0) {
            return back()->with('error', 'Cannot delete category with associated books.');
        }
        
        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();
            return redirect()->route('librarian.categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
