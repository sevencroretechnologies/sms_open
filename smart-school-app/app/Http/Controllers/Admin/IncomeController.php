<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $incomes = collect([]);
        return view('admin.income.index', compact('incomes'));
    }

    public function create()
    {
        return view('admin.income.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.incomes.index')->with('success', 'Income created successfully.');
    }

    public function show($id)
    {
        return view('admin.income.index', ['incomes' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.income.create', ['income' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.incomes.index')->with('success', 'Income deleted successfully.');
    }

    public function report()
    {
        return view('admin.accounting.report');
    }
}
