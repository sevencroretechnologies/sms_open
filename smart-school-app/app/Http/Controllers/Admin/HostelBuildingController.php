<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelBuildingController extends Controller
{
    public function index(Request $request)
    {
        $hostels = collect([]);
        return view('admin.hostels.index', compact('hostels'));
    }

    public function create()
    {
        return view('admin.hostels.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.hostel-buildings.index')->with('success', 'Hostel created successfully.');
    }

    public function show($id)
    {
        return view('admin.hostels.index', ['hostels' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.hostels.create', ['hostel' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.hostel-buildings.index')->with('success', 'Hostel updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.hostel-buildings.index')->with('success', 'Hostel deleted successfully.');
    }
}
