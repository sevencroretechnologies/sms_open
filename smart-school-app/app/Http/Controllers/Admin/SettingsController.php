<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function general()
    {
        return view('admin.settings.general');
    }

    public function updateGeneral(Request $request)
    {
        return redirect()->route('admin.settings.general')->with('success', 'Settings updated successfully.');
    }

    public function school()
    {
        return view('admin.settings.school');
    }

    public function updateSchool(Request $request)
    {
        return redirect()->route('admin.settings.school')->with('success', 'School settings updated successfully.');
    }

    public function email()
    {
        return view('admin.settings.email');
    }

    public function updateEmail(Request $request)
    {
        return redirect()->route('admin.settings.email')->with('success', 'Email settings updated successfully.');
    }

    public function sms()
    {
        return view('admin.settings.sms');
    }

    public function updateSms(Request $request)
    {
        return redirect()->route('admin.settings.sms')->with('success', 'SMS settings updated successfully.');
    }

    public function payment()
    {
        return view('admin.settings.payment');
    }

    public function updatePayment(Request $request)
    {
        return redirect()->route('admin.settings.payment')->with('success', 'Payment settings updated successfully.');
    }
}
