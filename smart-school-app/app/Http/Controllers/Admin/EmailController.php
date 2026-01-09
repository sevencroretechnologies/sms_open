<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $emails = collect([]);
        return view('admin.communication.emails', compact('emails'));
    }

    public function compose()
    {
        return view('admin.communication.compose-email');
    }

    public function send(Request $request)
    {
        return redirect()->route('admin.emails.index')->with('success', 'Email sent successfully.');
    }

    public function show($id)
    {
        return view('admin.communication.email-show', ['email' => null]);
    }

    public function templates()
    {
        return view('admin.communication.email-templates', ['templates' => collect([])]);
    }

    public function createTemplate()
    {
        return view('admin.communication.email-template-create');
    }

    public function storeTemplate(Request $request)
    {
        return redirect()->route('admin.emails.templates')->with('success', 'Template created successfully.');
    }
}
