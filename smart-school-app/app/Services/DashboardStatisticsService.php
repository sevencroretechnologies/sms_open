<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use App\Models\Attendance;
use App\Models\FeesTransaction;
use App\Models\LibraryIssue;
use App\Models\LibraryBook;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Dashboard Statistics Service
 * 
 * Prompt 432: Create Dashboard Statistics Service
 * 
 * Provides comprehensive dashboard statistics for the school management system.
 * Aggregates data from multiple modules for dashboard display.
 * 
 * Features:
 * - Overall school statistics
 * - Student enrollment statistics
 * - Attendance statistics
 * - Fee collection statistics
 * - Library statistics
 * - Exam performance statistics
 * - Recent activity feeds
 */
class DashboardStatisticsService
{
    /**
     * Get overall school statistics.
     *
     * @return array
     */
    public function getOverallStatistics(): array
    {
        return [
            'students' => $this->getStudentStatistics(),
            'staff' => $this->getStaffStatistics(),
            'attendance' => $this->getAttendanceStatistics(),
            'fees' => $this->getFeeStatistics(),
            'library' => $this->getLibraryStatistics(),
            'exams' => $this->getExamStatistics(),
        ];
    }

    /**
     * Get student statistics.
     *
     * @return array
     */
    public function getStudentStatistics(): array
    {
        $total = Student::count();
        $active = Student::where('is_active', true)->count();
        $inactive = Student::where('is_active', false)->count();
        $male = Student::where('gender', 'male')->count();
        $female = Student::where('gender', 'female')->count();
        
        $newThisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $byClass = Student::where('is_active', true)
            ->selectRaw('class_id, count(*) as count')
            ->groupBy('class_id')
            ->with('schoolClass:id,name')
            ->get()
            ->map(fn($item) => [
                'class' => $item->schoolClass?->name ?? 'Unknown',
                'count' => $item->count,
            ]);

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'male' => $male,
            'female' => $female,
            'new_this_month' => $newThisMonth,
            'by_class' => $byClass,
        ];
    }

    /**
     * Get staff statistics.
     *
     * @return array
     */
    public function getStaffStatistics(): array
    {
        $totalUsers = User::count();
        
        $byRole = User::selectRaw('
            (SELECT COUNT(*) FROM model_has_roles WHERE model_has_roles.model_id = users.id) as role_count
        ')->get();

        $teachers = User::whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        })->count();

        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->count();

        $accountants = User::whereHas('roles', function ($q) {
            $q->where('name', 'accountant');
        })->count();

        $librarians = User::whereHas('roles', function ($q) {
            $q->where('name', 'librarian');
        })->count();

        return [
            'total_users' => $totalUsers,
            'teachers' => $teachers,
            'admins' => $admins,
            'accountants' => $accountants,
            'librarians' => $librarians,
        ];
    }

    /**
     * Get attendance statistics.
     *
     * @return array
     */
    public function getAttendanceStatistics(): array
    {
        $today = now()->format('Y-m-d');
        
        $todayAttendance = Attendance::whereDate('date', $today)->get();
        $todayTotal = $todayAttendance->count();
        $todayPresent = $todayAttendance->where('status', 'present')->count();
        $todayAbsent = $todayAttendance->where('status', 'absent')->count();
        $todayLate = $todayAttendance->where('status', 'late')->count();

        $thisMonth = Attendance::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();
        $monthTotal = $thisMonth->count();
        $monthPresent = $thisMonth->where('status', 'present')->count();
        $monthRate = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 2) : 0;

        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayAttendance = Attendance::whereDate('date', $date)->get();
            $total = $dayAttendance->count();
            $present = $dayAttendance->where('status', 'present')->count();
            
            $weeklyTrend[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'total' => $total,
                'present' => $present,
                'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        }

        return [
            'today' => [
                'total' => $todayTotal,
                'present' => $todayPresent,
                'absent' => $todayAbsent,
                'late' => $todayLate,
                'rate' => $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 2) : 0,
            ],
            'this_month' => [
                'total' => $monthTotal,
                'present' => $monthPresent,
                'rate' => $monthRate,
            ],
            'weekly_trend' => $weeklyTrend,
        ];
    }

    /**
     * Get fee statistics.
     *
     * @return array
     */
    public function getFeeStatistics(): array
    {
        $today = now()->format('Y-m-d');
        
        $todayCollection = FeesTransaction::whereDate('payment_date', $today)
            ->where('payment_status', 'completed')
            ->sum('amount');

        $thisMonth = FeesTransaction::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('payment_status', 'completed')
            ->sum('amount');

        $thisYear = FeesTransaction::whereYear('payment_date', now()->year)
            ->where('payment_status', 'completed')
            ->sum('amount');

        $pendingTransactions = FeesTransaction::where('payment_status', 'pending')->count();
        $pendingAmount = FeesTransaction::where('payment_status', 'pending')->sum('amount');

        $recentTransactions = FeesTransaction::with(['student.user', 'feesType'])
            ->where('payment_status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'student' => $t->student?->user 
                    ? "{$t->student->user->first_name} {$t->student->user->last_name}" 
                    : '',
                'amount' => $t->amount,
                'fee_type' => $t->feesType?->name ?? '',
                'date' => $t->payment_date?->format('Y-m-d'),
            ]);

        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $collection = FeesTransaction::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->where('payment_status', 'completed')
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'amount' => $collection,
            ];
        }

        return [
            'today_collection' => $todayCollection,
            'this_month' => $thisMonth,
            'this_year' => $thisYear,
            'pending_transactions' => $pendingTransactions,
            'pending_amount' => $pendingAmount,
            'recent_transactions' => $recentTransactions,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Get library statistics.
     *
     * @return array
     */
    public function getLibraryStatistics(): array
    {
        $totalBooks = LibraryBook::count();
        $totalQuantity = LibraryBook::sum('quantity');
        $availableQuantity = LibraryBook::sum('available_quantity');
        $issuedBooks = $totalQuantity - $availableQuantity;

        $currentlyIssued = LibraryIssue::whereNull('return_date')->count();
        $overdueBooks = LibraryIssue::whereNull('return_date')
            ->where('due_date', '<', now())
            ->count();

        $todayIssued = LibraryIssue::whereDate('issue_date', now())->count();
        $todayReturned = LibraryIssue::whereDate('return_date', now())->count();

        $thisMonthIssued = LibraryIssue::whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->count();

        $recentIssues = LibraryIssue::with(['book', 'member'])
            ->orderBy('issue_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($i) => [
                'book' => $i->book?->title ?? '',
                'member' => $i->member?->name ?? '',
                'issue_date' => $i->issue_date?->format('Y-m-d'),
                'due_date' => $i->due_date?->format('Y-m-d'),
                'status' => $i->return_date ? 'Returned' : ($i->due_date && $i->due_date->isPast() ? 'Overdue' : 'Issued'),
            ]);

        return [
            'total_books' => $totalBooks,
            'total_quantity' => $totalQuantity,
            'available' => $availableQuantity,
            'issued' => $issuedBooks,
            'currently_issued' => $currentlyIssued,
            'overdue' => $overdueBooks,
            'today_issued' => $todayIssued,
            'today_returned' => $todayReturned,
            'this_month_issued' => $thisMonthIssued,
            'recent_issues' => $recentIssues,
        ];
    }

    /**
     * Get exam statistics.
     *
     * @return array
     */
    public function getExamStatistics(): array
    {
        $totalExams = Exam::count();
        $upcomingExams = Exam::where('start_date', '>', now())->count();
        $ongoingExams = Exam::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $completedExams = Exam::where('end_date', '<', now())->count();

        $recentExams = Exam::orderBy('start_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'name' => $e->name,
                'start_date' => $e->start_date?->format('Y-m-d'),
                'end_date' => $e->end_date?->format('Y-m-d'),
                'status' => $e->end_date && $e->end_date->isPast() ? 'Completed' 
                    : ($e->start_date && $e->start_date->isPast() ? 'Ongoing' : 'Upcoming'),
            ]);

        $totalMarksEntered = ExamMark::count();

        return [
            'total_exams' => $totalExams,
            'upcoming' => $upcomingExams,
            'ongoing' => $ongoingExams,
            'completed' => $completedExams,
            'recent_exams' => $recentExams,
            'total_marks_entered' => $totalMarksEntered,
        ];
    }

    /**
     * Get class-wise statistics.
     *
     * @return Collection
     */
    public function getClassWiseStatistics(): Collection
    {
        return SchoolClass::with(['sections'])->get()->map(function ($class) {
            $studentCount = Student::where('class_id', $class->id)
                ->where('is_active', true)
                ->count();

            $sectionCount = $class->sections->count();

            $todayAttendance = Attendance::where('class_id', $class->id)
                ->whereDate('date', now())
                ->get();
            $todayPresent = $todayAttendance->where('status', 'present')->count();
            $todayTotal = $todayAttendance->count();

            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'sections' => $sectionCount,
                'students' => $studentCount,
                'today_present' => $todayPresent,
                'today_total' => $todayTotal,
                'attendance_rate' => $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Get recent activities.
     *
     * @param int $limit
     * @return array
     */
    public function getRecentActivities(int $limit = 20): array
    {
        $activities = [];

        $recentStudents = Student::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'type' => 'student_admission',
                'message' => "New student admitted: " . ($s->user ? "{$s->user->first_name} {$s->user->last_name}" : $s->admission_number),
                'timestamp' => $s->created_at,
            ]);

        $recentFees = FeesTransaction::with(['student.user'])
            ->where('payment_status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'type' => 'fee_payment',
                'message' => "Fee payment received: ₹{$t->amount} from " . ($t->student?->user ? "{$t->student->user->first_name} {$t->student->user->last_name}" : ''),
                'timestamp' => $t->payment_date,
            ]);

        $recentLibrary = LibraryIssue::with(['book', 'member'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($i) => [
                'type' => 'library_issue',
                'message' => "Book " . ($i->return_date ? 'returned' : 'issued') . ": {$i->book?->title}",
                'timestamp' => $i->return_date ?? $i->issue_date,
            ]);

        $activities = collect()
            ->merge($recentStudents)
            ->merge($recentFees)
            ->merge($recentLibrary)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get dashboard summary for specific role.
     *
     * @param string $role
     * @return array
     */
    public function getDashboardForRole(string $role): array
    {
        return match ($role) {
            'admin' => $this->getAdminDashboard(),
            'teacher' => $this->getTeacherDashboard(),
            'accountant' => $this->getAccountantDashboard(),
            'librarian' => $this->getLibrarianDashboard(),
            'student' => $this->getStudentDashboard(),
            'parent' => $this->getParentDashboard(),
            default => $this->getOverallStatistics(),
        };
    }

    /**
     * Get admin dashboard data.
     *
     * @return array
     */
    protected function getAdminDashboard(): array
    {
        return [
            'overview' => $this->getOverallStatistics(),
            'class_wise' => $this->getClassWiseStatistics(),
            'recent_activities' => $this->getRecentActivities(),
        ];
    }

    /**
     * Get teacher dashboard data.
     *
     * @return array
     */
    protected function getTeacherDashboard(): array
    {
        return [
            'students' => $this->getStudentStatistics(),
            'attendance' => $this->getAttendanceStatistics(),
            'exams' => $this->getExamStatistics(),
        ];
    }

    /**
     * Get accountant dashboard data.
     *
     * @return array
     */
    protected function getAccountantDashboard(): array
    {
        return [
            'fees' => $this->getFeeStatistics(),
            'students' => [
                'total' => Student::where('is_active', true)->count(),
            ],
        ];
    }

    /**
     * Get librarian dashboard data.
     *
     * @return array
     */
    protected function getLibrarianDashboard(): array
    {
        return [
            'library' => $this->getLibraryStatistics(),
        ];
    }

    /**
     * Get student dashboard data.
     *
     * @return array
     */
    protected function getStudentDashboard(): array
    {
        return [
            'exams' => $this->getExamStatistics(),
            'library' => [
                'currently_issued' => LibraryIssue::whereNull('return_date')->count(),
                'overdue' => LibraryIssue::whereNull('return_date')->where('due_date', '<', now())->count(),
            ],
        ];
    }

    /**
     * Get parent dashboard data.
     *
     * @return array
     */
    protected function getParentDashboard(): array
    {
        return [
            'attendance' => $this->getAttendanceStatistics(),
            'fees' => [
                'pending' => FeesTransaction::where('payment_status', 'pending')->count(),
            ],
        ];
    }

    /**
     * Get quick stats for dashboard cards.
     *
     * @return array
     */
    public function getQuickStats(): array
    {
        return [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count(),
            'today_attendance_rate' => $this->getTodayAttendanceRate(),
            'today_collection' => FeesTransaction::whereDate('payment_date', now())
                ->where('payment_status', 'completed')
                ->sum('amount'),
            'overdue_books' => LibraryIssue::whereNull('return_date')
                ->where('due_date', '<', now())
                ->count(),
            'upcoming_exams' => Exam::where('start_date', '>', now())->count(),
        ];
    }

    /**
     * Get today's attendance rate.
     *
     * @return float
     */
    protected function getTodayAttendanceRate(): float
    {
        $today = Attendance::whereDate('date', now())->get();
        $total = $today->count();
        $present = $today->where('status', 'present')->count();
        
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }
}
