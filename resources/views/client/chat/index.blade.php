@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Support Chat</h1>
        <a href="{{ route('client.invoices.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back
            to Dashboard</a>
    </div>

    @if(!$admin)
        <div class="card" style="text-align: center; color: var(--text-muted); padding: 3rem;">
            <h3>No Admin Available</h3>
            <p>Please contact support via email.</p>
        </div>
    @else
        <div class="card" style="height: 600px; display: flex; flex-direction: column; padding: 0;">
            <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 1.5rem; background: #f9fafb;">
                <div style="text-align: center; color: var(--text-muted);">Loading conversation...</div>
            </div>

            <div style="padding: 1rem; border-top: 1px solid var(--border); background: white;">
                <form id="chat-form" style="display: flex; gap: 1rem;">
                    <input type="hidden" id="receiver_id" value="{{ $admin->id }}">
                    <input type="text" id="message-input" class="form-control" placeholder="Type your message..." required
                        autocomplete="off">
                    <button type="submit" class="btn">Send</button>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chatMessages = document.getElementById('chat-messages');
                const chatForm = document.getElementById('chat-form');
                const messageInput = document.getElementById('message-input');
                const receiverId = document.getElementById('receiver_id').value;
                const currentUserId = {{ auth()->id() }};

                // Initialize Echo
                window.Pusher = Pusher;
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: '{{ env('REVERB_APP_KEY') }}',
                    wsHost: '{{ env('REVERB_HOST') }}',
                    wsPort: {{ env('REVERB_PORT', 8080) }},
                    wssPort: {{ env('REVERB_PORT', 8080) }},
                    forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                });

                // Listen for messages
                window.Echo.private(`chat.${currentUserId}`)
                    .listen('.message.sent', (e) => {
                        console.log('Message received:', e);
                        if (parseInt(e.message.sender_id) === parseInt(receiverId)) {
                            appendMessage(e.message, false);
                            scrollToBottom();
                            fetch(`/chat/messages/${receiverId}`); // Mark as read
                        }
                    });

                function scrollToBottom() {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                function appendMessage(msg, isMe) {
                    const align = isMe ? 'flex-end' : 'flex-start';
                    const bg = isMe ? 'var(--primary)' : 'white';
                    const color = isMe ? 'white' : 'var(--text-color)';
                    const border = isMe ? 'none' : '1px solid var(--border)';

                    const html = `
                                <div style="display: flex; justify-content: ${align}; margin-bottom: 1rem;">
                                    <div style="max-width: 70%; padding: 0.75rem 1rem; border-radius: 12px; background: ${bg}; color: ${color}; border: ${border}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <div>${msg.message}</div>
                                        <div style="font-size: 0.7rem; margin-top: 0.25rem; opacity: 0.8; text-align: right;">
                                            ${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        </div>
                                    </div>
                                </div>
                            `;

                    if (chatMessages.innerHTML.includes('No messages yet') || chatMessages.innerHTML.includes('Loading conversation...')) {
                        chatMessages.innerHTML = '';
                    }

                    chatMessages.insertAdjacentHTML('beforeend', html);
                }

                async function fetchMessages() {
                    try {
                        const response = await fetch(`/chat/messages/${receiverId}`);
                        const messages = await response.json();

                        chatMessages.innerHTML = '';
                        if (messages.length > 0) {
                            messages.forEach(msg => {
                                appendMessage(msg, parseInt(msg.sender_id) === currentUserId);
                            });
                            scrollToBottom();
                        } else {
                            chatMessages.innerHTML = '<div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">No messages yet. Start the conversation!</div>';
                        }
                    } catch (error) {
                        console.error('Error fetching messages:', error);
                    }
                }

                fetchMessages();

                chatForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const messageText = messageInput.value;
                    if (!messageText.trim()) return;

                    messageInput.value = '';

                    try {
                        const response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                receiver_id: receiverId,
                                message: messageText
                            })
                        });

                        if (response.ok) {
                            const sentMsg = await response.json();
                            appendMessage(sentMsg, true);
                            scrollToBottom();
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                    }
                });
            });
        </script>
    @endif
@endsection