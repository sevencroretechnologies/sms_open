<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportVehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = collect([]);
        return view('admin.transport.vehicles', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.transport.vehicles-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.transport-vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function show($id)
    {
        return view('admin.transport.vehicles-show', ['vehicle' => null]);
    }

    public function edit($id)
    {
        return view('admin.transport.vehicles-create', ['vehicle' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.transport-vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.transport-vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
