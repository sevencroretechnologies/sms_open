<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = collect([]);
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.expenses.index')->with('success', 'Expense created successfully.');
    }

    public function show($id)
    {
        return view('admin.expenses.index', ['expenses' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.expenses.create', ['expense' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function report()
    {
        return view('admin.accounting.report');
    }
}
