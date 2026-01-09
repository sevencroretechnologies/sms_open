<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = collect([]);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show($id)
    {
        return view('admin.roles.show', ['role' => null]);
    }

    public function edit($id)
    {
        return view('admin.roles.create', ['role' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissions($id)
    {
        return view('admin.roles.permissions', ['role' => null]);
    }

    public function updatePermissions(Request $request, $id)
    {
        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated successfully.');
    }
}
