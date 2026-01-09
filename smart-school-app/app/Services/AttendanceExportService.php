<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Attendance Export Service
 * 
 * Prompt 421: Create Attendance Export Service
 * 
 * Handles exporting attendance data to various formats with filtering options.
 * Supports date range, class, section, and student filters.
 * 
 * Features:
 * - Export daily attendance records
 * - Export attendance summary by student
 * - Export attendance statistics by class
 * - Support PDF, Excel, CSV formats
 */
class AttendanceExportService
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export daily attendance records.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportDailyAttendance(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getDailyAttendanceData($filters);
        $filename = 'attendance_daily_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Daily Attendance Report');
    }

    /**
     * Export attendance summary by student.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportStudentSummary(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getStudentSummaryData($filters);
        $filename = 'attendance_summary_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Student Attendance Summary');
    }

    /**
     * Export attendance for a specific date.
     *
     * @param string $date
     * @param int|null $classId
     * @param int|null $sectionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportByDate(string $date, ?int $classId = null, ?int $sectionId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters = ['date' => $date];
        if ($classId) {
            $filters['class_id'] = $classId;
        }
        if ($sectionId) {
            $filters['section_id'] = $sectionId;
        }
        
        return $this->exportDailyAttendance($filters, $format);
    }

    /**
     * Export attendance for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportByDateRange(string $startDate, string $endDate, array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters['date_from'] = $startDate;
        $filters['date_to'] = $endDate;
        
        return $this->exportDailyAttendance($filters, $format);
    }

    /**
     * Export monthly attendance report.
     *
     * @param int $year
     * @param int $month
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportMonthlyReport(int $year, int $month, array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $filters['date_from'] = $startDate->format('Y-m-d');
        $filters['date_to'] = $endDate->format('Y-m-d');
        
        $data = $this->getMonthlyAttendanceData($filters, $year, $month);
        $filename = "attendance_monthly_{$year}_{$month}_" . now()->format('His');
        
        return $this->exportService->export($data, $format, $filename, "Monthly Attendance Report - {$startDate->format('F Y')}");
    }

    /**
     * Get daily attendance data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getDailyAttendanceData(array $filters = []): Collection
    {
        $query = Attendance::query()
            ->with(['student.user', 'schoolClass', 'section']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('date', 'desc')
            ->orderBy('student_id')
            ->get()
            ->map(function ($attendance) {
                return [
                    'Date' => $attendance->date?->format('Y-m-d') ?? '',
                    'Admission No' => $attendance->student?->admission_number ?? '',
                    'Student Name' => $attendance->student?->user 
                        ? "{$attendance->student->user->first_name} {$attendance->student->user->last_name}" 
                        : '',
                    'Class' => $attendance->schoolClass?->name ?? '',
                    'Section' => $attendance->section?->name ?? '',
                    'Status' => ucfirst($attendance->status ?? ''),
                    'Remarks' => $attendance->remarks ?? '',
                    'Recorded At' => $attendance->created_at?->format('Y-m-d H:i') ?? '',
                ];
            });
    }

    /**
     * Get student attendance summary data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getStudentSummaryData(array $filters = []): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section'])
            ->where('is_active', true);

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        $students = $query->orderBy('admission_number')->get();

        return $students->map(function ($student) use ($filters) {
            $attendanceQuery = Attendance::where('student_id', $student->id);
            
            if (!empty($filters['date_from'])) {
                $attendanceQuery->whereDate('date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $attendanceQuery->whereDate('date', '<=', $filters['date_to']);
            }

            $total = $attendanceQuery->count();
            $present = (clone $attendanceQuery)->where('status', 'present')->count();
            $absent = (clone $attendanceQuery)->where('status', 'absent')->count();
            $late = (clone $attendanceQuery)->where('status', 'late')->count();
            $halfDay = (clone $attendanceQuery)->where('status', 'half_day')->count();

            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            return [
                'Admission No' => $student->admission_number,
                'Student Name' => $student->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
                'Total Days' => $total,
                'Present' => $present,
                'Absent' => $absent,
                'Late' => $late,
                'Half Day' => $halfDay,
                'Attendance %' => "{$percentage}%",
            ];
        });
    }

    /**
     * Get monthly attendance data with day-wise breakdown.
     *
     * @param array $filters
     * @param int $year
     * @param int $month
     * @return Collection
     */
    protected function getMonthlyAttendanceData(array $filters, int $year, int $month): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section'])
            ->where('is_active', true);

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        $students = $query->orderBy('admission_number')->get();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        return $students->map(function ($student) use ($year, $month, $daysInMonth) {
            $row = [
                'Admission No' => $student->admission_number,
                'Student Name' => $student->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
            ];

            $attendances = Attendance::where('student_id', $student->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(fn($a) => $a->date->format('d'));

            $presentCount = 0;
            $totalDays = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                $attendance = $attendances->get($dayKey);
                
                if ($attendance) {
                    $totalDays++;
                    $status = match ($attendance->status) {
                        'present' => 'P',
                        'absent' => 'A',
                        'late' => 'L',
                        'half_day' => 'H',
                        default => '-',
                    };
                    if ($attendance->status === 'present') {
                        $presentCount++;
                    }
                } else {
                    $status = '-';
                }
                
                $row["Day {$day}"] = $status;
            }

            $row['Total Present'] = $presentCount;
            $row['Total Days'] = $totalDays;
            $row['Percentage'] = $totalDays > 0 ? round(($presentCount / $totalDays) * 100, 2) . '%' : '0%';

            return $row;
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

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }
    }

    /**
     * Get attendance statistics for export preview.
     *
     * @param array $filters
     * @return array
     */
    public function getExportStatistics(array $filters = []): array
    {
        $query = Attendance::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();

        return [
            'total_records' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}
