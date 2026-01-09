<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = collect([]);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        return view('admin.users.show', ['user' => null]);
    }

    public function edit($id)
    {
        return view('admin.users.create', ['user' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
