<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\FeesAllotment;
use App\Models\FeesTransaction;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * FeesReportController
 * 
 * Handles fee reports for accountants.
 */
class FeesReportController extends Controller
{
    /**
     * Display fee reports dashboard.
     */
    public function index()
    {
        $todayCollection = FeesTransaction::whereDate('payment_date', Carbon::today())->sum('amount');
        $monthCollection = FeesTransaction::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');
        $totalDue = FeesAllotment::sum('balance');
        $totalCollection = FeesTransaction::sum('amount');
        
        return view('accountant.fees-reports.index', compact('todayCollection', 'monthCollection', 'totalDue', 'totalCollection'));
    }

    /**
     * Display collection report.
     */
    public function collection(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        $transactions = FeesTransaction::with(['feesAllotment.student.user', 'feesAllotment.student.schoolClass', 'feesAllotment.feeGroup', 'collector'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);
        
        $totalAmount = FeesTransaction::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        
        return view('accountant.fees-reports.collection', compact('transactions', 'startDate', 'endDate', 'totalAmount'));
    }

    /**
     * Display due fees report.
     */
    public function due(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        
        $query = FeesAllotment::with(['student.user', 'student.schoolClass', 'student.section', 'feeGroup'])
            ->where('balance', '>', 0);
        
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        
        $dueList = $query->orderBy('due_date')->paginate(20);
        $totalDue = FeesAllotment::where('balance', '>', 0)->sum('balance');
        
        return view('accountant.fees-reports.due', compact('dueList', 'classes', 'totalDue'));
    }

    /**
     * Display defaulters report.
     */
    public function defaulters(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        
        $query = FeesAllotment::with(['student.user', 'student.schoolClass', 'student.section', 'feeGroup'])
            ->where('balance', '>', 0)
            ->where('due_date', '<', Carbon::today());
        
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        
        $defaulters = $query->orderBy('due_date')->paginate(20);
        $totalDefaultAmount = FeesAllotment::where('balance', '>', 0)
            ->where('due_date', '<', Carbon::today())
            ->sum('balance');
        
        return view('accountant.fees-reports.defaulters', compact('defaulters', 'classes', 'totalDefaultAmount'));
    }

    /**
     * Display class-wise collection summary.
     */
    public function classWise(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        $classSummary = SchoolClass::withCount(['students'])
            ->get()
            ->map(function ($class) use ($startDate, $endDate) {
                $studentIds = $class->students->pluck('id');
                
                $totalFees = FeesAllotment::whereIn('student_id', $studentIds)->sum('total_amount');
                $collected = FeesTransaction::whereHas('feesAllotment', fn($q) => $q->whereIn('student_id', $studentIds))
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->sum('amount');
                $pending = FeesAllotment::whereIn('student_id', $studentIds)->sum('balance');
                
                return [
                    'class' => $class,
                    'total_fees' => $totalFees,
                    'collected' => $collected,
                    'pending' => $pending,
                ];
            });
        
        return view('accountant.fees-reports.class-wise', compact('classSummary', 'month'));
    }
}
