@extends('layouts.dashboard')

@section('title', 'Schedule Meeting â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/meetings/meetings.css',
    ]])
@endpush

@section('content')
<div class="dashboard-topbar">
    <div class="topbar-left">
        <a href="{{ route('meetings.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Meetings
        </a>
    </div>
</div>

<div class="form-card">
    <h1 class="form-title">Schedule a Meeting</h1>
    <p class="form-subtitle">Set up a time to connect with a founder, investor, or mentor.</p>

    <form action="{{ route('meetings.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="invitee_id">Invite <span class="required">*</span></label>
            <select name="invitee_id" id="invitee_id" required class="form-select">
                <option value="">Select a person...</option>
                @forelse($users as $role => $roleUsers)
                    <optgroup label="{{ ucfirst(str_replace('_', ' ', $role)) }}s">
                        @foreach($roleUsers as $user)
                            <option value="{{ $user->id }}"
                                {{ (old('invitee_id') == $user->id || (isset($invitee) && $invitee->id == $user->id)) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                            </option>
                        @endforeach
                    </optgroup>
                @empty
                    <option value="" disabled>No users available</option>
                @endforelse
            </select>
            @error('invitee_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="title">Meeting Title <span class="required">*</span></label>
            <input type="text" name="title" id="title" required maxlength="200"
                   class="form-input" placeholder="e.g. Pitch Deck Review"
                   value="{{ old('title', isset($startup) ? 'Meeting: ' . $startup->name : '') }}">
            @error('title')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="scheduled_at">Date & Time <span class="required">*</span></label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                       class="form-input" value="{{ old('scheduled_at') }}">
                @error('scheduled_at')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="location">Location / Link</label>
                <input type="text" name="location" id="location" maxlength="300"
                       class="form-input" placeholder="Zoom, Google Meet, or address"
                       value="{{ old('location') }}">
                @error('location')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Agenda / Notes</label>
            <textarea name="notes" id="notes" rows="3" maxlength="2000"
                      class="form-textarea" placeholder="What would you like to discuss?">{{ old('notes') }}</textarea>
            @error('notes')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        @if($startupId)
            <input type="hidden" name="startup_id" value="{{ $startupId }}">
        @endif

        <button type="submit" class="btn-primary full-width">
            <i class="fa-solid fa-paper-plane"></i> Send Meeting Request
        </button>
    </form>
</div>
@endsection