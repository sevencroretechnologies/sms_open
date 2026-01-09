<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    public function index(Request $request)
    {
        $routes = collect([]);
        return view('admin.transport.routes', compact('routes'));
    }

    public function create()
    {
        return view('admin.transport.routes-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.transport-routes.index')->with('success', 'Route created successfully.');
    }

    public function show($id)
    {
        return view('admin.transport.routes-show', ['route' => null]);
    }

    public function edit($id)
    {
        return view('admin.transport.routes-create', ['route' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.transport-routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.transport-routes.index')->with('success', 'Route deleted successfully.');
    }

    public function stops($route)
    {
        return view('admin.transport.stops', ['route' => null]);
    }

    public function saveStops(Request $request, $route)
    {
        return redirect()->route('admin.transport-routes.index')->with('success', 'Stops saved successfully.');
    }
}
