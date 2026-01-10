<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\ParentModel;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * MessageController
 * 
 * Handles messaging for teachers.
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
                $query->whereHas('message', fn($q) => $q->where('subject', 'like', "%{$search}%"));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = MessageRecipient::where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('teacher.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display sent messages.
     */
    public function sent(Request $request)
    {
        $user = Auth::user();
        
        $messages = Message::where('sender_id', $user->id)
            ->with(['recipients.recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('teacher.messages.sent', compact('messages'));
    }

    /**
     * Show compose form.
     */
    public function create()
    {
        $teacher = Auth::user();
        $classIds = $this->getTeacherClassIds($teacher);
        $sectionIds = $this->getTeacherSectionIds($teacher);
        
        $students = Student::whereIn('class_id', $classIds)
            ->whereIn('section_id', $sectionIds)
            ->active()
            ->with('user')
            ->get();
        
        $parents = ParentModel::whereHas('children', function ($query) use ($classIds, $sectionIds) {
            $query->whereIn('class_id', $classIds)->whereIn('section_id', $sectionIds);
        })->with('user')->get();
        
        return view('teacher.messages.create', compact('students', 'parents'));
    }

    /**
     * Send a message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'exists:users,id',
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

            foreach ($request->recipient_ids as $recipientId) {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                    'is_read' => false,
                ]);
            }

            DB::commit();
            
            return redirect()->route('teacher.messages.sent')
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send message. Please try again.');
        }
    }

    /**
     * Display a message.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $messageRecipient = MessageRecipient::where('recipient_id', $user->id)
            ->where('message_id', $id)
            ->with(['message.sender'])
            ->first();
        
        if ($messageRecipient) {
            $messageRecipient->update(['is_read' => true, 'read_at' => now()]);
            return view('teacher.messages.show', ['message' => $messageRecipient->message, 'isInbox' => true]);
        }
        
        $message = Message::where('sender_id', $user->id)->findOrFail($id);
        $message->load('recipients.recipient');
        
        return view('teacher.messages.show', ['message' => $message, 'isInbox' => false]);
    }

    protected function getTeacherClassIds($teacher): array
    {
        $classTeacherClasses = Section::where('class_teacher_id', $teacher->id)->pluck('class_id')->toArray();
        $subjectClasses = DB::table('class_subjects')->where('teacher_id', $teacher->id)->pluck('class_id')->toArray();
        return array_unique(array_merge($classTeacherClasses, $subjectClasses));
    }

    protected function getTeacherSectionIds($teacher): array
    {
        $classTeacherSections = Section::where('class_teacher_id', $teacher->id)->pluck('id')->toArray();
        $subjectSections = DB::table('class_subjects')->where('teacher_id', $teacher->id)->pluck('section_id')->toArray();
        return array_unique(array_merge($classTeacherSections, $subjectSections));
    }
}
