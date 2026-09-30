@extends('layouts.admin')

@section('content')
<div class="h-[calc(100vh-140px)] flex bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Conversations List -->
    <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-900">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Customer Chats</h2>
        </div>
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conv)
                <a href="{{ route('admin.chats.index', ['id' => $conv->id]) }}" class="block p-4 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800 transition {{ request('id') == $conv->id ? 'bg-green-50 dark:bg-gray-800 border-l-4 border-l-green-500' : '' }}">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-gray-800 dark:text-gray-200">{{ $conv->user->name ?? 'User' }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}</span>
                    </div>
                    @php
                        $unread = $conv->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                    @endphp
                    @if($unread > 0)
                        <span class="inline-block bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $unread }} new</span>
                    @endif
                </a>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">No chats yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Active Chat Area -->
    <div class="w-2/3 flex flex-col bg-white dark:bg-gray-800">
        @if($activeConversation)
            <div x-data="adminChat({{ $activeConversation->id }}, {{ auth()->id() }})" x-init="initChat" class="flex flex-col h-full w-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ $activeConversation->user->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activeConversation->user->email }}</p>
                    </div>
                    <form action="{{ route('admin.chats.destroy', $activeConversation->id) }}" method="POST" onsubmit="return confirmDeleteChat(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 px-3 py-1.5 rounded-lg text-sm transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Chat
                        </button>
                    </form>
                </div>
                
                <div class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-900" id="admin-chat-messages">
                <template x-for="message in messages" :key="message.id">
                    <div class="mb-4 flex" :class="message.sender_id === userId ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[75%] rounded-2xl p-3 relative group"
                             :class="message.sender_id === userId ? 'bg-green-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-none shadow-sm border border-gray-200 dark:border-gray-700'">
                             
                            <!-- Edit/Delete options for own messages -->
                            <div x-show="message.sender_id === userId" class="absolute top-2 -left-16 hidden group-hover:flex gap-1 bg-white dark:bg-gray-700 rounded-lg shadow px-1 py-1">
                                <button @click="startEdit(message)" class="text-blue-500 hover:text-blue-700 p-1 rounded">✏️</button>
                                <button @click="deleteMessage(message.id)" class="text-red-500 hover:text-red-700 p-1 rounded">🗑️</button>
                            </div>

                            <div x-show="message.attachment" class="mb-2">
                                <img :src="'/storage/' + message.attachment" class="rounded-lg max-h-48 object-contain cursor-pointer" @click="window.open('/storage/' + message.attachment, '_blank')">
                            </div>
                            
                            <p class="text-sm whitespace-pre-wrap" x-text="message.content"></p>
                            <p class="text-[10px] opacity-70 text-right mt-1" x-text="formatDate(message.created_at)"></p>
                        </div>
                    </div>
                </template>
                
                <div x-show="messages.length === 0" class="h-full flex items-center justify-center flex-col text-gray-400">
                    <span class="text-4xl mb-2">💬</span>
                    <p>No messages yet.</p>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form id="adminSendForm" @submit.prevent="sendMessage" class="flex items-end gap-2">
                    <div class="relative">
                        <input type="file" id="adminFileInput" class="hidden" accept="image/*" onchange="document.getElementById('adminFilePreview').classList.remove('hidden'); document.getElementById('adminFileName').textContent = this.files[0].name;">
                        <button type="button" onclick="document.getElementById('adminFileInput').click()" class="p-3 text-gray-500 hover:text-green-600 transition bg-gray-100 dark:bg-gray-700 rounded-full">
                            📸
                        </button>
                    </div>
                    
                    <div class="flex-1 relative">
                        <!-- File Preview -->
                        <div id="adminFilePreview" class="hidden absolute bottom-full left-0 mb-2 bg-green-50 dark:bg-gray-700 p-1.5 rounded flex items-center gap-2 z-10 w-full shadow-sm">
                            <span id="adminFileName" class="text-xs text-green-700 dark:text-green-300 font-medium truncate flex-1"></span>
                            <button type="button" onclick="document.getElementById('adminFileInput').value = ''; document.getElementById('adminFilePreview').classList.add('hidden');" class="text-red-500 font-bold px-1">×</button>
                        </div>
                        <textarea id="adminNewMessage" placeholder="Type your reply..." class="w-full bg-gray-100 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-green-500 resize-none px-4 py-3 text-gray-800 dark:text-gray-100" rows="1" onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); document.getElementById('adminSendForm').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true})); }"></textarea>
                    </div>
                    
                    <button type="submit" class="p-3 bg-green-600 hover:bg-green-700 text-white rounded-full transition flex items-center justify-center w-12 h-12">
                        <svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                <span class="text-6xl mb-4">💬</span>
                <p class="text-lg">Select a conversation to start chatting</p>
            </div>
        @endif
    </div>
</div>

@if($activeConversation)
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminChat', (conversationId, userId) => ({
        conversationId,
        userId,
        messages: @json($activeConversation->messages),
        editingMessage: null,
        
        initChat() {
            this.scrollToBottom();
            
            if (window.Echo) {
                window.Echo.private('chat.' + this.conversationId)
                    .listen('MessageSent', (e) => {
                        const idx = this.messages.findIndex(m => m.id === e.message.id);
                        if (idx > -1) {
                            this.messages[idx] = e.message;
                        } else {
                            this.messages.push(e.message);
                            this.scrollToBottom();
                        }
                    });
            }
        },
        
        async sendMessage() {
            const input = document.getElementById('adminNewMessage');
            const fileInput = document.getElementById('adminFileInput');
            const content = input.value;
            const file = fileInput.files[0];
            
            if (!content.trim() && !file) return;
            
            if (this.editingMessage) {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('content', content);
                
                try {
                    const res = await fetch(`/admin/messages/${this.editingMessage.id}`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    const idx = this.messages.findIndex(m => m.id === data.id);
                    if(idx > -1) this.messages[idx] = data;
                    this.editingMessage = null;
                    input.value = '';
                } catch(e) {}
                return;
            }

            // Optimistic Update
            const tempId = 'temp_' + Date.now();
            const tempMessage = {
                id: tempId,
                sender_id: this.userId,
                content: content,
                attachment: file ? URL.createObjectURL(file) : null,
                created_at: new Date().toISOString(),
                is_temp: true
            };
            
            this.messages.push(tempMessage);
            this.scrollToBottom();

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('content', content);
            if (file) formData.append('image', file);
            
            const contentBackup = content; // Store it
            input.value = '';
            fileInput.value = '';
            document.getElementById('adminFilePreview').classList.add('hidden');
            
            try {
                const res = await fetch(`/admin/chats/${this.conversationId}/messages`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (!res.ok) {
                    throw new Error(data.message || 'Error sending message');
                }
                
                // Replace temp message reactively
                const idx = this.messages.findIndex(m => m.id === tempId);
                if(idx > -1) {
                    this.messages.splice(idx, 1, data);
                } else {
                    this.messages.push(data);
                }
                this.scrollToBottom();
            } catch(e) {
                // Revert on failure
                this.messages = this.messages.filter(m => m.id !== tempId);
                input.value = contentBackup; // Important: Restore text if failed
                console.error("Admin chat error:", e);
                alert("Failed to send message: " + e.message);
            }
        },
        
        startEdit(msg) {
            this.editingMessage = msg;
            document.getElementById('adminNewMessage').value = msg.content;
            document.getElementById('adminFileInput').value = '';
        },
        
        async deleteMessage(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "Delete this message?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });
            
            if(!result.isConfirmed) return;
            
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const res = await fetch(`/admin/messages/${id}`, {
                    method: 'POST',
                    body: formData
                });
                if(res.ok) {
                    this.messages = this.messages.filter(m => m.id !== id);
                }
            } catch(e) { console.error(e); }
        },
        
        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('admin-chat-messages');
                if (container) container.scrollTop = container.scrollHeight;
            }, 50);
        },
        
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
    }));
});

function confirmDeleteChat(e) {
    e.preventDefault();
    const form = e.target;
    Swal.fire({
        title: 'Delete entire chat?',
        text: "This will permanently delete this conversation and all its messages. This cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it all!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endif
@endsection
