<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::with('user')
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        $activeConversation = null;
        if ($request->has('id')) {
            $activeConversation = Conversation::with(['messages.sender', 'user'])->findOrFail($request->id);
            // Mark unread messages as read
            $activeConversation->messages()->where('sender_id', '!=', auth()->id())->update(['is_read' => true]);
        }

        return view('admin.chats.index', compact('conversations', 'activeConversation'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required_without:image|string|nullable',
            'image' => 'nullable|image|max:5120',
        ]);

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
        } catch (\Throwable $e) {}

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

    public function deleteChat(Conversation $conversation)
    {
        // Delete all message attachments first
        foreach ($conversation->messages as $msg) {
            if ($msg->attachment) {
                Storage::disk('public')->delete($msg->attachment);
            }
        }
        
        $conversation->delete();
        
        return redirect()->route('admin.chats.index')->with('success', 'Chat deleted successfully.');
    }
}
