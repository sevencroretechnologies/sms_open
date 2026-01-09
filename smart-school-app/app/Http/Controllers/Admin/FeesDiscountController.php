<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeesDiscountController extends Controller
{
    public function index(Request $request)
    {
        $discounts = collect([]);
        return view('admin.fees.discounts', compact('discounts'));
    }

    public function create()
    {
        return view('admin.fees.discounts-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.fees-discounts.index')->with('success', 'Discount created successfully.');
    }

    public function show($id)
    {
        return view('admin.fees.discounts', ['discounts' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.fees.discounts-create', ['discount' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.fees-discounts.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.fees-discounts.index')->with('success', 'Discount deleted successfully.');
    }
}
