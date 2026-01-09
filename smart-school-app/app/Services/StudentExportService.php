<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Student Export Service
 * 
 * Prompt 420: Create Student Export Service
 * 
 * Handles exporting student data to various formats with filtering options.
 * Supports class, section, session, and status filters.
 * 
 * Features:
 * - Export student list with filters
 * - Export student details with guardian info
 * - Export student documents list
 * - Support PDF, Excel, CSV formats
 */
class StudentExportService
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export student list with filters.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportStudentList(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getStudentListData($filters);
        $filename = 'students_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Student List Report');
    }

    /**
     * Export student details with guardian information.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportStudentDetails(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getStudentDetailsData($filters);
        $filename = 'student_details_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Student Details Report');
    }

    /**
     * Export students by class.
     *
     * @param int $classId
     * @param int|null $sectionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportByClass(int $classId, ?int $sectionId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters = ['class_id' => $classId];
        if ($sectionId) {
            $filters['section_id'] = $sectionId;
        }
        
        return $this->exportStudentList($filters, $format);
    }

    /**
     * Export students by academic session.
     *
     * @param int $sessionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportBySession(int $sessionId, string $format = 'xlsx'): Response|StreamedResponse
    {
        return $this->exportStudentList(['academic_session_id' => $sessionId], $format);
    }

    /**
     * Export active students only.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportActiveStudents(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters['is_active'] = true;
        return $this->exportStudentList($filters, $format);
    }

    /**
     * Export inactive/archived students.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportInactiveStudents(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters['is_active'] = false;
        return $this->exportStudentList($filters, $format);
    }

    /**
     * Get student list data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getStudentListData(array $filters = []): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section', 'academicSession', 'category']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('admission_number')->get()->map(function ($student) {
            return [
                'Admission No' => $student->admission_number,
                'Roll No' => $student->roll_number ?? '',
                'First Name' => $student->user?->first_name ?? '',
                'Last Name' => $student->user?->last_name ?? '',
                'Email' => $student->user?->email ?? '',
                'Phone' => $student->user?->phone ?? '',
                'Gender' => ucfirst($student->gender ?? ''),
                'Date of Birth' => $student->date_of_birth?->format('Y-m-d') ?? '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
                'Session' => $student->academicSession?->name ?? '',
                'Category' => $student->category?->name ?? '',
                'Admission Date' => $student->date_of_admission?->format('Y-m-d') ?? '',
                'Status' => $student->is_active ? 'Active' : 'Inactive',
            ];
        });
    }

    /**
     * Get student details data with guardian info for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getStudentDetailsData(array $filters = []): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section', 'academicSession', 'category']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('admission_number')->get()->map(function ($student) {
            return [
                'Admission No' => $student->admission_number,
                'Roll No' => $student->roll_number ?? '',
                'First Name' => $student->user?->first_name ?? '',
                'Last Name' => $student->user?->last_name ?? '',
                'Email' => $student->user?->email ?? '',
                'Phone' => $student->user?->phone ?? '',
                'Gender' => ucfirst($student->gender ?? ''),
                'Date of Birth' => $student->date_of_birth?->format('Y-m-d') ?? '',
                'Blood Group' => $student->blood_group ?? '',
                'Religion' => $student->religion ?? '',
                'Nationality' => $student->nationality ?? '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
                'Session' => $student->academicSession?->name ?? '',
                'Category' => $student->category?->name ?? '',
                'Father Name' => $student->father_name ?? '',
                'Father Phone' => $student->father_phone ?? '',
                'Father Occupation' => $student->father_occupation ?? '',
                'Mother Name' => $student->mother_name ?? '',
                'Mother Phone' => $student->mother_phone ?? '',
                'Mother Occupation' => $student->mother_occupation ?? '',
                'Guardian Name' => $student->guardian_name ?? '',
                'Guardian Phone' => $student->guardian_phone ?? '',
                'Guardian Relation' => $student->guardian_relation ?? '',
                'Address' => $student->address ?? '',
                'City' => $student->city ?? '',
                'State' => $student->state ?? '',
                'Postal Code' => $student->postal_code ?? '',
                'Admission Date' => $student->date_of_admission?->format('Y-m-d') ?? '',
                'Admission Type' => ucfirst($student->admission_type ?? ''),
                'Previous School' => $student->previous_school_name ?? '',
                'Is RTE' => $student->is_rte ? 'Yes' : 'No',
                'Status' => $student->is_active ? 'Active' : 'Inactive',
            ];
        });
    }

    /**
     * Apply filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['admission_type'])) {
            $query->where('admission_type', $filters['admission_type']);
        }

        if (!empty($filters['is_rte'])) {
            $query->where('is_rte', $filters['is_rte']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_admission', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_admission', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
    }

    /**
     * Get export statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getExportStatistics(array $filters = []): array
    {
        $query = Student::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();
        $active = (clone $query)->where('is_active', true)->count();
        $inactive = (clone $query)->where('is_active', false)->count();
        $male = (clone $query)->where('gender', 'male')->count();
        $female = (clone $query)->where('gender', 'female')->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'male' => $male,
            'female' => $female,
        ];
    }
}
