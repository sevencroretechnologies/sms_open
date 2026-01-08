<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\FeesTransaction;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $currentSession = AcademicSession::getCurrentSession();
        $sessionId = $request->get('session_id', $currentSession?->id);

        $statistics = $this->getStatistics($sessionId);
        $chartData = $this->getChartData($sessionId);
        $recentActivities = $this->getRecentActivities();
        $academicSessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();

        return view('admin.dashboard', compact(
            'statistics',
            'chartData',
            'recentActivities',
            'academicSessions',
            'currentSession'
        ));
    }

    /**
     * Get dashboard statistics.
     *
     * @param  int|null  $sessionId
     * @return array
     */
    protected function getStatistics(?int $sessionId): array
    {
        $totalStudents = Student::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })->active()->count();

        $totalTeachers = User::role('teacher')->where('is_active', true)->count();

        $totalClasses = SchoolClass::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })->active()->count();

        $totalSections = Section::active()->count();

        $totalSubjects = Subject::active()->count();

        $totalStaff = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['teacher', 'accountant', 'librarian']);
        })->where('is_active', true)->count();

        $todayAttendance = $this->getTodayAttendancePercentage($sessionId);

        $todayCollection = $this->getTodayFeeCollection();

        $pendingFees = $this->getPendingFees($sessionId);

        $upcomingExams = $this->getUpcomingExamsCount($sessionId);

        return [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalSections' => $totalSections,
            'totalSubjects' => $totalSubjects,
            'totalStaff' => $totalStaff,
            'todayAttendance' => $todayAttendance,
            'todayCollection' => $todayCollection,
            'pendingFees' => $pendingFees,
            'upcomingExams' => $upcomingExams,
        ];
    }

    /**
     * Get today's attendance percentage.
     *
     * @param  int|null  $sessionId
     * @return string
     */
    protected function getTodayAttendancePercentage(?int $sessionId): string
    {
        $today = Carbon::today();

        $totalStudents = Student::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })->active()->count();

        if ($totalStudents === 0) {
            return '0%';
        }

        $presentCount = Attendance::whereDate('attendance_date', $today)
            ->whereHas('attendanceType', function ($query) {
                $query->where('name', 'present');
            })
            ->count();

        $percentage = round(($presentCount / $totalStudents) * 100);

        return $percentage . '%';
    }

    /**
     * Get today's fee collection amount.
     *
     * @return string
     */
    protected function getTodayFeeCollection(): string
    {
        $today = Carbon::today();

        $collection = FeesTransaction::whereDate('payment_date', $today)
            ->where('payment_status', 'completed')
            ->sum('amount_paid');

        return '₹' . number_format($collection);
    }

    /**
     * Get pending fees amount.
     *
     * @param  int|null  $sessionId
     * @return string
     */
    protected function getPendingFees(?int $sessionId): string
    {
        $pending = FeesTransaction::where('payment_status', 'pending')
            ->when($sessionId, function ($query) use ($sessionId) {
                return $query->whereHas('feesAllotment', function ($q) use ($sessionId) {
                    $q->whereHas('student', function ($sq) use ($sessionId) {
                        $sq->where('academic_session_id', $sessionId);
                    });
                });
            })
            ->sum('amount_paid');

        return '₹' . number_format($pending);
    }

    /**
     * Get upcoming exams count.
     *
     * @param  int|null  $sessionId
     * @return int
     */
    protected function getUpcomingExamsCount(?int $sessionId): int
    {
        $today = Carbon::today();
        $endOfMonth = Carbon::now()->endOfMonth();

        return ExamSchedule::whereBetween('exam_date', [$today, $endOfMonth])
            ->when($sessionId, function ($query) use ($sessionId) {
                return $query->whereHas('exam', function ($q) use ($sessionId) {
                    $q->where('academic_session_id', $sessionId);
                });
            })
            ->count();
    }

    /**
     * Get chart data for visualizations.
     *
     * @param  int|null  $sessionId
     * @return array
     */
    protected function getChartData(?int $sessionId): array
    {
        return [
            'enrollment' => $this->getEnrollmentByClass($sessionId),
            'attendance' => $this->getAttendanceTrend(),
            'feeCollection' => $this->getFeeCollectionTrend(),
            'examPerformance' => $this->getExamPerformance($sessionId),
        ];
    }

    /**
     * Get student enrollment by class.
     *
     * @param  int|null  $sessionId
     * @return array
     */
    protected function getEnrollmentByClass(?int $sessionId): array
    {
        $classes = SchoolClass::when($sessionId, function ($query) use ($sessionId) {
            return $query->where('academic_session_id', $sessionId);
        })
            ->active()
            ->ordered()
            ->withCount(['students' => function ($query) {
                $query->active();
            }])
            ->get();

        return [
            'labels' => $classes->pluck('display_name')->toArray(),
            'data' => $classes->pluck('students_count')->toArray(),
        ];
    }

    /**
     * Get attendance trend for the last 7 days.
     *
     * @return array
     */
    protected function getAttendanceTrend(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');

            $totalStudents = Student::active()->count();
            if ($totalStudents === 0) {
                $data[] = 0;
                continue;
            }

            $presentCount = Attendance::whereDate('attendance_date', $date)
                ->whereHas('attendanceType', function ($query) {
                    $query->where('name', 'present');
                })
                ->count();

            $data[] = round(($presentCount / $totalStudents) * 100);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get fee collection trend for the last 6 months.
     *
     * @return array
     */
    protected function getFeeCollectionTrend(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            $collection = FeesTransaction::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->where('payment_status', 'completed')
                ->sum('amount_paid');

            $data[] = $collection;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get exam performance distribution.
     *
     * @param  int|null  $sessionId
     * @return array
     */
    protected function getExamPerformance(?int $sessionId): array
    {
        $grades = ['A', 'B', 'C', 'D', 'F'];
        $data = [];

        foreach ($grades as $grade) {
            $count = DB::table('exam_marks')
                ->join('exam_grades', 'exam_marks.grade_id', '=', 'exam_grades.id')
                ->where('exam_grades.name', $grade)
                ->when($sessionId, function ($query) use ($sessionId) {
                    return $query->join('exam_schedules', 'exam_marks.exam_schedule_id', '=', 'exam_schedules.id')
                        ->join('exams', 'exam_schedules.exam_id', '=', 'exams.id')
                        ->where('exams.academic_session_id', $sessionId);
                })
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $grades,
            'data' => $data,
        ];
    }

    /**
     * Get recent activities for the dashboard.
     *
     * @return array
     */
    protected function getRecentActivities(): array
    {
        $activities = [];

        $recentStudents = Student::with(['user', 'schoolClass', 'section'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentStudents as $student) {
            $activities[] = [
                'type' => 'student_admission',
                'icon' => 'bi-person-plus',
                'color' => 'primary',
                'message' => "New student <strong>{$student->user->full_name}</strong> admitted to {$student->schoolClass->display_name}-{$student->section->name}",
                'time' => $student->created_at->diffForHumans(),
            ];
        }

        $recentTransactions = FeesTransaction::with(['student.user'])
            ->where('payment_status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->take(3)
            ->get();

        foreach ($recentTransactions as $transaction) {
            $activities[] = [
                'type' => 'fee_payment',
                'icon' => 'bi-currency-rupee',
                'color' => 'success',
                'message' => "Fee payment of <strong>₹" . number_format($transaction->amount_paid) . "</strong> received from {$transaction->student->user->full_name}",
                'time' => $transaction->payment_date->diffForHumans(),
            ];
        }

        usort($activities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 5);
    }

    /**
     * Refresh dashboard data via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $sessionId = $request->get('session_id');

        $statistics = $this->getStatistics($sessionId);
        $chartData = $this->getChartData($sessionId);

        return response()->json([
            'statistics' => $statistics,
            'chartData' => $chartData,
        ]);
    }
}
