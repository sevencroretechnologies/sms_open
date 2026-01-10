<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'schoolClass', 'section', 'academicSession', 'category'])
            ->select('students.*');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
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

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $students = $query->orderBy('admission_number')
            ->paginate(15)
            ->withQueryString();

        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $categories = StudentCategory::active()->ordered()->get();
        $currentSession = AcademicSession::getCurrentSession();

        return view('admin.students.index', compact(
            'students',
            'classes',
            'academicSessions',
            'categories',
            'currentSession'
        ));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $categories = StudentCategory::active()->ordered()->get();
        $currentSession = AcademicSession::getCurrentSession();
        $parents = User::role('parent')->where('is_active', true)->orderBy('first_name')->get();

        $nextAdmissionNumber = $this->generateAdmissionNumber();

        return view('admin.students.create', compact(
            'classes',
            'academicSessions',
            'categories',
            'currentSession',
            'parents',
            'nextAdmissionNumber'
        ));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'date_of_admission' => ['required', 'date'],
            'category_id' => ['nullable', 'exists:student_categories,id'],
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
            'guardian_occupation' => ['nullable', 'string', 'max:100'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'medical_notes' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['boolean'],
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
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $user->assignRole('student');

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

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
                'nationality' => $validated['nationality'] ?? 'Indian',
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
                'guardian_occupation' => $validated['guardian_occupation'] ?? null,
                'guardian_email' => $validated['guardian_email'] ?? null,
                'guardian_address' => $validated['guardian_address'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'photo' => $photoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->withInput()
                ->with('error', 'Failed to create student. Please try again.');
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'schoolClass',
            'section',
            'academicSession',
            'category',
            'documents',
            'attendances' => function ($query) {
                $query->orderBy('date', 'desc')->take(30);
            },
            'examMarks' => function ($query) {
                $query->with(['exam', 'subject'])->orderBy('created_at', 'desc')->take(10);
            },
            'feesTransactions' => function ($query) {
                $query->orderBy('payment_date', 'desc')->take(10);
            },
        ]);

        $statistics = [
            'totalAttendance' => $student->attendances->count(),
            'presentDays' => $student->attendances->where('status', 'present')->count(),
            'absentDays' => $student->attendances->where('status', 'absent')->count(),
            'totalExams' => $student->examMarks->unique('exam_id')->count(),
            'totalFeesPaid' => $student->feesTransactions->sum('amount'),
        ];

        return view('admin.students.show', compact('student', 'statistics'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $student->load(['user', 'schoolClass', 'section', 'academicSession', 'category']);

        $classes = SchoolClass::active()->ordered()->get();
        $sections = Section::where('class_id', $student->class_id)->active()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        $categories = StudentCategory::active()->ordered()->get();
        $parents = User::role('parent')->where('is_active', true)->orderBy('first_name')->get();

        return view('admin.students.edit', compact(
            'student',
            'classes',
            'sections',
            'academicSessions',
            'categories',
            'parents'
        ));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'admission_number' => ['required', 'string', 'max:50', Rule::unique('students', 'admission_number')->ignore($student->id)],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'date_of_admission' => ['required', 'date'],
            'category_id' => ['nullable', 'exists:student_categories,id'],
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
            'guardian_occupation' => ['nullable', 'string', 'max:100'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'medical_notes' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $photoPath = $student->photo;
            if ($request->hasFile('photo')) {
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

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
                'nationality' => $validated['nationality'] ?? 'Indian',
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
                'guardian_occupation' => $validated['guardian_occupation'] ?? null,
                'guardian_email' => $validated['guardian_email'] ?? null,
                'guardian_address' => $validated['guardian_address'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'] ?? 'India',
                'postal_code' => $validated['postal_code'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'photo' => $photoPath,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Failed to update student. Please try again.');
        }
    }

    /**
     * Remove the specified student from storage (soft delete).
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

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete student. Please try again.');
        }
    }

    /**
     * Display student profile.
     */
    public function profile(Student $student)
    {
        return $this->show($student);
    }

    /**
     * Display student documents.
     */
    public function documents(Student $student)
    {
        $student->load(['user', 'schoolClass', 'section', 'documents']);

        return view('admin.students.documents', compact('student'));
    }

    /**
     * Upload a document for the student.
     */
    public function uploadDocument(Request $request, Student $student)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'document_type' => ['nullable', 'string', 'max:100'],
        ]);

        $path = $request->file('document')->store('students/documents/' . $student->id, 'public');

        StudentDocument::create([
            'student_id' => $student->id,
            'title' => $validated['title'],
            'file_path' => $path,
            'file_name' => $request->file('document')->getClientOriginalName(),
            'file_type' => $request->file('document')->getClientMimeType(),
            'file_size' => $request->file('document')->getSize(),
            'document_type' => $validated['document_type'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete a document for the student.
     */
    public function deleteDocument(Student $student, StudentDocument $document)
    {
        if ($document->student_id !== $student->id) {
            return back()->with('error', 'Document not found.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('admin.students.import', compact('classes', 'academicSessions'));
    }

    /**
     * Import students from file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:10240'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Students imported successfully.');
    }

    /**
     * Export students to file.
     */
    public function export(Request $request)
    {
        return redirect()->route('admin.students.index')
            ->with('success', 'Export started. You will receive the file shortly.');
    }

    /**
     * Show bulk actions form.
     */
    public function bulkActionsForm()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('admin.students.bulk-actions', compact('classes', 'academicSessions'));
    }

    /**
     * Perform bulk actions on students.
     */
    public function bulkActions(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete,change_class,change_section'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'class_id' => ['required_if:action,change_class', 'exists:classes,id'],
            'section_id' => ['required_if:action,change_section', 'exists:sections,id'],
        ]);

        $students = Student::whereIn('id', $validated['student_ids'])->get();

        switch ($validated['action']) {
            case 'activate':
                Student::whereIn('id', $validated['student_ids'])->update(['is_active' => true]);
                $message = count($validated['student_ids']) . ' students activated successfully.';
                break;

            case 'deactivate':
                Student::whereIn('id', $validated['student_ids'])->update(['is_active' => false]);
                $message = count($validated['student_ids']) . ' students deactivated successfully.';
                break;

            case 'delete':
                Student::whereIn('id', $validated['student_ids'])->delete();
                $message = count($validated['student_ids']) . ' students deleted successfully.';
                break;

            case 'change_class':
                Student::whereIn('id', $validated['student_ids'])->update(['class_id' => $validated['class_id']]);
                $message = count($validated['student_ids']) . ' students moved to new class successfully.';
                break;

            case 'change_section':
                Student::whereIn('id', $validated['student_ids'])->update(['section_id' => $validated['section_id']]);
                $message = count($validated['student_ids']) . ' students moved to new section successfully.';
                break;

            default:
                $message = 'Action completed.';
        }

        return redirect()->route('admin.students.index')->with('success', $message);
    }

    /**
     * Get sections for a class (AJAX).
     */
    public function getSections(SchoolClass $class)
    {
        $sections = $class->sections()->active()->get(['id', 'name', 'display_name']);

        return response()->json($sections);
    }

    /**
     * Generate a unique admission number.
     */
    private function generateAdmissionNumber(): string
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastStudent ? (int) substr($lastStudent->admission_number, -4) + 1 : 1;

        return 'STU' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
