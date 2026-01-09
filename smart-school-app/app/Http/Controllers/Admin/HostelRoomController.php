<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelRoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = collect([]);
        return view('admin.hostels.rooms', compact('rooms'));
    }

    public function create()
    {
        return view('admin.hostels.rooms-create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room created successfully.');
    }

    public function show($id)
    {
        return view('admin.hostels.rooms-show', ['room' => null]);
    }

    public function edit($id)
    {
        return view('admin.hostels.rooms-create', ['room' => null]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.hostel-rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function byBuilding($building)
    {
        return view('admin.hostels.rooms', ['rooms' => collect([])]);
    }
}
