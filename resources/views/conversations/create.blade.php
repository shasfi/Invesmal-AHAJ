@extends('layouts.dashboard')

@section('title', 'New Message — Invesmal')

@section('content')
<div class="dashboard-topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">New Message</h1>
        <p class="topbar-subtitle">Start a conversation with someone on the platform</p>
    </div>
    <a href="{{ route('conversations.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="form-card" style="max-width: 560px;">
    <form method="POST" action="{{ route('conversations.store') }}">
        @csrf

        <div class="auth-form-group">
            <label for="participant_id">Message <span class="required">*</span></label>
            <select id="participant_id" name="participant_id" class="form-select" required>
                <option value="">Choose recipient...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('participant_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} — {{ ucwords(str_replace('_', ' ', $user->role)) }}
                        @if($user->university) ({{ $user->university }}) @endif
                    </option>
                @endforeach
            </select>
            @error('participant_id') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="auth-form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-input" value="{{ old('subject') }}"
                   placeholder="e.g. Partnership discussion" maxlength="255">
        </div>

        <p style="font-size:0.85rem;color:var(--muted);margin:0 0 1rem;">After opening the chat, type and send each message separately (Enter to send).</p>

        <div class="form-actions">
            <a href="{{ route('conversations.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary"><i class="fa-solid fa-comments"></i> Open Chat</button>
        </div>
    </form>
</div>
@endsection
