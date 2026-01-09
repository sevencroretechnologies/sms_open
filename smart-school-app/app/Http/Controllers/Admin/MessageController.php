<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = collect([]);
        return view('admin.messages.inbox', compact('messages'));
    }

    public function inbox()
    {
        return view('admin.messages.inbox', ['messages' => collect([])]);
    }

    public function sent()
    {
        return view('admin.messages.sent', ['messages' => collect([])]);
    }

    public function compose()
    {
        return view('admin.messages.compose');
    }

    public function send(Request $request)
    {
        return redirect()->route('admin.messages.sent')->with('success', 'Message sent successfully.');
    }

    public function show($id)
    {
        return view('admin.messages.show', ['message' => null]);
    }

    public function destroy($id)
    {
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully.');
    }
}
