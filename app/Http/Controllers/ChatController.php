<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    // Get or create conversation for the logged-in customer
    public function index()
    {
        $conversation = Conversation::firstOrCreate(
            ['user_id' => auth()->id()]
        );

        return view('chat.index', compact('conversation'));
    }

    // Fetch messages for AJAX
    public function getMessages()
    {
        $conversation = Conversation::firstOrCreate(
            ['user_id' => auth()->id()]
        );
        
        $messages = $conversation->messages()->with('sender')->get();
        return response()->json($messages);
    }

    // Send a message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required_without:image|string|nullable',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $conversation = Conversation::firstOrCreate(
            ['user_id' => auth()->id()]
        );

        $attachmentPath = null;
        if ($request->hasFile('image')) {
            $attachmentPath = $request->file('image')->store('chat_images', 'public');
        }

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content,
            'attachment' => $attachmentPath,
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new MessageSent($message->load('sender')));
        } catch (\Throwable $e) {
            \Log::error('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json($message->load('sender'));
    }

    public function editMessage(Request $request, Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['content' => 'required|string']);
        $message->update(['content' => $request->content]);

        return response()->json($message->load('sender'));
    }

    public function deleteMessage(Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($message->attachment) {
            Storage::disk('public')->delete($message->attachment);
        }
        
        $message->delete();

        return response()->json(['success' => true]);
    }
}
