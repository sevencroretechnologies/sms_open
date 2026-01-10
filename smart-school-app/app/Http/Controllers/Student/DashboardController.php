<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ClassTimetable;
use App\Models\ExamMark;
use App\Models\ExamSchedule;
use App\Models\FeesAllotment;
use App\Models\Homework;
use App\Models\LibraryIssue;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }

        $currentSession = AcademicSession::getCurrentSession();

        $profileData = $this->getProfileData($student);
        $attendanceSummary = $this->getAttendanceSummary($student);
        $upcomingExams = $this->getUpcomingExams($student);
        $recentResults = $this->getRecentResults($student);
        $feeStatus = $this->getFeeStatus($student);
        $pendingHomework = $this->getPendingHomework($student);
        $todaySchedule = $this->getTodaySchedule($student);
        $chartData = $this->getChartData($student);

        return view('student.dashboard', compact(
            'student',
            'currentSession',
            'profileData',
            'attendanceSummary',
            'upcomingExams',
            'recentResults',
            'feeStatus',
            'pendingHomework',
            'todaySchedule',
            'chartData'
        ));
    }

    protected function getProfileData(Student $student): array
    {
        return [
            'name' => $student->user->name ?? $student->first_name . ' ' . $student->last_name,
            'photo' => $student->photo ?? null,
            'class_name' => $student->schoolClass->name ?? 'N/A',
            'section_name' => $student->section->name ?? 'N/A',
            'roll_number' => $student->roll_number ?? 'N/A',
            'admission_number' => $student->admission_number ?? 'N/A',
            'email' => $student->user->email ?? $student->email ?? 'N/A',
            'phone' => $student->phone ?? 'N/A',
        ];
    }

    protected function getAttendanceSummary(Student $student): array
    {
        $currentSession = AcademicSession::getCurrentSession();
        $startDate = $currentSession ? $currentSession->start_date : Carbon::now()->startOfYear();
        $endDate = Carbon::now();

        $attendanceRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();

        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        $monthlyAttendance = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthRecords = $attendanceRecords->filter(function ($record) use ($month) {
                return Carbon::parse($record->date)->format('Y-m') === $month->format('Y-m');
            });
            $monthTotal = $monthRecords->count();
            $monthPresent = $monthRecords->where('status', 'present')->count();
            $monthlyAttendance[] = [
                'month' => $month->format('M'),
                'percentage' => $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 1) : 0,
            ];
        }

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'percentage' => $percentage,
            'status' => $percentage >= 75 ? 'Good' : ($percentage >= 50 ? 'Average' : 'Poor'),
            'monthly' => $monthlyAttendance,
        ];
    }

    protected function getUpcomingExams(Student $student)
    {
        return ExamSchedule::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('exam_date', '>=', Carbon::now()->toDateString())
            ->with(['exam', 'subject', 'schoolClass', 'section'])
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();
    }

    protected function getRecentResults(Student $student)
    {
        return ExamMark::where('student_id', $student->id)
            ->with(['examSchedule.exam', 'examSchedule.subject'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($mark) {
                $schedule = $mark->examSchedule;
                $percentage = $schedule && $schedule->full_marks > 0 
                    ? round(($mark->obtained_marks / $schedule->full_marks) * 100, 1) 
                    : 0;
                return [
                    'exam_name' => $schedule->exam->name ?? 'N/A',
                    'subject_name' => $schedule->subject->name ?? 'N/A',
                    'obtained_marks' => $mark->obtained_marks,
                    'full_marks' => $schedule->full_marks ?? 0,
                    'percentage' => $percentage,
                    'grade' => $mark->grade ?? $this->calculateGrade($percentage),
                    'date' => $schedule->exam_date ?? null,
                ];
            });
    }

    protected function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    protected function getFeeStatus(Student $student): array
    {
        $allotments = FeesAllotment::where('student_id', $student->id)
            ->with(['feesMaster.feesType', 'feesMaster.feesGroup'])
            ->get();

        $totalAmount = $allotments->sum('total_amount');
        $paidAmount = $allotments->sum('paid_amount');
        $pendingAmount = $totalAmount - $paidAmount;

        $pendingFees = $allotments->filter(function ($allotment) {
            return $allotment->balance > 0;
        })->map(function ($allotment) {
            return [
                'fee_type' => $allotment->feesMaster->feesType->name ?? 'N/A',
                'amount' => $allotment->balance,
                'due_date' => $allotment->due_date,
                'is_overdue' => $allotment->due_date && Carbon::parse($allotment->due_date)->isPast(),
            ];
        })->take(5);

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
            'pending_count' => $pendingFees->count(),
            'pending_fees' => $pendingFees,
        ];
    }

    protected function getPendingHomework(Student $student)
    {
        return Homework::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('submission_date', '>=', Carbon::now()->toDateString())
            ->with(['subject'])
            ->orderBy('submission_date')
            ->take(5)
            ->get()
            ->map(function ($homework) {
                $dueDate = Carbon::parse($homework->submission_date);
                $daysLeft = Carbon::now()->diffInDays($dueDate, false);
                return [
                    'id' => $homework->id,
                    'title' => $homework->title,
                    'subject_name' => $homework->subject->name ?? 'N/A',
                    'due_date' => $homework->submission_date,
                    'days_left' => $daysLeft,
                    'urgency' => $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info'),
                ];
            });
    }

    protected function getTodaySchedule(Student $student)
    {
        $dayOfWeek = strtolower(Carbon::now()->format('l'));

        return ClassTimetable::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('day_of_week', $dayOfWeek)
            ->with(['subject', 'teacher.user'])
            ->orderBy('start_time')
            ->get();
    }

    protected function getChartData(Student $student): array
    {
        return [
            'attendanceMonthly' => $this->getMonthlyAttendanceChart($student),
            'subjectPerformance' => $this->getSubjectPerformanceChart($student),
        ];
    }

    protected function getMonthlyAttendanceChart(Student $student): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');

            $monthRecords = Attendance::where('student_id', $student->id)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->get();

            $total = $monthRecords->count();
            $present = $monthRecords->where('status', 'present')->count();
            $data[] = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function getSubjectPerformanceChart(Student $student): array
    {
        $subjectMarks = ExamMark::where('student_id', $student->id)
            ->with(['examSchedule.subject'])
            ->get()
            ->groupBy(function ($mark) {
                return $mark->examSchedule->subject->name ?? 'Unknown';
            })
            ->map(function ($marks) {
                $totalObtained = $marks->sum('obtained_marks');
                $totalFull = $marks->sum(function ($mark) {
                    return $mark->examSchedule->full_marks ?? 0;
                });
                return $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
            });

        return [
            'labels' => $subjectMarks->keys()->toArray(),
            'data' => $subjectMarks->values()->toArray(),
        ];
    }
}
