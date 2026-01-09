<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesTypeController extends Controller
{
    public function index(Request $request)
    {
        $feesTypes = collect([]);
        return view('admin.fee-types.index', compact('feesTypes'));
    }

    public function create()
    {
        return view('admin.fee-types.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-types.index')->with('success', 'Fee type created successfully.');
    }

    public function show($id)
    {
        return view('admin.fee-types.index', ['feesTypes' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.fee-types.create', ['feesType' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-types.index')->with('success', 'Fee type updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-types.index')->with('success', 'Fee type deleted successfully.');
    }
}
