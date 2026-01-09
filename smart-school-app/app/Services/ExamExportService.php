<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exam Export Service
 * 
 * Prompt 422: Create Exam Export Service
 * 
 * Handles exporting exam data to various formats with filtering options.
 * Supports exam, class, section, and subject filters.
 * 
 * Features:
 * - Export exam schedules
 * - Export exam marks/results
 * - Export student result cards
 * - Export class-wise result summary
 * - Support PDF, Excel, CSV formats
 */
class ExamExportService
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export exam schedules.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportExamSchedules(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getExamScheduleData($filters);
        $filename = 'exam_schedules_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Exam Schedule Report');
    }

    /**
     * Export exam marks/results.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportExamMarks(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getExamMarksData($filters);
        $filename = 'exam_marks_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Exam Marks Report');
    }

    /**
     * Export results for a specific exam.
     *
     * @param int $examId
     * @param int|null $classId
     * @param int|null $sectionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportByExam(int $examId, ?int $classId = null, ?int $sectionId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters = ['exam_id' => $examId];
        if ($classId) {
            $filters['class_id'] = $classId;
        }
        if ($sectionId) {
            $filters['section_id'] = $sectionId;
        }
        
        return $this->exportExamMarks($filters, $format);
    }

    /**
     * Export class-wise result summary.
     *
     * @param int $examId
     * @param int $classId
     * @param int|null $sectionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportClassResultSummary(int $examId, int $classId, ?int $sectionId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getClassResultSummaryData($examId, $classId, $sectionId);
        $filename = 'class_result_summary_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Class Result Summary');
    }

    /**
     * Export subject-wise result analysis.
     *
     * @param int $examId
     * @param int|null $subjectId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportSubjectAnalysis(int $examId, ?int $subjectId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getSubjectAnalysisData($examId, $subjectId);
        $filename = 'subject_analysis_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Subject-wise Analysis');
    }

    /**
     * Get exam schedule data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getExamScheduleData(array $filters = []): Collection
    {
        $query = ExamSchedule::query()
            ->with(['exam', 'schoolClass', 'section', 'subject']);

        $this->applyScheduleFilters($query, $filters);

        return $query->orderBy('exam_date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) {
                return [
                    'Exam' => $schedule->exam?->name ?? '',
                    'Class' => $schedule->schoolClass?->name ?? '',
                    'Section' => $schedule->section?->name ?? '',
                    'Subject' => $schedule->subject?->name ?? '',
                    'Date' => $schedule->exam_date?->format('Y-m-d') ?? '',
                    'Start Time' => $schedule->start_time ?? '',
                    'End Time' => $schedule->end_time ?? '',
                    'Room' => $schedule->room_number ?? '',
                    'Full Marks' => $schedule->full_marks ?? '',
                    'Passing Marks' => $schedule->passing_marks ?? '',
                ];
            });
    }

    /**
     * Get exam marks data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getExamMarksData(array $filters = []): Collection
    {
        $query = ExamMark::query()
            ->with(['student.user', 'examSchedule.exam', 'examSchedule.subject', 'examSchedule.schoolClass', 'examSchedule.section']);

        $this->applyMarksFilters($query, $filters);

        return $query->get()->map(function ($mark) {
            $schedule = $mark->examSchedule;
            $percentage = $schedule?->full_marks > 0 
                ? round(($mark->obtained_marks / $schedule->full_marks) * 100, 2) 
                : 0;
            $result = $schedule?->passing_marks && $mark->obtained_marks >= $schedule->passing_marks 
                ? 'Pass' 
                : 'Fail';

            return [
                'Exam' => $schedule?->exam?->name ?? '',
                'Class' => $schedule?->schoolClass?->name ?? '',
                'Section' => $schedule?->section?->name ?? '',
                'Subject' => $schedule?->subject?->name ?? '',
                'Admission No' => $mark->student?->admission_number ?? '',
                'Student Name' => $mark->student?->user 
                    ? "{$mark->student->user->first_name} {$mark->student->user->last_name}" 
                    : '',
                'Full Marks' => $schedule?->full_marks ?? '',
                'Passing Marks' => $schedule?->passing_marks ?? '',
                'Obtained Marks' => $mark->obtained_marks,
                'Percentage' => "{$percentage}%",
                'Result' => $result,
                'Remarks' => $mark->remarks ?? '',
            ];
        });
    }

    /**
     * Get class result summary data.
     *
     * @param int $examId
     * @param int $classId
     * @param int|null $sectionId
     * @return Collection
     */
    protected function getClassResultSummaryData(int $examId, int $classId, ?int $sectionId = null): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section'])
            ->where('class_id', $classId)
            ->where('is_active', true);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $students = $query->orderBy('admission_number')->get();

        return $students->map(function ($student) use ($examId) {
            $marks = ExamMark::whereHas('examSchedule', function ($q) use ($examId) {
                $q->where('exam_id', $examId);
            })->where('student_id', $student->id)->with('examSchedule')->get();

            $totalObtained = 0;
            $totalFull = 0;
            $subjectCount = 0;
            $passedSubjects = 0;

            foreach ($marks as $mark) {
                $totalObtained += $mark->obtained_marks;
                $totalFull += $mark->examSchedule?->full_marks ?? 0;
                $subjectCount++;
                
                if ($mark->examSchedule?->passing_marks && $mark->obtained_marks >= $mark->examSchedule->passing_marks) {
                    $passedSubjects++;
                }
            }

            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
            $overallResult = $passedSubjects === $subjectCount && $subjectCount > 0 ? 'Pass' : 'Fail';

            return [
                'Admission No' => $student->admission_number,
                'Student Name' => $student->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
                'Subjects' => $subjectCount,
                'Total Marks' => $totalFull,
                'Obtained Marks' => $totalObtained,
                'Percentage' => "{$percentage}%",
                'Passed Subjects' => $passedSubjects,
                'Failed Subjects' => $subjectCount - $passedSubjects,
                'Result' => $overallResult,
            ];
        });
    }

    /**
     * Get subject-wise analysis data.
     *
     * @param int $examId
     * @param int|null $subjectId
     * @return Collection
     */
    protected function getSubjectAnalysisData(int $examId, ?int $subjectId = null): Collection
    {
        $query = ExamSchedule::query()
            ->with(['subject', 'schoolClass', 'section'])
            ->where('exam_id', $examId);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $schedules = $query->get();

        return $schedules->map(function ($schedule) {
            $marks = ExamMark::where('exam_schedule_id', $schedule->id)->get();
            
            $totalStudents = $marks->count();
            $passed = $marks->filter(fn($m) => $m->obtained_marks >= ($schedule->passing_marks ?? 0))->count();
            $failed = $totalStudents - $passed;
            $highest = $marks->max('obtained_marks') ?? 0;
            $lowest = $marks->min('obtained_marks') ?? 0;
            $average = $totalStudents > 0 ? round($marks->avg('obtained_marks'), 2) : 0;
            $passPercentage = $totalStudents > 0 ? round(($passed / $totalStudents) * 100, 2) : 0;

            return [
                'Subject' => $schedule->subject?->name ?? '',
                'Class' => $schedule->schoolClass?->name ?? '',
                'Section' => $schedule->section?->name ?? '',
                'Full Marks' => $schedule->full_marks ?? '',
                'Passing Marks' => $schedule->passing_marks ?? '',
                'Total Students' => $totalStudents,
                'Passed' => $passed,
                'Failed' => $failed,
                'Pass %' => "{$passPercentage}%",
                'Highest' => $highest,
                'Lowest' => $lowest,
                'Average' => $average,
            ];
        });
    }

    /**
     * Apply filters to schedule query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyScheduleFilters($query, array $filters): void
    {
        if (!empty($filters['exam_id'])) {
            $query->where('exam_id', $filters['exam_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('exam_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('exam_date', '<=', $filters['date_to']);
        }
    }

    /**
     * Apply filters to marks query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyMarksFilters($query, array $filters): void
    {
        if (!empty($filters['exam_id'])) {
            $query->whereHas('examSchedule', function ($q) use ($filters) {
                $q->where('exam_id', $filters['exam_id']);
            });
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('examSchedule', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['section_id'])) {
            $query->whereHas('examSchedule', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        if (!empty($filters['subject_id'])) {
            $query->whereHas('examSchedule', function ($q) use ($filters) {
                $q->where('subject_id', $filters['subject_id']);
            });
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
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
        $marksQuery = ExamMark::query();
        $this->applyMarksFilters($marksQuery, $filters);

        $total = $marksQuery->count();
        
        return [
            'total_records' => $total,
        ];
    }
}
