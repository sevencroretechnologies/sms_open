<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesCollectionController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.fees.collect');
    }

    public function collectForm($student)
    {
        return view('admin.fees.collect', ['student' => null]);
    }

    public function collect(Request $request, $student)
    {
        return redirect()->route('admin.fees-collection.index')->with('success', 'Fee collected successfully.');
    }

    public function receipt($transaction)
    {
        return view('admin.fees.receipt', ['transaction' => null]);
    }

    public function report()
    {
        return view('admin.fees.reports');
    }

    public function dueReport()
    {
        return view('admin.fees.reports');
    }

    public function export()
    {
        return redirect()->route('admin.fees-collection.index')->with('success', 'Export started.');
    }
}
