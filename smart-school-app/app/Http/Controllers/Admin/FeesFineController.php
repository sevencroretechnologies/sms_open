<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesFineController extends Controller
{
    public function index(Request $request)
    {
        $fines = collect([]);
        return view('admin.fees.fines', compact('fines'));
    }

    public function create()
    {
        return view('admin.fees.fines-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-fines.index')->with('success', 'Fine created successfully.');
    }

    public function show($id)
    {
        return view('admin.fees.fines', ['fines' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.fees.fines-create', ['fine' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-fines.index')->with('success', 'Fine updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-fines.index')->with('success', 'Fine deleted successfully.');
    }
}
