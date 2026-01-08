<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = SchoolClass::with(['academicSession', 'sections'])
            ->withCount(['students', 'sections']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $classes = $query->ordered()->paginate(15)->withQueryString();

        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('admin.classes.index', compact('classes', 'academicSessions'));
    }

    /**
     * Show the form for creating a new class.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $currentSession = AcademicSession::getCurrentSession();

        $maxOrderIndex = SchoolClass::max('order_index') ?? 0;

        return view('admin.classes.create', compact('academicSessions', 'currentSession', 'maxOrderIndex'));
    }

    /**
     * Store a newly created class in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'display_name' => ['required', 'string', 'max:100'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'section_count' => ['required', 'integer', 'min:0', 'max:20'],
            'order_index' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $exists = SchoolClass::where('name', $validated['name'])
            ->where('academic_session_id', $validated['academic_session_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['name' => 'A class with this name already exists for the selected academic session.']);
        }

        $class = SchoolClass::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'academic_session_id' => $validated['academic_session_id'],
            'section_count' => $validated['section_count'],
            'order_index' => $validated['order_index'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($validated['section_count'] > 0) {
            $sectionNames = range('A', chr(ord('A') + $validated['section_count'] - 1));
            foreach ($sectionNames as $sectionName) {
                Section::create([
                    'class_id' => $class->id,
                    'name' => $sectionName,
                    'display_name' => "Section {$sectionName}",
                    'capacity' => 40,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.classes.show', $class)
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified class.
     *
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\View\View
     */
    public function show(SchoolClass $class)
    {
        $class->load([
            'academicSession',
            'sections' => function ($query) {
                $query->active()->withCount('students');
            },
            'students' => function ($query) {
                $query->active()->with(['user', 'section']);
            },
            'classSubjects' => function ($query) {
                $query->with(['subject', 'teacher']);
            },
        ]);

        $statistics = [
            'totalSections' => $class->sections->count(),
            'totalStudents' => $class->students->count(),
            'totalSubjects' => $class->classSubjects->unique('subject_id')->count(),
            'maleStudents' => $class->students->where('gender', 'male')->count(),
            'femaleStudents' => $class->students->where('gender', 'female')->count(),
            'averageCapacity' => $class->sections->avg('capacity') ?? 0,
        ];

        return view('admin.classes.show', compact('class', 'statistics'));
    }

    /**
     * Show the form for editing the specified class.
     *
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\View\View
     */
    public function edit(SchoolClass $class)
    {
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('admin.classes.edit', compact('class', 'academicSessions'));
    }

    /**
     * Update the specified class in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'display_name' => ['required', 'string', 'max:100'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'section_count' => ['required', 'integer', 'min:0', 'max:20'],
            'order_index' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $exists = SchoolClass::where('name', $validated['name'])
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where('id', '!=', $class->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['name' => 'A class with this name already exists for the selected academic session.']);
        }

        $class->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'academic_session_id' => $validated['academic_session_id'],
            'section_count' => $validated['section_count'],
            'order_index' => $validated['order_index'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified class from storage (soft delete).
     *
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Cannot delete class with enrolled students.');
        }

        $class->sections()->delete();

        $class->update(['is_active' => false]);
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    /**
     * Toggle the active status of the specified class.
     *
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(SchoolClass $class)
    {
        if ($class->is_active && $class->students()->exists()) {
            return back()->with('error', 'Cannot deactivate class with enrolled students.');
        }

        $class->update(['is_active' => !$class->is_active]);

        $status = $class->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Class {$status} successfully.");
    }

    /**
     * Get sections for a specific class (AJAX).
     *
     * @param  \App\Models\SchoolClass  $class
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSections(SchoolClass $class)
    {
        $sections = $class->sections()
            ->active()
            ->get(['id', 'name', 'display_name', 'capacity']);

        return response()->json($sections);
    }

    /**
     * Reorder classes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'classes' => ['required', 'array'],
            'classes.*.id' => ['required', 'exists:classes,id'],
            'classes.*.order_index' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['classes'] as $classData) {
            SchoolClass::where('id', $classData['id'])
                ->update(['order_index' => $classData['order_index']]);
        }

        return response()->json(['success' => true]);
    }
}
