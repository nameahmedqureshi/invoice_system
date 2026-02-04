<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $users = User::where('role', 'client')->withCount([
                'sentMessages as unread_count' => function ($query) {
                    $query->where('receiver_id', Auth::id())->where('is_read', false);
                }
            ])->get();
            return view('admin.chat.index', compact('users'));
        } else {
            // Check for existing admin
            $admin = User::where('role', 'admin')->first();
            return view('client.chat.index', compact('admin'));
        }
    }

    public function show(User $user)
    {
        // For polling, we might just return the view, and JS fetches messages
        return view('admin.chat.show', compact('user'));
    }

    public function fetchMessages($userId)
    {
        $currentUserId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $currentUserId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
