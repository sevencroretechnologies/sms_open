<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students with pagination, search, and filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'schoolClass', 'section', 'academicSession', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->orderBy('admission_number')->paginate(15)->withQueryString();

        $classes = SchoolClass::active()->ordered()->get();
        $sections = Section::active()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('students.index', compact('students', 'classes', 'sections', 'academicSessions'));
    }

    /**
     * Show the form for creating a new student.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $sections = Section::active()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $currentSession = AcademicSession::getCurrentSession();

        return view('students.create', compact('classes', 'sections', 'academicSessions', 'currentSession'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'roll_number' => ['nullable', 'string', 'max:20'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date_of_admission' => ['required', 'date'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:50'],
            'caste' => ['nullable', 'string', 'max:50'],
            'nationality' => ['required', 'string', 'max:50'],
            'mother_tongue' => ['nullable', 'string', 'max:50'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'category_id' => ['nullable', 'exists:student_categories,id'],
            'is_rte' => ['boolean'],
            'admission_type' => ['required', 'in:new,transfer'],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'uuid' => Str::uuid(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('password123'),
                'is_active' => true,
                'country' => $validated['country'],
            ]);

            $user->assignRole('student');

            $student = Student::create([
                'user_id' => $user->id,
                'academic_session_id' => $validated['academic_session_id'],
                'admission_number' => $validated['admission_number'],
                'roll_number' => $validated['roll_number'] ?? null,
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'date_of_admission' => $validated['date_of_admission'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'blood_group' => $validated['blood_group'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'caste' => $validated['caste'] ?? null,
                'nationality' => $validated['nationality'],
                'mother_tongue' => $validated['mother_tongue'] ?? null,
                'father_name' => $validated['father_name'] ?? null,
                'father_phone' => $validated['father_phone'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'father_email' => $validated['father_email'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_phone' => $validated['mother_phone'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'mother_email' => $validated['mother_email'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
                'guardian_relation' => $validated['guardian_relation'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'is_rte' => $validated['is_rte'] ?? false,
                'admission_type' => $validated['admission_type'],
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create student. Please try again.');
        }
    }

    /**
     * Display the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'schoolClass',
            'section',
            'academicSession',
            'category',
            'siblings',
            'documents',
            'attendances' => function ($query) {
                $query->orderBy('attendance_date', 'desc')->take(30);
            },
            'examMarks' => function ($query) {
                $query->with(['examSchedule.exam', 'examSchedule.subject', 'grade'])
                    ->orderBy('created_at', 'desc')->take(10);
            },
            'feesAllotments' => function ($query) {
                $query->with(['feesMaster.feesType'])->orderBy('created_at', 'desc');
            },
            'feesTransactions' => function ($query) {
                $query->orderBy('payment_date', 'desc')->take(10);
            },
        ]);

        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function edit(Student $student)
    {
        $student->load(['user', 'schoolClass', 'section', 'academicSession', 'category']);

        $classes = SchoolClass::active()->ordered()->get();
        $sections = Section::active()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('students.edit', compact('student', 'classes', 'sections', 'academicSessions'));
    }

    /**
     * Update the specified student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'admission_number' => ['required', 'string', 'max:50', Rule::unique('students', 'admission_number')->ignore($student->id)],
            'roll_number' => ['nullable', 'string', 'max:20'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date_of_admission' => ['required', 'date'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:50'],
            'caste' => ['nullable', 'string', 'max:50'],
            'nationality' => ['required', 'string', 'max:50'],
            'mother_tongue' => ['nullable', 'string', 'max:50'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'category_id' => ['nullable', 'exists:student_categories,id'],
            'is_rte' => ['boolean'],
            'admission_type' => ['required', 'in:new,transfer'],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();

        try {
            $student->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'country' => $validated['country'],
            ]);

            $student->update([
                'academic_session_id' => $validated['academic_session_id'],
                'admission_number' => $validated['admission_number'],
                'roll_number' => $validated['roll_number'] ?? null,
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'date_of_admission' => $validated['date_of_admission'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'blood_group' => $validated['blood_group'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'caste' => $validated['caste'] ?? null,
                'nationality' => $validated['nationality'],
                'mother_tongue' => $validated['mother_tongue'] ?? null,
                'father_name' => $validated['father_name'] ?? null,
                'father_phone' => $validated['father_phone'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'father_email' => $validated['father_email'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_phone' => $validated['mother_phone'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'mother_email' => $validated['mother_email'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
                'guardian_relation' => $validated['guardian_relation'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'is_rte' => $validated['is_rte'] ?? false,
                'admission_type' => $validated['admission_type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update student. Please try again.');
        }
    }

    /**
     * Remove the specified student from storage (soft delete).
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Student $student)
    {
        DB::beginTransaction();

        try {
            $student->update(['is_active' => false]);
            $student->delete();

            $student->user->update(['is_active' => false]);
            $student->user->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete student. Please try again.');
        }
    }

    /**
     * Search students by name, admission number, or father name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where(function ($query) use ($search) {
                $query->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->active()
            ->take(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'text' => "{$student->user->full_name} ({$student->admission_number}) - {$student->schoolClass->display_name} {$student->section->name}",
                    'admission_number' => $student->admission_number,
                    'name' => $student->user->full_name,
                    'class' => $student->schoolClass->display_name,
                    'section' => $student->section->name,
                ];
            });

        return response()->json($students);
    }

    /**
     * Show the form for promoting a student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function showPromoteForm(Student $student)
    {
        $student->load(['user', 'schoolClass', 'section', 'academicSession']);

        $classes = SchoolClass::active()->ordered()->get();
        $sections = Section::active()->get();
        $academicSessions = AcademicSession::active()
            ->where('id', '!=', $student->academic_session_id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('students.promote', compact('student', 'classes', 'sections', 'academicSessions'));
    }

    /**
     * Promote a student to the next class/session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function promote(Request $request, Student $student)
    {
        $validated = $request->validate([
            'to_session_id' => ['required', 'exists:academic_sessions,id'],
            'to_class_id' => ['required', 'exists:classes,id'],
            'to_section_id' => ['required', 'exists:sections,id'],
            'promotion_status' => ['required', 'in:promoted,demoted,retained'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();

        try {
            DB::table('student_promotions')->insert([
                'student_id' => $student->id,
                'from_session_id' => $student->academic_session_id,
                'from_class_id' => $student->class_id,
                'from_section_id' => $student->section_id,
                'to_session_id' => $validated['to_session_id'],
                'to_class_id' => $validated['to_class_id'],
                'to_section_id' => $validated['to_section_id'],
                'promotion_status' => $validated['promotion_status'],
                'remarks' => $validated['remarks'] ?? null,
                'promoted_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $student->update([
                'academic_session_id' => $validated['to_session_id'],
                'class_id' => $validated['to_class_id'],
                'section_id' => $validated['to_section_id'],
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student promoted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to promote student. Please try again.');
        }
    }

    /**
     * Get sections for a specific class (AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSections(Request $request)
    {
        $classId = $request->get('class_id');

        $sections = Section::where('class_id', $classId)
            ->active()
            ->get(['id', 'name', 'display_name']);

        return response()->json($sections);
    }
}
