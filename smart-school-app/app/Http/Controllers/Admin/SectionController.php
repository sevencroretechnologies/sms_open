<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Display a listing of sections.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Section::with(['schoolClass', 'classTeacher'])
            ->withCount('students');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sections = $query->orderBy('class_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::active()->ordered()->get();

        return view('admin.sections.index', compact('sections', 'classes'));
    }

    /**
     * Show the form for creating a new section.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('admin.sections.create', compact('classes', 'teachers'));
    }

    /**
     * Store a newly created section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:10'],
            'display_name' => ['required', 'string', 'max:50'],
            'class_id' => ['required', 'exists:classes,id'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'room_number' => ['nullable', 'string', 'max:20'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $exists = Section::where('name', $validated['name'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['name' => 'A section with this name already exists for the selected class.']);
        }

        $section = Section::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'class_id' => $validated['class_id'],
            'capacity' => $validated['capacity'],
            'room_number' => $validated['room_number'] ?? null,
            'class_teacher_id' => $validated['class_teacher_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $class = SchoolClass::find($validated['class_id']);
        $class->increment('section_count');

        return redirect()->route('admin.sections.show', $section)
            ->with('success', 'Section created successfully.');
    }

    /**
     * Display the specified section.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\View\View
     */
    public function show(Section $section)
    {
        $section->load([
            'schoolClass.academicSession',
            'classTeacher',
            'students' => function ($query) {
                $query->active()->with('user')->orderBy('roll_number');
            },
            'classSubjects' => function ($query) {
                $query->with(['subject', 'teacher']);
            },
            'classTimetables' => function ($query) {
                $query->with('subject')->orderBy('day_of_week')->orderBy('period_number');
            },
        ]);

        $statistics = [
            'totalStudents' => $section->students->count(),
            'maleStudents' => $section->students->where('gender', 'male')->count(),
            'femaleStudents' => $section->students->where('gender', 'female')->count(),
            'availableSeats' => $section->available_seats,
            'occupancyRate' => $section->capacity > 0 
                ? round(($section->students->count() / $section->capacity) * 100) 
                : 0,
            'totalSubjects' => $section->classSubjects->unique('subject_id')->count(),
        ];

        return view('admin.sections.show', compact('section', 'statistics'));
    }

    /**
     * Show the form for editing the specified section.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\View\View
     */
    public function edit(Section $section)
    {
        $classes = SchoolClass::active()->ordered()->get();
        $teachers = User::role('teacher')->where('is_active', true)->get();

        return view('admin.sections.edit', compact('section', 'classes', 'teachers'));
    }

    /**
     * Update the specified section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:10'],
            'display_name' => ['required', 'string', 'max:50'],
            'class_id' => ['required', 'exists:classes,id'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'room_number' => ['nullable', 'string', 'max:20'],
            'class_teacher_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $exists = Section::where('name', $validated['name'])
            ->where('class_id', $validated['class_id'])
            ->where('id', '!=', $section->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['name' => 'A section with this name already exists for the selected class.']);
        }

        $oldClassId = $section->class_id;

        $section->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'class_id' => $validated['class_id'],
            'capacity' => $validated['capacity'],
            'room_number' => $validated['room_number'] ?? null,
            'class_teacher_id' => $validated['class_teacher_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($oldClassId !== $validated['class_id']) {
            SchoolClass::find($oldClassId)?->decrement('section_count');
            SchoolClass::find($validated['class_id'])?->increment('section_count');
        }

        return redirect()->route('admin.sections.show', $section)
            ->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified section from storage (soft delete).
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Section $section)
    {
        if ($section->students()->exists()) {
            return back()->with('error', 'Cannot delete section with enrolled students.');
        }

        $classId = $section->class_id;

        $section->update(['is_active' => false]);
        $section->delete();

        SchoolClass::find($classId)?->decrement('section_count');

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section deleted successfully.');
    }

    /**
     * Toggle the active status of the specified section.
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Section $section)
    {
        if ($section->is_active && $section->students()->exists()) {
            return back()->with('error', 'Cannot deactivate section with enrolled students.');
        }

        $section->update(['is_active' => !$section->is_active]);

        $status = $section->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Section {$status} successfully.");
    }

    /**
     * Assign a class teacher to the section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignTeacher(Request $request, Section $section)
    {
        $validated = $request->validate([
            'class_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $section->update([
            'class_teacher_id' => $validated['class_teacher_id'],
        ]);

        $message = $validated['class_teacher_id'] 
            ? 'Class teacher assigned successfully.' 
            : 'Class teacher removed successfully.';

        return back()->with('success', $message);
    }

    /**
     * Get students for a specific section (AJAX).
     *
     * @param  \App\Models\Section  $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudents(Section $section)
    {
        $students = $section->students()
            ->active()
            ->with('user')
            ->orderBy('roll_number')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'admission_number' => $student->admission_number,
                    'roll_number' => $student->roll_number,
                ];
            });

        return response()->json($students);
    }
}
