@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 h-[calc(100vh-100px)] flex flex-col" x-data="chatApp({{ $conversation->id }}, {{ auth()->id() }})">
    <div class="bg-white dark:bg-gray-800 rounded-t-xl shadow-sm border-b border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold">
                Admin
            </div>
            <div>
                <h2 class="font-bold text-gray-800 dark:text-gray-100">Support Chat</h2>
                <p class="text-xs text-green-500">We usually reply within a few minutes.</p>
            </div>
        </div>
    </div>
    
    <div class="flex-1 bg-gray-50 dark:bg-gray-900 p-4 overflow-y-auto" id="chat-messages" x-ref="messagesContainer">
        <template x-for="message in messages" :key="message.id">
            <div class="mb-4 flex" :class="message.sender_id === userId ? 'justify-end' : 'justify-start'">
                <div class="max-w-[75%] rounded-2xl p-3 relative group"
                     :class="message.sender_id === userId ? 'bg-green-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-none shadow-sm'">
                     
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
            <span class="text-4xl mb-2">👋</span>
            <p>Send a message to start chatting with us!</p>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-b-xl shadow-sm border-t border-gray-200 dark:border-gray-700 p-4">
        <!-- Edit Indicator -->
        <div x-show="editingMessage" class="flex justify-between items-center mb-2 px-3 py-2 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-xs rounded-lg">
            <span>Editing message...</span>
            <button @click="cancelEdit()" class="font-bold hover:underline">Cancel</button>
        </div>
        
        <form @submit.prevent="sendMessage" class="flex items-end gap-2">
            <div class="relative">
                <input type="file" x-ref="fileInput" class="hidden" accept="image/*" @change="handleFileSelect">
                <button type="button" @click="$refs.fileInput.click()" class="p-3 text-gray-500 hover:text-green-600 transition bg-gray-100 dark:bg-gray-700 rounded-full">
                    📷
                </button>
            </div>
            
            <div class="flex-1 relative">
                <div x-show="selectedFile" class="absolute bottom-full left-0 mb-2 bg-green-50 dark:bg-gray-700 p-2 rounded flex items-center gap-2">
                    <span class="text-xs text-green-700 dark:text-green-300 font-medium truncate max-w-xs" x-text="selectedFile?.name"></span>
                    <button type="button" @click="selectedFile = null; $refs.fileInput.value = ''" class="text-red-500 font-bold">×</button>
                </div>
                <textarea x-model="newMessage" @keydown.enter.prevent="if(!event.shiftKey) sendMessage()" placeholder="Type a message..." class="w-full bg-gray-100 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-green-500 resize-none px-4 py-3 text-gray-800 dark:text-gray-100" rows="1"></textarea>
            </div>
            
            <button type="submit" :disabled="!newMessage.trim() && !selectedFile" class="p-3 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-full transition flex items-center justify-center w-12 h-12">
                <svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatApp', (conversationId, userId) => ({
        conversationId,
        userId,
        messages: [],
        newMessage: '',
        selectedFile: null,
        editingMessage: null,
        
        init() {
            this.fetchMessages();
            
            // Setup Reverb listening
            if (window.Echo) {
                window.Echo.private('chat.' + this.conversationId)
                    .listen('MessageSent', (e) => {
                        // Check if message is already there (from our own send) or update if edited
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
        
        fetchMessages() {
            fetch('/chat/messages')
                .then(res => res.json())
                .then(data => {
                    this.messages = data;
                    this.scrollToBottom();
                });
        },
        
        handleFileSelect(e) {
            this.selectedFile = e.target.files[0];
        },
        
        startEdit(msg) {
            this.editingMessage = msg;
            this.newMessage = msg.content;
            this.$refs.fileInput.value = '';
            this.selectedFile = null;
        },
        
        cancelEdit() {
            this.editingMessage = null;
            this.newMessage = '';
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() && !this.selectedFile) return;
            
            if (this.editingMessage) {
                // Edit mode
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('content', this.newMessage);
                
                try {
                    const res = await fetch(`/chat/messages/${this.editingMessage.id}`, {
                        method: 'POST', // Form spoofing
                        body: formData
                    });
                    const data = await res.json();
                    if(data.id) {
                        const idx = this.messages.findIndex(m => m.id === data.id);
                        if(idx > -1) this.messages[idx] = data;
                    }
                    this.cancelEdit();
                } catch(e) { console.error(e); }
                return;
            }

            // Normal send
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('content', this.newMessage);
            if (this.selectedFile) {
                formData.append('image', this.selectedFile);
            }
            
            const contentBackup = this.newMessage;
            this.newMessage = '';
            this.selectedFile = null;
            this.$refs.fileInput.value = '';
            
            try {
                const res = await fetch('/chat/messages', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if(data.id) {
                    this.messages.push(data);
                    this.scrollToBottom();
                }
            } catch(e) {
                this.newMessage = contentBackup;
                Swal.fire('Error', 'Could not send message', 'error');
            }
        },
        
        async deleteMessage(id) {
            if(!confirm('Delete this message?')) return;
            
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const res = await fetch(`/chat/messages/${id}`, {
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
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            }, 50);
        },
        
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
    }));
});
</script>
@endsection
