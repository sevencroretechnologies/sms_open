<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\ParentModel;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * MessageController
 * 
 * Handles messaging for parents.
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
        
        return view('parent.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display sent messages.
     */
    public function sent(Request $request)
    {
        $user = Auth::user();
        
        $messages = Message::where('sender_id', $user->id)
            ->with(['recipients.recipient'])
            ->when($request->search, function ($query, $search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('parent.messages.sent', compact('messages'));
    }

    /**
     * Show compose message form.
     */
    public function create()
    {
        $teachers = Teacher::with('user')
            ->whereHas('user')
            ->get();
        
        return view('parent.messages.create', compact('teachers'));
    }

    /**
     * Send a message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $message = Message::create([
                'sender_id' => $user->id,
                'subject' => $request->subject,
                'body' => $request->body,
            ]);

            MessageRecipient::create([
                'message_id' => $message->id,
                'recipient_id' => $request->recipient_id,
            ]);

            DB::commit();
            
            return redirect()->route('parent.messages.sent')
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send message. Please try again.');
        }
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
            ->first();
        
        if ($messageRecipient) {
            if (!$messageRecipient->read_at) {
                $messageRecipient->read_at = now();
                $messageRecipient->save();
            }
            return view('parent.messages.show', compact('messageRecipient'));
        }
        
        $message = Message::where('sender_id', $user->id)
            ->where('id', $id)
            ->with(['recipients.recipient', 'attachments'])
            ->firstOrFail();
        
        return view('parent.messages.show-sent', compact('message'));
    }
}
