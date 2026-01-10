<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * ProfileController
 * 
 * Handles student profile viewing and editing.
 */
class ProfileController extends Controller
{
    /**
     * Display the student's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section', 'category', 'user'])
            ->first();
        
        return view('student.profile.index', compact('student', 'user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section', 'user'])
            ->first();
        
        return view('student.profile.edit', compact('student', 'user'));
    }

    /**
     * Update the student's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->phone) {
            $user->phone = $request->phone;
        }
        
        if ($request->current_password && $request->new_password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            $user->password = Hash::make($request->new_password);
        }
        
        $user->save();
        
        $student = Student::where('user_id', $user->id)->first();
        if ($student && $request->address) {
            $student->current_address = $request->address;
            $student->save();
        }
        
        return redirect()->route('student.profile.index')
            ->with('success', 'Profile updated successfully.');
    }
}
