@auth
@if(auth()->user()->role === 'customer')
<div x-data="chatWidget({{ auth()->id() }})" class="fixed bottom-24 md:bottom-8 right-4 md:right-8 z-50 font-sans">
    
    <!-- Floating Button -->
    <button @click="toggleChat()" x-show="!isOpen" x-transition.scale.origin.bottom.right class="bg-green-600 hover:bg-green-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-2xl transition-transform hover:scale-110 relative">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 1.745.449 3.385 1.229 4.814L2 22l5.186-1.229C8.615 21.551 10.255 22 12 22c5.523 22 10-4.477 10-10S17.523 2 12 2zm0 18c-1.42 0-2.77-.333-3.979-.925l-.286-.14-.383.09-2.613.62.62-2.614.09-.383-.14-.286A7.95 7.95 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"></path><path d="M8 11h8v2H8z"></path></svg>
        <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white" x-text="unreadCount"></span>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" x-transition.opacity.scale.origin.bottom.right class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-[350px] sm:w-[380px] h-[500px] max-h-[80vh] flex flex-col border border-gray-100 dark:border-gray-700 overflow-hidden" style="display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-500 p-4 text-white flex justify-between items-center relative overflow-hidden">
            <!-- Decorative wave -->
            <svg class="absolute bottom-0 left-0 w-full h-auto text-white dark:text-gray-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" style="height: 25px;"><path fill="currentColor" fill-opacity="1" d="M0,160L48,170.7C96,181,192,203,288,197.3C384,192,480,160,576,149.3C672,139,768,149,864,165.3C960,181,1056,203,1152,202.7C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
            
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border-2 border-green-300">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=ffffff&color=16a34a" class="rounded-full w-full h-full">
                </div>
                <div>
                    <h3 class="font-bold text-lg leading-tight">Chat with us!</h3>
                    <p class="text-xs text-green-100">We typically reply in few minutes.</p>
                </div>
            </div>
            
            <button @click="toggleChat()" class="text-white hover:text-green-200 transition relative z-10 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-900" id="chatWidgetMessages">
            <template x-for="message in messages" :key="message.id">
                <div class="mb-4 flex" :class="message.sender_id === userId ? 'justify-end' : 'justify-start'">
                    
                    <div x-show="message.sender_id !== userId" class="w-6 h-6 rounded-full bg-gray-300 mr-2 flex-shrink-0 mt-auto mb-1 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="w-full h-full">
                    </div>

                    <div class="max-w-[75%] rounded-2xl p-3 text-sm relative group"
                         :class="message.sender_id === userId ? 'bg-green-600 text-white rounded-br-sm' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-sm shadow-sm border border-gray-100 dark:border-gray-700'">
                         
                        <!-- Edit/Delete options for own messages -->
                        <div x-show="message.sender_id === userId" class="absolute top-1 -left-16 hidden group-hover:flex gap-1 bg-white dark:bg-gray-700 rounded-lg shadow px-1 py-1 z-10">
                            <button @click="startEdit(message)" class="text-green-500 hover:text-green-700 p-1 rounded">✏️</button>
                            <button @click="deleteMessage(message.id)" class="text-red-500 hover:text-red-700 p-1 rounded">🗑️</button>
                        </div>

                        <div x-show="message.attachment" class="mb-2">
                            <img :src="'/storage/' + message.attachment" class="rounded-lg max-h-32 object-contain cursor-pointer" @click="window.open('/storage/' + message.attachment, '_blank')">
                        </div>
                        
                        <p class="whitespace-pre-wrap leading-relaxed" x-text="message.content"></p>
                    </div>
                </div>
            </template>
            
            <div x-show="messages.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-3">
                    <span class="text-3xl">👋</span>
                </div>
                <p class="text-sm font-medium">Hello there!</p>
                <p class="text-xs">How can we help you today?</p>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <!-- Edit Indicator -->
            <div x-show="editingMessage" class="flex justify-between items-center mb-2 px-2 py-1 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-xs rounded">
                <span>Editing message...</span>
                <button @click="cancelEdit()" class="font-bold hover:underline">Cancel</button>
            </div>
            
            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <div class="relative">
                    <input type="file" x-ref="widgetFileInput" class="hidden" accept="image/*" @change="handleFileSelect">
                    <button type="button" @click="$refs.widgetFileInput.click()" class="p-2 text-gray-400 hover:text-green-600 transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 relative">
                    <div x-show="selectedFile" class="absolute bottom-full left-0 mb-2 bg-green-50 dark:bg-gray-700 p-1.5 rounded flex items-center gap-2 z-10 w-full shadow-sm">
                        <span class="text-xs text-green-700 dark:text-green-300 font-medium truncate flex-1" x-text="selectedFile?.name"></span>
                        <button type="button" @click="selectedFile = null; $refs.widgetFileInput.value = ''" class="text-red-500 font-bold px-1">×</button>
                    </div>
                    <textarea x-model="newMessage" @keydown.enter.prevent="if(!event.shiftKey) sendMessage()" placeholder="Hit the buttons to respond..." class="w-full bg-transparent border-none focus:ring-0 resize-none py-2 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400" rows="1"></textarea>
                </div>
                
                <button type="submit" :disabled="!newMessage.trim() && !selectedFile" class="p-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-full transition flex items-center justify-center focus:outline-none">
                    <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <span class="text-[9px] text-gray-300 uppercase tracking-widest font-semibold">Powered by SLSeeds</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatWidget', (userId) => ({
        userId,
        isOpen: false,
        messages: [],
        newMessage: '',
        selectedFile: null,
        editingMessage: null,
        unreadCount: 0,
        conversationId: null,
        
        init() {
            // First time they open it or periodically check for unreads
            this.fetchMessages();
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                this.scrollToBottom();
            }
        },
        
        fetchMessages() {
            fetch('/chat/messages')
                .then(res => res.json())
                .then(data => {
                    this.messages = data;
                    if (data.length > 0) {
                        this.conversationId = data[0].conversation_id;
                        this.setupReverb();
                    }
                    if (this.isOpen) this.scrollToBottom();
                });
        },
        
        setupReverb() {
            if (window.Echo && this.conversationId) {
                window.Echo.private('chat.' + this.conversationId)
                    .listen('MessageSent', (e) => {
                        const idx = this.messages.findIndex(m => m.id === e.message.id);
                        if (idx > -1) {
                            this.messages[idx] = e.message;
                        } else {
                            this.messages.push(e.message);
                            if (!this.isOpen && e.message.sender_id !== this.userId) {
                                this.unreadCount++;
                            }
                            if (this.isOpen) this.scrollToBottom();
                        }
                    });
            }
        },
        
        handleFileSelect(e) {
            this.selectedFile = e.target.files[0];
        },
        
        startEdit(msg) {
            this.editingMessage = msg;
            this.newMessage = msg.content;
            this.$refs.widgetFileInput.value = '';
            this.selectedFile = null;
        },
        
        cancelEdit() {
            this.editingMessage = null;
            this.newMessage = '';
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() && !this.selectedFile) return;
            
            if (this.editingMessage) {
                // Edit logic remains the same...
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('content', this.newMessage);
                
                try {
                    const res = await fetch(`/chat/messages/${this.editingMessage.id}`, {
                        method: 'POST',
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

            // Normal send - Optimistic Update
            const tempId = 'temp_' + Date.now();
            const tempMessage = {
                id: tempId,
                sender_id: this.userId,
                content: this.newMessage,
                attachment: this.selectedFile ? URL.createObjectURL(this.selectedFile) : null,
                created_at: new Date().toISOString(),
                is_temp: true // Flag to show it's sending if we want to style it
            };
            
            this.messages.push(tempMessage);
            this.scrollToBottom();

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('content', this.newMessage);
            if (this.selectedFile) {
                formData.append('image', this.selectedFile);
            }
            
            const contentBackup = this.newMessage;
            this.newMessage = '';
            this.selectedFile = null;
            this.$refs.widgetFileInput.value = '';
            
            try {
                const res = await fetch('/chat/messages', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if (!res.ok) {
                    throw new Error(data.message || 'Error sending message');
                }
                
                // Replace temp message with real message reactively
                const idx = this.messages.findIndex(m => m.id === tempId);
                if(idx > -1) {
                    // Use splice for Alpine reactivity!
                    this.messages.splice(idx, 1, data);
                } else {
                    this.messages.push(data);
                }
                
                if (!this.conversationId) {
                    this.conversationId = data.conversation_id;
                    this.setupReverb();
                }
                this.scrollToBottom();
            } catch(e) {
                // Remove temp message if failed
                this.messages = this.messages.filter(m => m.id !== tempId);
                this.newMessage = contentBackup;
                console.error("Message send error:", e);
                // Optionally show error to user
                // alert("Failed to send: " + e.message);
            }
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
                const container = document.getElementById('chatWidgetMessages');
                if (container) container.scrollTop = container.scrollHeight;
            }, 50);
        }
    }));
});
</script>
@endif
@endauth
