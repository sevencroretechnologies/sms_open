<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $notices = collect([]);
        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.notices.index')->with('success', 'Notice created successfully.');
    }

    public function show($id)
    {
        return view('admin.notices.show', ['notice' => null]);
    }

    public function edit($id)
    {
        return view('admin.notices.edit', ['notice' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.notices.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.notices.index')->with('success', 'Notice deleted successfully.');
    }

    public function publish($id)
    {
        return redirect()->route('admin.notices.index')->with('success', 'Notice published successfully.');
    }
}
