<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\FeesAllotment;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\ParentModel;
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
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->route('home')->with('error', 'Parent profile not found.');
        }

        $currentSession = AcademicSession::getCurrentSession();
        
        $children = $this->getChildren($parent);
        $childrenData = $this->getChildrenData($children);
        $feeSummary = $this->getFeeSummary($children);
        $notices = $this->getNotices();
        $chartData = $this->getChartData($children);

        return view('parent.dashboard', compact(
            'parent',
            'currentSession',
            'children',
            'childrenData',
            'feeSummary',
            'notices',
            'chartData'
        ));
    }

    protected function getChildren(ParentModel $parent)
    {
        return Student::where('parent_id', $parent->id)
            ->with(['schoolClass', 'section', 'user'])
            ->get();
    }

    protected function getChildrenData($children): array
    {
        $childrenData = [];
        
        foreach ($children as $child) {
            $childrenData[$child->id] = [
                'profile' => $this->getChildProfile($child),
                'attendance' => $this->getChildAttendance($child),
                'recentResults' => $this->getChildRecentResults($child),
                'pendingFees' => $this->getChildPendingFees($child),
                'pendingHomework' => $this->getChildPendingHomework($child),
            ];
        }
        
        return $childrenData;
    }

    protected function getChildProfile(Student $child): array
    {
        return [
            'id' => $child->id,
            'name' => $child->user->name ?? $child->first_name . ' ' . $child->last_name,
            'photo' => $child->photo ?? null,
            'class_name' => $child->schoolClass->name ?? 'N/A',
            'section_name' => $child->section->name ?? 'N/A',
            'roll_number' => $child->roll_number ?? 'N/A',
            'admission_number' => $child->admission_number ?? 'N/A',
        ];
    }

    protected function getChildAttendance(Student $child): array
    {
        $currentSession = AcademicSession::getCurrentSession();
        $startDate = $currentSession ? $currentSession->start_date : Carbon::now()->startOfYear();
        $endDate = Carbon::now();

        $attendanceRecords = Attendance::where('student_id', $child->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();

        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        $thisMonthRecords = Attendance::where('student_id', $child->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->get();

        $monthTotal = $thisMonthRecords->count();
        $monthPresent = $thisMonthRecords->where('status', 'present')->count();
        $monthAbsent = $thisMonthRecords->where('status', 'absent')->count();
        $monthLate = $thisMonthRecords->where('status', 'late')->count();
        $monthPercentage = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 1) : 0;

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'percentage' => $percentage,
            'status' => $percentage >= 75 ? 'Good' : ($percentage >= 50 ? 'Average' : 'Poor'),
            'this_month' => [
                'total' => $monthTotal,
                'present' => $monthPresent,
                'absent' => $monthAbsent,
                'late' => $monthLate,
                'percentage' => $monthPercentage,
            ],
        ];
    }

    protected function getChildRecentResults(Student $child)
    {
        return ExamMark::where('student_id', $child->id)
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

    protected function getChildPendingFees(Student $child): array
    {
        $allotments = FeesAllotment::where('student_id', $child->id)
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

    protected function getChildPendingHomework(Student $child)
    {
        return Homework::where('class_id', $child->class_id)
            ->where('section_id', $child->section_id)
            ->where('due_date', '>=', Carbon::now()->toDateString())
            ->whereDoesntHave('submissions', function ($query) use ($child) {
                $query->where('student_id', $child->id);
            })
            ->with(['subject', 'teacher.user'])
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function ($homework) {
                $dueDate = Carbon::parse($homework->due_date);
                $daysLeft = Carbon::now()->diffInDays($dueDate, false);
                return [
                    'id' => $homework->id,
                    'title' => $homework->title,
                    'subject_name' => $homework->subject->name ?? 'N/A',
                    'due_date' => $homework->due_date,
                    'days_left' => $daysLeft,
                    'urgency' => $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info'),
                ];
            });
    }

    protected function getFeeSummary($children): array
    {
        $totalPending = 0;
        $totalPaid = 0;
        $childrenFees = [];

        foreach ($children as $child) {
            $allotments = FeesAllotment::where('student_id', $child->id)->get();
            $childTotal = $allotments->sum('total_amount');
            $childPaid = $allotments->sum('paid_amount');
            $childPending = $childTotal - $childPaid;

            $totalPending += $childPending;
            $totalPaid += $childPaid;

            $childrenFees[] = [
                'child_id' => $child->id,
                'child_name' => $child->user->name ?? $child->first_name . ' ' . $child->last_name,
                'total' => $childTotal,
                'paid' => $childPaid,
                'pending' => $childPending,
            ];
        }

        return [
            'total_pending' => $totalPending,
            'total_paid' => $totalPaid,
            'children_fees' => $childrenFees,
        ];
    }

    protected function getNotices()
    {
        return Notice::where('is_active', true)
            ->where(function ($query) {
                $query->where('audience', 'all')
                    ->orWhere('audience', 'parents');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    protected function getChartData($children): array
    {
        return [
            'attendanceOverview' => $this->getAttendanceOverviewChart($children),
        ];
    }

    protected function getAttendanceOverviewChart($children): array
    {
        $labels = [];
        $data = [];

        foreach ($children as $child) {
            $labels[] = $child->user->name ?? $child->first_name;
            
            $currentSession = AcademicSession::getCurrentSession();
            $startDate = $currentSession ? $currentSession->start_date : Carbon::now()->startOfYear();
            
            $records = Attendance::where('student_id', $child->id)
                ->whereBetween('date', [$startDate, Carbon::now()])
                ->get();
            
            $total = $records->count();
            $present = $records->where('status', 'present')->count();
            $data[] = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
