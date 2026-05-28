@extends('layouts.dashboard')

@section('title', 'Messages â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/conversations/conversations.css',
    ]])
@endpush

@section('content')
<div class="conversations-page">
    <div class="conv-header">
        <div>
            <h1><i class="fa-solid fa-comments" style="color:var(--accent-soft);"></i> Messages</h1>
            <p>Chat with investors, founders, and mentors</p>
        </div>
        <a href="{{ route('conversations.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> New Message
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="quick-compose">
        <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:0.75rem;">Quick start conversation</h3>
        <p style="font-size:0.8rem;color:var(--muted);margin:0 0 0.75rem;">Select someone to open chat â€” send messages one by one in the chat screen.</p>
        <form method="POST" action="{{ route('conversations.store') }}">
            @csrf
            <select name="participant_id" class="form-select" required>
                <option value="">Select a person to message...</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ ucwords(str_replace('_', ' ', $u->role)) }})</option>
                @endforeach
            </select>
            <input type="text" name="subject" class="form-input" placeholder="Subject (optional) e.g. Investment discussion">
            <button type="submit" class="btn-primary"><i class="fa-solid fa-comments"></i> Open Chat</button>
        </form>
    </div>

    @if($conversations->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-comments empty-icon"></i>
            <h3>No conversations yet</h3>
            <p>Use <strong>New Message</strong> above or the quick form to start chatting.</p>
        </div>
    @else
        <div class="conv-list">
            @foreach($conversations as $conv)
                @php $other = $conv->participants->firstWhere('id', '!=', auth()->id()); @endphp
                <a href="{{ route('conversations.show', $conv) }}" class="conv-card {{ ($conv->unread_count ?? 0) > 0 ? 'unread' : '' }}">
                    <div class="conv-avatar">{{ strtoupper(substr($other?->name ?? '?', 0, 2)) }}</div>
                    <div class="conv-body">
                        <div class="conv-top">
                            <span class="conv-subject">{{ $conv->subject ?? ($other?->name ?? 'Conversation') }}</span>
                            @if($conv->latestMessage)
                                <span class="conv-time">{{ $conv->latestMessage->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="conv-preview">
                            @if($conv->latestMessage)
                                <strong>{{ $conv->latestMessage->sender->name }}:</strong>
                                {{ \Illuminate\Support\Str::limit($conv->latestMessage->body, 80) }}
                                @if(($conv->unread_count ?? 0) > 0)
                                    <span class="unread-badge">{{ $conv->unread_count }}</span>
                                @endif
                            @else
                                <span class="muted">No messages yet â€” say hello!</span>
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
        @if($conversations->hasPages())
            <div class="pagination-wrap" style="margin-top:1.5rem;">{{ $conversations->links() }}</div>
        @endif
    @endif
</div>
@endsection
