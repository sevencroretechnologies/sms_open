<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of academic sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AcademicSession::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sessions = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        return view('admin.academic-sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new academic session.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.academic-sessions.create');
    }

    /**
     * Store a newly created academic session in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:academic_sessions,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if ($validated['is_current'] ?? false) {
            AcademicSession::query()->update(['is_current' => false]);
        }

        $session = AcademicSession::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $validated['is_current'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.academic-sessions.show', $session)
            ->with('success', 'Academic session created successfully.');
    }

    /**
     * Display the specified academic session.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\View\View
     */
    public function show(AcademicSession $academicSession)
    {
        $academicSession->load([
            'classes' => function ($query) {
                $query->active()->ordered()->withCount('students');
            },
            'students' => function ($query) {
                $query->active()->with(['user', 'schoolClass', 'section']);
            },
            'exams' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
        ]);

        $statistics = [
            'totalClasses' => $academicSession->classes->count(),
            'totalStudents' => $academicSession->students->count(),
            'totalExams' => $academicSession->exams->count(),
            'maleStudents' => $academicSession->students->where('gender', 'male')->count(),
            'femaleStudents' => $academicSession->students->where('gender', 'female')->count(),
        ];

        return view('admin.academic-sessions.show', compact('academicSession', 'statistics'));
    }

    /**
     * Show the form for editing the specified academic session.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\View\View
     */
    public function edit(AcademicSession $academicSession)
    {
        return view('admin.academic-sessions.edit', compact('academicSession'));
    }

    /**
     * Update the specified academic session in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AcademicSession $academicSession)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('academic_sessions', 'name')->ignore($academicSession->id)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        if (($validated['is_current'] ?? false) && !$academicSession->is_current) {
            AcademicSession::query()->update(['is_current' => false]);
        }

        $academicSession->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $validated['is_current'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Academic session updated successfully.');
    }

    /**
     * Remove the specified academic session from storage (soft delete).
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AcademicSession $academicSession)
    {
        if ($academicSession->is_current) {
            return back()->with('error', 'Cannot delete the current academic session.');
        }

        if ($academicSession->students()->exists()) {
            return back()->with('error', 'Cannot delete academic session with enrolled students.');
        }

        $academicSession->update(['is_active' => false]);
        $academicSession->delete();

        return redirect()->route('admin.academic-sessions.index')
            ->with('success', 'Academic session deleted successfully.');
    }

    /**
     * Set the specified academic session as current.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setCurrent(AcademicSession $academicSession)
    {
        if (!$academicSession->is_active) {
            return back()->with('error', 'Cannot set an inactive session as current.');
        }

        $academicSession->setAsCurrent();

        return redirect()->route('admin.academic-sessions.show', $academicSession)
            ->with('success', 'Academic session set as current successfully.');
    }

    /**
     * Toggle the active status of the specified academic session.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(AcademicSession $academicSession)
    {
        if ($academicSession->is_current && $academicSession->is_active) {
            return back()->with('error', 'Cannot deactivate the current academic session.');
        }

        $academicSession->update(['is_active' => !$academicSession->is_active]);

        $status = $academicSession->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Academic session {$status} successfully.");
    }
}
