<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $messages = collect([]);
        return view('admin.communication.sms', compact('messages'));
    }

    public function compose()
    {
        return view('admin.communication.compose-sms');
    }

    public function send(Request $request)
    {
        return redirect()->route('admin.sms.index')->with('success', 'SMS sent successfully.');
    }

    public function show($id)
    {
        return view('admin.communication.sms-show', ['message' => null]);
    }

    public function templates()
    {
        return view('admin.communication.sms-templates', ['templates' => collect([])]);
    }

    public function createTemplate()
    {
        return view('admin.communication.sms-template-create');
    }

    public function storeTemplate(Request $request)
    {
        return redirect()->route('admin.sms.templates')->with('success', 'Template created successfully.');
    }

    public function settings()
    {
        return view('admin.communication.sms-settings');
    }
}
