<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Attendance Report Service
 * 
 * Prompt 428: Create Attendance Report Service
 * 
 * Generates attendance reports in PDF format.
 * Supports daily, monthly, and student-wise reports.
 * 
 * Features:
 * - Generate daily attendance report
 * - Generate monthly attendance report
 * - Generate student attendance report
 * - Generate class attendance summary
 * - Include attendance statistics and charts
 */
class AttendanceReportService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate daily attendance report.
     *
     * @param string $date
     * @param int|null $classId
     * @param int|null $sectionId
     * @return Response
     */
    public function generateDailyReport(string $date, ?int $classId = null, ?int $sectionId = null): Response
    {
        $attendances = $this->getDailyAttendanceData($date, $classId, $sectionId);
        $statistics = $this->calculateDailyStatistics($attendances);
        
        $html = $this->buildDailyReportHtml($date, $attendances, $statistics, $classId, $sectionId);
        $filename = "daily_attendance_{$date}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'landscape');
    }

    /**
     * Generate monthly attendance report.
     *
     * @param int $year
     * @param int $month
     * @param int|null $classId
     * @param int|null $sectionId
     * @return Response
     */
    public function generateMonthlyReport(int $year, int $month, ?int $classId = null, ?int $sectionId = null): Response
    {
        $data = $this->getMonthlyAttendanceData($year, $month, $classId, $sectionId);
        
        $html = $this->buildMonthlyReportHtml($year, $month, $data, $classId, $sectionId);
        $filename = "monthly_attendance_{$year}_{$month}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'landscape');
    }

    /**
     * Generate student attendance report.
     *
     * @param int $studentId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Response
     */
    public function generateStudentReport(int $studentId, ?string $startDate = null, ?string $endDate = null): Response
    {
        $student = Student::with(['user', 'schoolClass', 'section', 'academicSession'])->findOrFail($studentId);
        $data = $this->getStudentAttendanceData($studentId, $startDate, $endDate);
        $statistics = $this->calculateStudentStatistics($data);
        
        $html = $this->buildStudentReportHtml($student, $data, $statistics, $startDate, $endDate);
        $filename = "student_attendance_{$student->admission_number}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate class attendance summary report.
     *
     * @param int $classId
     * @param int|null $sectionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Response
     */
    public function generateClassSummaryReport(int $classId, ?int $sectionId = null, ?string $startDate = null, ?string $endDate = null): Response
    {
        $data = $this->getClassSummaryData($classId, $sectionId, $startDate, $endDate);
        
        $html = $this->buildClassSummaryHtml($data, $classId, $sectionId, $startDate, $endDate);
        $filename = "class_attendance_summary_" . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Get daily attendance data.
     *
     * @param string $date
     * @param int|null $classId
     * @param int|null $sectionId
     * @return Collection
     */
    protected function getDailyAttendanceData(string $date, ?int $classId, ?int $sectionId): Collection
    {
        $query = Attendance::with(['student.user', 'schoolClass', 'section'])
            ->whereDate('date', $date);

        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        return $query->orderBy('student_id')->get();
    }

    /**
     * Get monthly attendance data.
     *
     * @param int $year
     * @param int $month
     * @param int|null $classId
     * @param int|null $sectionId
     * @return array
     */
    protected function getMonthlyAttendanceData(int $year, int $month, ?int $classId, ?int $sectionId): array
    {
        $query = Student::with(['user', 'schoolClass', 'section'])
            ->where('is_active', true);

        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $students = $query->orderBy('admission_number')->get();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $data = [];
        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(fn($a) => $a->date->format('d'));

            $studentData = [
                'student' => $student,
                'days' => [],
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'half_day' => 0,
            ];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
                $attendance = $attendances->get($dayKey);
                
                if ($attendance) {
                    $studentData['days'][$day] = $attendance->status;
                    $studentData[$attendance->status]++;
                } else {
                    $studentData['days'][$day] = null;
                }
            }

            $totalDays = $studentData['present'] + $studentData['absent'] + $studentData['late'] + $studentData['half_day'];
            $studentData['percentage'] = $totalDays > 0 
                ? round(($studentData['present'] / $totalDays) * 100, 2) 
                : 0;

            $data[] = $studentData;
        }

        return [
            'students' => $data,
            'days_in_month' => $daysInMonth,
        ];
    }

    /**
     * Get student attendance data.
     *
     * @param int $studentId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    protected function getStudentAttendanceData(int $studentId, ?string $startDate, ?string $endDate): Collection
    {
        $query = Attendance::where('student_id', $studentId)
            ->orderBy('date', 'desc');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get class summary data.
     *
     * @param int $classId
     * @param int|null $sectionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    protected function getClassSummaryData(int $classId, ?int $sectionId, ?string $startDate, ?string $endDate): Collection
    {
        $query = Student::with(['user', 'section'])
            ->where('class_id', $classId)
            ->where('is_active', true);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $students = $query->orderBy('admission_number')->get();

        return $students->map(function ($student) use ($startDate, $endDate) {
            $attendanceQuery = Attendance::where('student_id', $student->id);
            
            if ($startDate) {
                $attendanceQuery->whereDate('date', '>=', $startDate);
            }
            if ($endDate) {
                $attendanceQuery->whereDate('date', '<=', $endDate);
            }

            $total = $attendanceQuery->count();
            $present = (clone $attendanceQuery)->where('status', 'present')->count();
            $absent = (clone $attendanceQuery)->where('status', 'absent')->count();
            $late = (clone $attendanceQuery)->where('status', 'late')->count();
            $halfDay = (clone $attendanceQuery)->where('status', 'half_day')->count();

            return [
                'student' => $student,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Calculate daily statistics.
     *
     * @param Collection $attendances
     * @return array
     */
    protected function calculateDailyStatistics(Collection $attendances): array
    {
        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate student statistics.
     *
     * @param Collection $attendances
     * @return array
     */
    protected function calculateStudentStatistics(Collection $attendances): array
    {
        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();

        return [
            'total_days' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Build daily report HTML.
     *
     * @param string $date
     * @param Collection $attendances
     * @param array $statistics
     * @param int|null $classId
     * @param int|null $sectionId
     * @return string
     */
    protected function buildDailyReportHtml(string $date, Collection $attendances, array $statistics, ?int $classId, ?int $sectionId): string
    {
        $schoolName = config('app.name', 'Smart School');
        $formattedDate = Carbon::parse($date)->format('F j, Y');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $rows = '';
        $sn = 1;
        foreach ($attendances as $attendance) {
            $studentName = $attendance->student?->user 
                ? "{$attendance->student->user->first_name} {$attendance->student->user->last_name}" 
                : '';
            $statusClass = match ($attendance->status) {
                'present' => 'status-present',
                'absent' => 'status-absent',
                'late' => 'status-late',
                'half_day' => 'status-half',
                default => '',
            };
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$attendance->student?->admission_number}</td>
    <td>{$studentName}</td>
    <td>{$attendance->schoolClass?->name}</td>
    <td>{$attendance->section?->name}</td>
    <td class="text-center {$statusClass}">{$attendance->status}</td>
    <td>{$attendance->remarks}</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance Report - {$date}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; padding: 15px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; color: #4f46e5; }
        .header h2 { font-size: 14px; color: #333; margin-top: 5px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 9px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .stat-box { text-align: center; padding: 5px 15px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 8px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 8px 5px; text-align: left; font-size: 9px; }
        td { padding: 6px 5px; border: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .status-half { color: #17a2b8; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Daily Attendance Report</h2>
        <p style="margin-top: 5px; color: #666;">{$formattedDate}</p>
    </div>

    <div class="meta">
        <span>Generated: {$generatedAt}</span>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total']}</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['present']}</div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #dc3545;">{$statistics['absent']}</div>
            <div class="stat-label">Absent</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #ffc107;">{$statistics['late']}</div>
            <div class="stat-label">Late</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['attendance_rate']}%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                <th class="text-center">Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build monthly report HTML.
     *
     * @param int $year
     * @param int $month
     * @param array $data
     * @param int|null $classId
     * @param int|null $sectionId
     * @return string
     */
    protected function buildMonthlyReportHtml(int $year, int $month, array $data, ?int $classId, ?int $sectionId): string
    {
        $schoolName = config('app.name', 'Smart School');
        $monthName = Carbon::create($year, $month, 1)->format('F Y');
        $generatedAt = now()->format('F j, Y \a\t g:i A');
        $daysInMonth = $data['days_in_month'];

        $dayHeaders = '';
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayHeaders .= "<th class='text-center' style='width: 20px; font-size: 7px;'>{$day}</th>";
        }

        $rows = '';
        $sn = 1;
        foreach ($data['students'] as $studentData) {
            $student = $studentData['student'];
            $studentName = $student->user 
                ? "{$student->user->first_name} {$student->user->last_name}" 
                : '';

            $dayCells = '';
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $status = $studentData['days'][$day] ?? '-';
                $symbol = match ($status) {
                    'present' => 'P',
                    'absent' => 'A',
                    'late' => 'L',
                    'half_day' => 'H',
                    default => '-',
                };
                $class = match ($status) {
                    'present' => 'status-present',
                    'absent' => 'status-absent',
                    'late' => 'status-late',
                    'half_day' => 'status-half',
                    default => '',
                };
                $dayCells .= "<td class='text-center {$class}' style='font-size: 7px;'>{$symbol}</td>";
            }

            $rows .= <<<HTML
<tr>
    <td class="text-center" style="font-size: 8px;">{$sn}</td>
    <td style="font-size: 8px;">{$student->admission_number}</td>
    <td style="font-size: 8px;">{$studentName}</td>
    {$dayCells}
    <td class="text-center" style="font-size: 8px;">{$studentData['present']}</td>
    <td class="text-center" style="font-size: 8px;">{$studentData['absent']}</td>
    <td class="text-center" style="font-size: 8px;">{$studentData['percentage']}%</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Attendance Report - {$monthName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 10px; }
        .header h1 { font-size: 16px; color: #4f46e5; }
        .header h2 { font-size: 12px; color: #333; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 4px 2px; text-align: left; font-size: 8px; }
        td { padding: 3px 2px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .status-half { color: #17a2b8; font-weight: bold; }
        .footer { margin-top: 15px; text-align: center; font-size: 7px; color: #666; }
        .legend { margin: 10px 0; font-size: 8px; }
        .legend span { margin-right: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Monthly Attendance Report - {$monthName}</h2>
    </div>

    <div class="legend">
        <span class="status-present">P = Present</span>
        <span class="status-absent">A = Absent</span>
        <span class="status-late">L = Late</span>
        <span class="status-half">H = Half Day</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">S.No</th>
                <th style="width: 60px;">Adm No</th>
                <th style="width: 100px;">Name</th>
                {$dayHeaders}
                <th class="text-center" style="width: 25px;">P</th>
                <th class="text-center" style="width: 25px;">A</th>
                <th class="text-center" style="width: 35px;">%</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build student report HTML.
     *
     * @param Student $student
     * @param Collection $attendances
     * @param array $statistics
     * @param string|null $startDate
     * @param string|null $endDate
     * @return string
     */
    protected function buildStudentReportHtml(Student $student, Collection $attendances, array $statistics, ?string $startDate, ?string $endDate): string
    {
        $schoolName = config('app.name', 'Smart School');
        $studentName = $student->user ? "{$student->user->first_name} {$student->user->last_name}" : '';
        $className = $student->schoolClass?->name ?? '';
        $sectionName = $student->section?->name ?? '';
        $generatedAt = now()->format('F j, Y');
        $dateRange = $startDate && $endDate ? "{$startDate} to {$endDate}" : 'All Time';

        $rows = '';
        foreach ($attendances as $attendance) {
            $statusClass = match ($attendance->status) {
                'present' => 'status-present',
                'absent' => 'status-absent',
                'late' => 'status-late',
                'half_day' => 'status-half',
                default => '',
            };
            
            $rows .= <<<HTML
<tr>
    <td>{$attendance->date?->format('Y-m-d')}</td>
    <td>{$attendance->date?->format('l')}</td>
    <td class="{$statusClass}">{$attendance->status}</td>
    <td>{$attendance->remarks}</td>
</tr>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Report - {$studentName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .student-info { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; color: #666; }
        .info-value { color: #333; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 15px; background: #e8f4f8; border-radius: 5px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .stat-label { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .status-half { color: #17a2b8; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Student Attendance Report</h2>
        <p style="margin-top: 5px; color: #666;">Period: {$dateRange}</p>
    </div>

    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Student Name:</span>
            <span class="info-value">{$studentName}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admission No:</span>
            <span class="info-value">{$student->admission_number}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Class:</span>
            <span class="info-value">{$className} - {$sectionName}</span>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{$statistics['total_days']}</div>
            <div class="stat-label">Total Days</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #28a745;">{$statistics['present']}</div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #dc3545;">{$statistics['absent']}</div>
            <div class="stat-label">Absent</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #ffc107;">{$statistics['late']}</div>
            <div class="stat-label">Late</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{$statistics['attendance_rate']}%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build class summary HTML.
     *
     * @param Collection $data
     * @param int $classId
     * @param int|null $sectionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return string
     */
    protected function buildClassSummaryHtml(Collection $data, int $classId, ?int $sectionId, ?string $startDate, ?string $endDate): string
    {
        $schoolName = config('app.name', 'Smart School');
        $generatedAt = now()->format('F j, Y');
        $dateRange = $startDate && $endDate ? "{$startDate} to {$endDate}" : 'All Time';

        $rows = '';
        $sn = 1;
        foreach ($data as $item) {
            $student = $item['student'];
            $studentName = $student->user 
                ? "{$student->user->first_name} {$student->user->last_name}" 
                : '';
            $percentageClass = $item['percentage'] >= 75 ? 'status-present' : ($item['percentage'] >= 50 ? 'status-late' : 'status-absent');
            
            $rows .= <<<HTML
<tr>
    <td class="text-center">{$sn}</td>
    <td>{$student->admission_number}</td>
    <td>{$studentName}</td>
    <td>{$student->section?->name}</td>
    <td class="text-center">{$item['total']}</td>
    <td class="text-center status-present">{$item['present']}</td>
    <td class="text-center status-absent">{$item['absent']}</td>
    <td class="text-center status-late">{$item['late']}</td>
    <td class="text-center {$percentageClass}">{$item['percentage']}%</td>
</tr>
HTML;
            $sn++;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Class Attendance Summary</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; }
        td { padding: 8px 5px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>Class Attendance Summary</h2>
        <p style="margin-top: 5px; color: #666;">Period: {$dateRange}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>Section</th>
                <th class="text-center">Total</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Late</th>
                <th class="text-center">%</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by {$schoolName} Management System on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }
}
