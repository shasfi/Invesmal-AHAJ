@extends('layouts.dashboard')

@section('title', ($conversation->subject ?? 'Chat') . ' â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/conversations/chat.css',
    ]])
@endpush

@section('content')
@php
    $other = $conversation->participants->firstWhere('id', '!=', auth()->id());
    $myId = auth()->id();
@endphp

<div class="chat-page">
    @if(session('success'))
        <div class="alert-success" style="margin-bottom:0.5rem;">{{ session('success') }}</div>
    @endif
    <div class="chat-header-bar">
        <a href="{{ route('conversations.index') }}" class="btn-ghost" title="Back"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="avatar-tiny" style="width:40px;height:40px;font-size:0.85rem;">{{ strtoupper(substr($other?->name ?? '?', 0, 2)) }}</div>
        <div style="flex:1;min-width:0;">
            <h2 class="chat-title">{{ $other?->name ?? 'Conversation' }}</h2>
            <p style="margin:0;font-size:0.75rem;color:var(--muted);">{{ $conversation->subject }}</p>
        </div>
        <a href="{{ route('ai.sentiment.show', $conversation) }}" class="btn-secondary" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Sentiment</a>
    </div>

    <div class="chat-messages" id="chat-messages">
        @forelse($messages as $message)
            @include('conversations.partials.message-bubble', ['message' => $message, 'myId' => $myId])
        @empty
            <div class="empty-chat" id="empty-chat">No messages yet. Say hello below!</div>
        @endforelse
    </div>

    <form class="chat-input-bar" id="chat-form" data-send-url="{{ route('conversations.send-message', $conversation) }}">
        @csrf
        <textarea name="body" rows="1" placeholder="Type a message... (Enter to send)" id="chat-input" required></textarea>
        <button type="submit" id="chat-send-btn" title="Send"><i class="fa-solid fa-paper-plane"></i></button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const messagesEl = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');
    const sendUrl = form.dataset.sendUrl;
    const myId = {{ $myId }};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    let lastMessageId = {{ $messages->max('id') ?? 0 }};

    function scrollBottom() {
        if (messagesEl) messagesEl.scrollTop = messagesEl.scrollHeight;
    }
    scrollBottom();

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function appendMessage(msg) {
        if (messagesEl.querySelector(`[data-message-id="${msg.id}"]`)) {
            return;
        }

        const empty = document.getElementById('empty-chat');
        if (empty) empty.remove();

        const isMine = msg.sender_id === myId;
        const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const initials = (msg.sender?.name || '?').substring(0, 2).toUpperCase();

        const row = document.createElement('div');
        row.className = 'message-row ' + (isMine ? 'sent' : 'received');
        row.dataset.messageId = msg.id;
        row.innerHTML = isMine
            ? `<div class="message-glass message-sent"><p class="message-body">${escapeHtml(msg.body)}</p><span class="message-time">${time}</span></div>`
            : `<div class="avatar-tiny">${initials}</div><div class="message-glass message-received"><p class="message-body">${escapeHtml(msg.body)}</p><span class="message-time">${time}</span></div>`;

        messagesEl.appendChild(row);
        lastMessageId = Math.max(lastMessageId, msg.id);
        scrollBottom();
    }

    if (input) {
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const body = input.value.trim();
        if (!body) return;

        sendBtn.disabled = true;
        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ body }),
            });

            if (!res.ok) throw new Error('Send failed');

            const data = await res.json();
            appendMessage(data.message);
            input.value = '';
            input.style.height = 'auto';
        } catch (err) {
            alert('Could not send message. Please try again.');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    });

    setInterval(async function() {
        try {
            const res = await fetch(`{{ route('conversations.show', $conversation) }}?after=${lastMessageId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.messages?.length) {
                data.messages.sort((a, b) => a.id - b.id).forEach(appendMessage);
            }
        } catch (e) {}
    }, 4000);
})();
</script>
@endpush
