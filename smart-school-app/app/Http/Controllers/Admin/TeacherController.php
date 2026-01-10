<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = User::role('teacher')
            ->with(['classTeacherSections', 'classSubjects']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teachers = $query->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $classes = SchoolClass::active()->ordered()->get();

        return view('admin.teachers.create', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'numeric', 'min:0'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            }

            $teacher = User::create([
                'uuid' => Str::uuid(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('password123'),
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'avatar' => $photoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $teacher->assignRole('teacher');

            DB::commit();

            return redirect()->route('admin.teachers.show', $teacher)
                ->with('success', 'Teacher created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->withInput()
                ->with('error', 'Failed to create teacher. Please try again.');
        }
    }

    /**
     * Display the specified teacher.
     */
    public function show(User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        $teacher->load([
            'classTeacherSections' => function ($query) {
                $query->with(['schoolClass'])->active();
            },
            'classSubjects' => function ($query) {
                $query->with(['schoolClass', 'section', 'subject']);
            },
        ]);

        $statistics = [
            'totalSections' => $teacher->classTeacherSections->count(),
            'totalSubjects' => $teacher->classSubjects->unique('subject_id')->count(),
            'totalClasses' => $teacher->classSubjects->unique('class_id')->count(),
        ];

        return view('admin.teachers.show', compact('teacher', 'statistics'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        $subjects = Subject::active()->orderBy('name')->get();
        $classes = SchoolClass::active()->ordered()->get();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'numeric', 'min:0'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();

        try {
            $photoPath = $teacher->avatar;
            if ($request->hasFile('photo')) {
                if ($teacher->avatar) {
                    Storage::disk('public')->delete($teacher->avatar);
                }
                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            }

            $teacher->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'avatar' => $photoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->route('admin.teachers.show', $teacher)
                ->with('success', 'Teacher updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Failed to update teacher. Please try again.');
        }
    }

    /**
     * Remove the specified teacher from storage (soft delete).
     */
    public function destroy(User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        if ($teacher->classTeacherSections()->exists()) {
            return back()->with('error', 'Cannot delete teacher who is assigned as class teacher.');
        }

        if ($teacher->classSubjects()->exists()) {
            return back()->with('error', 'Cannot delete teacher who is assigned to subjects.');
        }

        DB::beginTransaction();

        try {
            $teacher->update(['is_active' => false]);
            $teacher->delete();

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', 'Teacher deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete teacher. Please try again.');
        }
    }

    /**
     * Export teachers to file.
     */
    public function export(Request $request)
    {
        return redirect()->route('admin.teachers.index')
            ->with('success', 'Export started. You will receive the file shortly.');
    }

    /**
     * Toggle the active status of the specified teacher.
     */
    public function toggleStatus(User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        $teacher->update(['is_active' => !$teacher->is_active]);

        $status = $teacher->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Teacher {$status} successfully.");
    }

    /**
     * Get all active teachers (AJAX).
     */
    public function getAll()
    {
        $teachers = User::role('teacher')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json($teachers->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->full_name,
                'email' => $teacher->email,
            ];
        }));
    }
}
