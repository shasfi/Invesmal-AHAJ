@php
    $isMine = $message->sender_id === $myId;
@endphp
<div class="message-row {{ $isMine ? 'sent' : 'received' }}" data-message-id="{{ $message->id }}">
    @if(!$isMine)
        <div class="avatar-tiny">{{ strtoupper(substr($message->sender->name ?? '?', 0, 2)) }}</div>
    @endif
    <div class="message-glass {{ $isMine ? 'message-sent' : 'message-received' }}">
        <p class="message-body">{{ $message->body }}</p>
        <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
    </div>
</div>
