<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentCategoryController extends Controller
{
    /**
     * Display a listing of student categories with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = StudentCategory::withCount('students');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.student-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new student category.
     */
    public function create()
    {
        return view('admin.student-categories.create');
    }

    /**
     * Store a newly created student category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:student_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $category = StudentCategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.student-categories.show', $category)
            ->with('success', 'Student category created successfully.');
    }

    /**
     * Display the specified student category.
     */
    public function show(StudentCategory $studentCategory)
    {
        $studentCategory->load([
            'students' => function ($query) {
                $query->active()->with(['user', 'schoolClass', 'section'])->take(20);
            },
        ]);

        $statistics = [
            'totalStudents' => $studentCategory->students()->count(),
            'activeStudents' => $studentCategory->students()->active()->count(),
            'maleStudents' => $studentCategory->students()->where('gender', 'male')->count(),
            'femaleStudents' => $studentCategory->students()->where('gender', 'female')->count(),
        ];

        return view('admin.student-categories.show', compact('studentCategory', 'statistics'));
    }

    /**
     * Show the form for editing the specified student category.
     */
    public function edit(StudentCategory $studentCategory)
    {
        return view('admin.student-categories.edit', compact('studentCategory'));
    }

    /**
     * Update the specified student category in storage.
     */
    public function update(Request $request, StudentCategory $studentCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('student_categories', 'name')->ignore($studentCategory->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $studentCategory->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.student-categories.show', $studentCategory)
            ->with('success', 'Student category updated successfully.');
    }

    /**
     * Remove the specified student category from storage (soft delete).
     */
    public function destroy(StudentCategory $studentCategory)
    {
        if ($studentCategory->students()->exists()) {
            return back()->with('error', 'Cannot delete category with enrolled students.');
        }

        $studentCategory->update(['is_active' => false]);
        $studentCategory->delete();

        return redirect()->route('admin.student-categories.index')
            ->with('success', 'Student category deleted successfully.');
    }

    /**
     * Toggle the active status of the specified student category.
     */
    public function toggleStatus(StudentCategory $studentCategory)
    {
        if ($studentCategory->is_active && $studentCategory->students()->exists()) {
            return back()->with('error', 'Cannot deactivate category with enrolled students.');
        }

        $studentCategory->update(['is_active' => !$studentCategory->is_active]);

        $status = $studentCategory->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Student category {$status} successfully.");
    }

    /**
     * Get all active student categories (AJAX).
     */
    public function getAll()
    {
        $categories = StudentCategory::active()
            ->ordered()
            ->get(['id', 'name', 'description']);

        return response()->json($categories);
    }
}
