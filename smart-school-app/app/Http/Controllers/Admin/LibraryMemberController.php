<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryMemberController extends Controller
{
    public function index(Request $request)
    {
        $members = collect([]);
        return view('admin.library.members', compact('members'));
    }

    public function create()
    {
        return view('admin.library.members-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.library-members.index')->with('success', 'Member added successfully.');
    }

    public function show($id)
    {
        return view('admin.library.members', ['members' => collect([])]);
    }

    public function edit($id)
    {
        return view('admin.library.members-create', ['member' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.library-members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.library-members.index')->with('success', 'Member deleted successfully.');
    }

    public function card($id)
    {
        return view('admin.library.members', ['member' => null]);
    }
}
