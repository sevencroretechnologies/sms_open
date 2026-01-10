<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Expense;
use App\Models\FeesAllotment;
use App\Models\FeesTransaction;
use App\Models\FeesType;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::getCurrentSession();
        
        $statistics = $this->getStatistics();
        $todayCollection = $this->getTodayCollection();
        $recentTransactions = $this->getRecentTransactions();
        $overdueFees = $this->getOverdueFees();
        $pendingFeesByClass = $this->getPendingFeesByClass();
        $chartData = $this->getChartData();

        return view('accountant.dashboard', compact(
            'currentSession',
            'statistics',
            'todayCollection',
            'recentTransactions',
            'overdueFees',
            'pendingFeesByClass',
            'chartData'
        ));
    }

    protected function getStatistics(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth();

        $monthlyCollection = FeesTransaction::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('amount', '>', 0)
            ->sum('amount');

        $lastMonthCollection = FeesTransaction::whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->where('amount', '>', 0)
            ->sum('amount');

        $totalPending = FeesAllotment::where('balance', '>', 0)->sum('balance');

        $monthlyExpenses = 0;
        if (class_exists(Expense::class)) {
            $monthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
                ->whereYear('expense_date', $currentYear)
                ->sum('amount');
        }

        $netBalance = $monthlyCollection - $monthlyExpenses;

        $collectionChange = $lastMonthCollection > 0 
            ? round((($monthlyCollection - $lastMonthCollection) / $lastMonthCollection) * 100, 1) 
            : 0;

        return [
            'total_collection' => $monthlyCollection,
            'pending_fees' => $totalPending,
            'total_expenses' => $monthlyExpenses,
            'net_balance' => $netBalance,
            'collection_change' => $collectionChange,
        ];
    }

    protected function getTodayCollection(): array
    {
        $today = Carbon::today();

        $todayTransactions = FeesTransaction::whereDate('payment_date', $today)
            ->where('amount', '>', 0)
            ->get();

        $totalAmount = $todayTransactions->sum('amount');
        $transactionCount = $todayTransactions->count();

        return [
            'total_amount' => $totalAmount,
            'transaction_count' => $transactionCount,
        ];
    }

    protected function getRecentTransactions()
    {
        return FeesTransaction::with(['feesAllotment.student.user', 'feesAllotment.feesMaster.feesType'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($transaction) {
                $student = $transaction->feesAllotment->student ?? null;
                $feeType = $transaction->feesAllotment->feesMaster->feesType ?? null;
                
                return [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id ?? 'TXN' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
                    'student_name' => $student ? ($student->user->name ?? $student->first_name . ' ' . $student->last_name) : 'N/A',
                    'fee_type' => $feeType->name ?? 'N/A',
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method ?? 'Cash',
                    'payment_date' => $transaction->payment_date,
                    'status' => $transaction->amount > 0 ? 'completed' : 'refund',
                ];
            });
    }

    protected function getOverdueFees()
    {
        $today = Carbon::today();

        return FeesAllotment::with(['student.user', 'student.schoolClass', 'student.section'])
            ->where('balance', '>', 0)
            ->where('due_date', '<', $today)
            ->orderBy('due_date')
            ->take(10)
            ->get()
            ->map(function ($allotment) use ($today) {
                $student = $allotment->student;
                $daysOverdue = $allotment->due_date ? Carbon::parse($allotment->due_date)->diffInDays($today) : 0;
                
                return [
                    'id' => $allotment->id,
                    'student_id' => $student->id ?? null,
                    'student_name' => $student ? ($student->user->name ?? $student->first_name . ' ' . $student->last_name) : 'N/A',
                    'class_name' => $student->schoolClass->name ?? 'N/A',
                    'section_name' => $student->section->name ?? '',
                    'amount' => $allotment->balance,
                    'due_date' => $allotment->due_date,
                    'days_overdue' => $daysOverdue,
                    'urgency' => $daysOverdue > 30 ? 'danger' : ($daysOverdue > 15 ? 'warning' : 'info'),
                ];
            });
    }

    protected function getPendingFeesByClass()
    {
        return SchoolClass::withCount(['students'])
            ->get()
            ->map(function ($class) {
                $studentIds = Student::where('class_id', $class->id)->pluck('id');
                $pendingAmount = FeesAllotment::whereIn('student_id', $studentIds)
                    ->where('balance', '>', 0)
                    ->sum('balance');
                $pendingCount = FeesAllotment::whereIn('student_id', $studentIds)
                    ->where('balance', '>', 0)
                    ->count();

                return [
                    'class_id' => $class->id,
                    'class_name' => $class->name,
                    'student_count' => $class->students_count,
                    'pending_amount' => $pendingAmount,
                    'pending_count' => $pendingCount,
                ];
            })
            ->filter(function ($item) {
                return $item['pending_amount'] > 0;
            })
            ->sortByDesc('pending_amount')
            ->take(10)
            ->values();
    }

    protected function getChartData(): array
    {
        return [
            'collectionTrend' => $this->getCollectionTrendChart(),
            'feeTypeDistribution' => $this->getFeeTypeDistributionChart(),
        ];
    }

    protected function getCollectionTrendChart(): array
    {
        $labels = [];
        $collectionData = [];
        $targetData = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');
            
            $monthCollection = FeesTransaction::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->where('amount', '>', 0)
                ->sum('amount');
            
            $collectionData[] = $monthCollection;
            
            $avgCollection = FeesTransaction::where('amount', '>', 0)->avg('amount') ?? 0;
            $targetData[] = $avgCollection * 100;
        }

        return [
            'labels' => $labels,
            'collection' => $collectionData,
            'target' => $targetData,
        ];
    }

    protected function getFeeTypeDistributionChart(): array
    {
        $feeTypes = FeesType::all();
        $labels = [];
        $data = [];
        $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'];

        foreach ($feeTypes as $index => $feeType) {
            $totalCollected = FeesTransaction::whereHas('feesAllotment.feesMaster', function ($query) use ($feeType) {
                $query->where('fee_type_id', $feeType->id);
            })->where('amount', '>', 0)->sum('amount');

            if ($totalCollected > 0) {
                $labels[] = $feeType->name;
                $data[] = $totalCollected;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }
}
