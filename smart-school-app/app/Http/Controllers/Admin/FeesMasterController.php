<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesMasterController extends Controller
{
    public function index(Request $request)
    {
        $feesMasters = collect([]);
        return view('admin.fee-masters.index', compact('feesMasters'));
    }

    public function create()
    {
        return view('admin.fee-masters.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-masters.index')->with('success', 'Fees master created successfully.');
    }

    public function show($id)
    {
        return view('admin.fee-masters.index', ['feesMasters' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.fee-masters.create', ['feesMaster' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-master.index')->with('success', 'Fees master updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-master.index')->with('success', 'Fees master deleted successfully.');
    }
}
