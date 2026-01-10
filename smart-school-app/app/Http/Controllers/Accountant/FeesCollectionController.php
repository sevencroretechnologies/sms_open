<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\FeesAllotment;
use App\Models\FeesTransaction;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * FeesCollectionController
 * 
 * Handles fee collection for accountants.
 */
class FeesCollectionController extends Controller
{
    /**
     * Display fee collection dashboard.
     */
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        $query = FeesAllotment::with(['student.user', 'student.schoolClass', 'student.section', 'feeGroup'])
            ->where('balance', '>', 0);
        
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        
        if ($request->section_id) {
            $query->whereHas('student', fn($q) => $q->where('section_id', $request->section_id));
        }
        
        if ($request->search) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                    ->orWhere('admission_no', 'like', "%{$request->search}%");
            });
        }
        
        $pendingFees = $query->orderBy('due_date')->paginate(20);
        
        return view('accountant.fees-collection.index', compact('pendingFees', 'classes', 'sections'));
    }

    /**
     * Show collect fee form for a student.
     */
    public function collect($allotmentId)
    {
        $allotment = FeesAllotment::with(['student.user', 'student.schoolClass', 'student.section', 'feeGroup', 'feesMasters.feeType'])
            ->findOrFail($allotmentId);
        
        return view('accountant.fees-collection.collect', compact('allotment'));
    }

    /**
     * Process fee payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'allotment_id' => 'required|exists:fees_allotments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,card,bank_transfer,cheque,online',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $allotment = FeesAllotment::findOrFail($request->allotment_id);
        
        if ($request->amount > $allotment->balance) {
            return back()->with('error', 'Payment amount cannot exceed the balance due.');
        }

        DB::beginTransaction();
        try {
            $transaction = FeesTransaction::create([
                'fees_allotment_id' => $allotment->id,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'collected_by' => Auth::id(),
                'remarks' => $request->remarks,
            ]);

            $allotment->paid_amount += $request->amount;
            $allotment->balance -= $request->amount;
            $allotment->save();

            DB::commit();
            
            return redirect()->route('accountant.fees-collection.receipt', $transaction->id)
                ->with('success', 'Payment collected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payment. Please try again.');
        }
    }

    /**
     * Display payment receipt.
     */
    public function receipt($transactionId)
    {
        $transaction = FeesTransaction::with(['feesAllotment.student.user', 'feesAllotment.student.schoolClass', 'feesAllotment.student.section', 'feesAllotment.feeGroup', 'collector'])
            ->findOrFail($transactionId);
        
        return view('accountant.fees-collection.receipt', compact('transaction'));
    }

    /**
     * Search students for fee collection.
     */
    public function search(Request $request)
    {
        $students = Student::with(['user', 'schoolClass', 'section'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('admission_no', 'like', "%{$search}%");
            })
            ->take(20)
            ->get();
        
        return view('accountant.fees-collection.search', compact('students'));
    }

    /**
     * Show student's fee details.
     */
    public function studentFees($studentId)
    {
        $student = Student::with(['user', 'schoolClass', 'section'])->findOrFail($studentId);
        
        $allotments = FeesAllotment::where('student_id', $studentId)
            ->with(['feeGroup', 'feesMasters.feeType'])
            ->orderBy('due_date', 'desc')
            ->get();
        
        $transactions = FeesTransaction::whereHas('feesAllotment', fn($q) => $q->where('student_id', $studentId))
            ->with(['feesAllotment.feeGroup'])
            ->orderBy('payment_date', 'desc')
            ->take(20)
            ->get();
        
        return view('accountant.fees-collection.student-fees', compact('student', 'allotments', 'transactions'));
    }
}
