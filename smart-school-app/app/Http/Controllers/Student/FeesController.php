<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeesAllotment;
use App\Models\FeesTransaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * FeesController
 * 
 * Handles fee viewing for students.
 */
class FeesController extends Controller
{
    /**
     * Display fee status and payment history.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $allotments = FeesAllotment::where('student_id', $student->id)
            ->with(['feeGroup', 'feesMasters.feeType'])
            ->orderBy('due_date', 'desc')
            ->get();
        
        $transactions = FeesTransaction::whereHas('feesAllotment', fn($q) => $q->where('student_id', $student->id))
            ->with(['feesAllotment.feeGroup'])
            ->orderBy('payment_date', 'desc')
            ->take(20)
            ->get();
        
        $summary = [
            'total_fees' => $allotments->sum('total_amount'),
            'paid' => $allotments->sum('paid_amount'),
            'balance' => $allotments->sum('balance'),
        ];
        
        return view('student.fees.index', compact('allotments', 'transactions', 'summary'));
    }

    /**
     * Display payment history.
     */
    public function history()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $transactions = FeesTransaction::whereHas('feesAllotment', fn($q) => $q->where('student_id', $student->id))
            ->with(['feesAllotment.feeGroup'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);
        
        return view('student.fees.history', compact('transactions'));
    }
}
