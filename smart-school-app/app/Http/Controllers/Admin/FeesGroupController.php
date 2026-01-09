<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesGroupController extends Controller
{
    public function index(Request $request)
    {
        $feesGroups = collect([]);
        return view('admin.fee-groups.index', compact('feesGroups'));
    }

    public function create()
    {
        return view('admin.fee-groups.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-groups.index')->with('success', 'Fee group created successfully.');
    }

    public function show($id)
    {
        return view('admin.fee-groups.index', ['feesGroups' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.fee-groups.create', ['feesGroup' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-groups.index')->with('success', 'Fee group updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-groups.index')->with('success', 'Fee group deleted successfully.');
    }
}
