<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ExpenseController
 * 
 * Handles expense management for accountants.
 */
class ExpenseController extends Controller
{
    /**
     * Display expenses list.
     */
    public function index(Request $request)
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        
        $query = Expense::with(['category', 'createdBy']);
        
        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        
        $totalExpenses = Expense::whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');
        
        return view('accountant.expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    /**
     * Show create expense form.
     */
    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('accountant.expenses.create', compact('categories'));
    }

    /**
     * Store new expense.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'invoice_no' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            Expense::create([
                'title' => $request->title,
                'expense_category_id' => $request->expense_category_id,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'description' => $request->description,
                'invoice_no' => $request->invoice_no,
                'vendor' => $request->vendor,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('accountant.expenses.index')->with('success', 'Expense added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add expense. Please try again.');
        }
    }

    /**
     * Show expense details.
     */
    public function show($id)
    {
        $expense = Expense::with(['category', 'createdBy'])->findOrFail($id);
        return view('accountant.expenses.show', compact('expense'));
    }

    /**
     * Show edit expense form.
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('accountant.expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update expense.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'invoice_no' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:255',
        ]);

        $expense = Expense::findOrFail($id);

        DB::beginTransaction();
        try {
            $expense->update([
                'title' => $request->title,
                'expense_category_id' => $request->expense_category_id,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'description' => $request->description,
                'invoice_no' => $request->invoice_no,
                'vendor' => $request->vendor,
            ]);

            DB::commit();
            return redirect()->route('accountant.expenses.index')->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update expense. Please try again.');
        }
    }

    /**
     * Delete expense.
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $expense->delete();
            DB::commit();
            return redirect()->route('accountant.expenses.index')->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete expense. Please try again.');
        }
    }

    /**
     * Display expense categories.
     */
    public function categories()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->paginate(20);
        
        return view('accountant.expenses.categories', compact('categories'));
    }

    /**
     * Store new expense category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('accountant.expenses.categories')->with('success', 'Category added successfully.');
    }
}
