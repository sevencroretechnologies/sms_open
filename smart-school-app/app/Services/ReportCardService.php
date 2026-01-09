<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Report Card Service
 * 
 * Prompt 426: Create Student Report Card Service
 * 
 * Generates student report cards with exam results, grades, and rankings.
 * Supports individual and bulk report card generation.
 * 
 * Features:
 * - Generate individual student report card
 * - Generate bulk report cards for class/section
 * - Include subject-wise marks and grades
 * - Calculate overall percentage and rank
 * - Support multiple exam types
 */
class ReportCardService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate report card for a single student.
     *
     * @param int $studentId
     * @param int $examId
     * @return Response
     */
    public function generateReportCard(int $studentId, int $examId): Response
    {
        $student = Student::with(['user', 'schoolClass', 'section', 'academicSession'])->findOrFail($studentId);
        $exam = Exam::findOrFail($examId);
        
        $reportData = $this->getStudentReportData($student, $exam);
        $html = $this->buildReportCardHtml($student, $exam, $reportData);
        
        $filename = "report_card_{$student->admission_number}_{$exam->id}";
        
        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate bulk report cards for a class/section.
     *
     * @param int $examId
     * @param int $classId
     * @param int|null $sectionId
     * @return Response
     */
    public function generateBulkReportCards(int $examId, int $classId, ?int $sectionId = null): Response
    {
        $exam = Exam::findOrFail($examId);
        
        $query = Student::with(['user', 'schoolClass', 'section', 'academicSession'])
            ->where('class_id', $classId)
            ->where('is_active', true);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        $students = $query->orderBy('admission_number')->get();
        
        $html = '';
        foreach ($students as $index => $student) {
            $reportData = $this->getStudentReportData($student, $exam);
            $html .= $this->buildReportCardHtml($student, $exam, $reportData);
            
            if ($index < $students->count() - 1) {
                $html .= '<div style="page-break-after: always;"></div>';
            }
        }
        
        $filename = "bulk_report_cards_{$exam->id}_class_{$classId}";
        
        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Get student report data for an exam.
     *
     * @param Student $student
     * @param Exam $exam
     * @return array
     */
    protected function getStudentReportData(Student $student, Exam $exam): array
    {
        $marks = ExamMark::whereHas('examSchedule', function ($q) use ($exam, $student) {
            $q->where('exam_id', $exam->id)
              ->where('class_id', $student->class_id)
              ->where('section_id', $student->section_id);
        })
        ->where('student_id', $student->id)
        ->with(['examSchedule.subject'])
        ->get();

        $subjects = [];
        $totalObtained = 0;
        $totalFull = 0;
        $passedSubjects = 0;

        foreach ($marks as $mark) {
            $schedule = $mark->examSchedule;
            $fullMarks = $schedule->full_marks ?? 0;
            $passingMarks = $schedule->passing_marks ?? 0;
            $obtainedMarks = $mark->obtained_marks;
            $percentage = $fullMarks > 0 ? round(($obtainedMarks / $fullMarks) * 100, 2) : 0;
            $passed = $obtainedMarks >= $passingMarks;
            $grade = $this->calculateGrade($percentage);

            $subjects[] = [
                'name' => $schedule->subject?->name ?? 'Unknown',
                'full_marks' => $fullMarks,
                'passing_marks' => $passingMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'passed' => $passed,
                'remarks' => $mark->remarks ?? '',
            ];

            $totalObtained += $obtainedMarks;
            $totalFull += $fullMarks;
            
            if ($passed) {
                $passedSubjects++;
            }
        }

        $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $overallGrade = $this->calculateGrade($overallPercentage);
        $overallResult = $passedSubjects === count($subjects) && count($subjects) > 0 ? 'Pass' : 'Fail';

        $rank = $this->calculateRank($student, $exam, $overallPercentage);

        return [
            'subjects' => $subjects,
            'total_obtained' => $totalObtained,
            'total_full' => $totalFull,
            'overall_percentage' => $overallPercentage,
            'overall_grade' => $overallGrade,
            'overall_result' => $overallResult,
            'passed_subjects' => $passedSubjects,
            'failed_subjects' => count($subjects) - $passedSubjects,
            'rank' => $rank,
        ];
    }

    /**
     * Calculate grade based on percentage.
     *
     * @param float $percentage
     * @return string
     */
    protected function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F',
        };
    }

    /**
     * Calculate student rank in class.
     *
     * @param Student $student
     * @param Exam $exam
     * @param float $studentPercentage
     * @return int
     */
    protected function calculateRank(Student $student, Exam $exam, float $studentPercentage): int
    {
        $classStudents = Student::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('is_active', true)
            ->pluck('id');

        $studentPercentages = [];
        
        foreach ($classStudents as $studentId) {
            $marks = ExamMark::whereHas('examSchedule', function ($q) use ($exam, $student) {
                $q->where('exam_id', $exam->id)
                  ->where('class_id', $student->class_id)
                  ->where('section_id', $student->section_id);
            })
            ->where('student_id', $studentId)
            ->with('examSchedule')
            ->get();

            $totalObtained = $marks->sum('obtained_marks');
            $totalFull = $marks->sum(fn($m) => $m->examSchedule?->full_marks ?? 0);
            $percentage = $totalFull > 0 ? ($totalObtained / $totalFull) * 100 : 0;
            
            $studentPercentages[$studentId] = $percentage;
        }

        arsort($studentPercentages);
        $rank = 1;
        
        foreach ($studentPercentages as $id => $percentage) {
            if ($id == $student->id) {
                return $rank;
            }
            $rank++;
        }

        return $rank;
    }

    /**
     * Build report card HTML.
     *
     * @param Student $student
     * @param Exam $exam
     * @param array $reportData
     * @return string
     */
    protected function buildReportCardHtml(Student $student, Exam $exam, array $reportData): string
    {
        $schoolName = config('app.name', 'Smart School');
        $studentName = $student->user ? "{$student->user->first_name} {$student->user->last_name}" : '';
        $className = $student->schoolClass?->name ?? '';
        $sectionName = $student->section?->name ?? '';
        $sessionName = $student->academicSession?->name ?? '';
        $examName = $exam->name;
        $generatedAt = now()->format('F j, Y');

        $subjectRows = '';
        foreach ($reportData['subjects'] as $subject) {
            $resultClass = $subject['passed'] ? 'success' : 'danger';
            $subjectResult = $subject['passed'] ? 'Pass' : 'Fail';
            $subjectRows .= <<<HTML
<tr>
    <td>{$subject['name']}</td>
    <td class="text-center">{$subject['full_marks']}</td>
    <td class="text-center">{$subject['passing_marks']}</td>
    <td class="text-center">{$subject['obtained_marks']}</td>
    <td class="text-center">{$subject['percentage']}%</td>
    <td class="text-center">{$subject['grade']}</td>
    <td class="text-center {$resultClass}">{$subjectResult}</td>
</tr>
HTML;
        }

        $resultClass = $reportData['overall_result'] === 'Pass' ? 'success' : 'danger';
        $resultBoxClass = $reportData['overall_result'] === 'Pass' ? 'result-pass' : 'result-fail';
        $totalSubjects = $reportData['passed_subjects'] + $reportData['failed_subjects'];

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Card - {$studentName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; margin-bottom: 5px; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .student-info { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .info-group { width: 48%; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; color: #666; }
        .info-value { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; font-size: 10px; }
        td { padding: 8px 5px; border: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-center { text-align: center; }
        .success { color: #28a745; font-weight: bold; }
        .danger { color: #dc3545; font-weight: bold; }
        .summary { margin-top: 20px; padding: 15px; background: #e8f4f8; border-radius: 5px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { font-weight: bold; color: #666; }
        .summary-value { font-weight: bold; color: #333; }
        .result-box { text-align: center; margin: 20px 0; padding: 15px; border: 2px solid; border-radius: 5px; }
        .result-pass { border-color: #28a745; background: #d4edda; }
        .result-fail { border-color: #dc3545; background: #f8d7da; }
        .result-text { font-size: 18px; font-weight: bold; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 30%; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 9px; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>REPORT CARD</h2>
        <p style="margin-top: 5px; color: #666;">{$examName} - {$sessionName}</p>
    </div>

    <div class="student-info">
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value">{$studentName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No:</span>
                <span class="info-value">{$student->admission_number}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Roll No:</span>
                <span class="info-value">{$student->roll_number}</span>
            </div>
        </div>
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span class="info-value">{$className} - {$sectionName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Session:</span>
                <span class="info-value">{$sessionName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{$generatedAt}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th class="text-center">Full Marks</th>
                <th class="text-center">Pass Marks</th>
                <th class="text-center">Obtained</th>
                <th class="text-center">Percentage</th>
                <th class="text-center">Grade</th>
                <th class="text-center">Result</th>
            </tr>
        </thead>
        <tbody>
            {$subjectRows}
            <tr style="font-weight: bold; background: #e9ecef;">
                <td>TOTAL</td>
                <td class="text-center">{$reportData['total_full']}</td>
                <td class="text-center">-</td>
                <td class="text-center">{$reportData['total_obtained']}</td>
                <td class="text-center">{$reportData['overall_percentage']}%</td>
                <td class="text-center">{$reportData['overall_grade']}</td>
                <td class="text-center {$resultClass}">{$reportData['overall_result']}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Total Marks Obtained:</span>
            <span class="summary-value">{$reportData['total_obtained']} / {$reportData['total_full']}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Overall Percentage:</span>
            <span class="summary-value">{$reportData['overall_percentage']}%</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Overall Grade:</span>
            <span class="summary-value">{$reportData['overall_grade']}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Class Rank:</span>
            <span class="summary-value">{$reportData['rank']}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Subjects Passed:</span>
            <span class="summary-value">{$reportData['passed_subjects']} / {$totalSubjects}</span>
        </div>
    </div>

    <div class="result-box {$resultBoxClass}">
        <span class="result-text {$resultClass}">RESULT: {$reportData['overall_result']}</span>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Class Teacher</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Exam Controller</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Principal</div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated report card from {$schoolName} Management System</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get report card data without generating PDF.
     *
     * @param int $studentId
     * @param int $examId
     * @return array
     */
    public function getReportCardData(int $studentId, int $examId): array
    {
        $student = Student::with(['user', 'schoolClass', 'section', 'academicSession'])->findOrFail($studentId);
        $exam = Exam::findOrFail($examId);
        
        return [
            'student' => $student,
            'exam' => $exam,
            'report' => $this->getStudentReportData($student, $exam),
        ];
    }
}
