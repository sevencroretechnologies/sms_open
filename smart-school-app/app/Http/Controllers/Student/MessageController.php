<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MessageController
 * 
 * Handles message viewing for students.
 */
class MessageController extends Controller
{
    /**
     * Display inbox messages.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $messages = MessageRecipient::where('recipient_id', $user->id)
            ->with(['message.sender'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('message', function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $unreadCount = MessageRecipient::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();
        
        return view('student.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display a specific message.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $messageRecipient = MessageRecipient::where('recipient_id', $user->id)
            ->where('message_id', $id)
            ->with(['message.sender', 'message.attachments'])
            ->firstOrFail();
        
        if (!$messageRecipient->read_at) {
            $messageRecipient->read_at = now();
            $messageRecipient->save();
        }
        
        return view('student.messages.show', compact('messageRecipient'));
    }
}
