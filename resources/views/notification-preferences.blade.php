@extends('layouts.dashboard')

@section('title', 'Notification Preferences')

@section('content')
@php
$preferences = $preferences ?? auth()->user()->notificationPreference ?? null;
@endphp

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:700;">Notification Preferences</h1>
    <p style="color:var(--muted);font-size:0.85rem;">Manage how you receive notifications</p>
</div>

<form method="POST" action="{{ route('notification.preferences.update') }}">
@csrf
@method('PUT')

<div style="display:grid;gap:1.5rem;">
    {{-- Email Notifications --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;"><i class="fa-solid fa-envelope" style="color:var(--primary);margin-right:0.5rem;"></i> Email Notifications</h3>
        @foreach([
            'email_investment_updates' => 'Investment Updates',
            'email_meeting_updates' => 'Meeting Updates',
            'email_message_notifications' => 'Message Notifications',
            'email_verification_updates' => 'Verification Updates',
        ] as $key => $label)
        <label style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0;cursor:pointer;border-bottom:1px solid var(--border);">
            <input type="hidden" name="{{ $key }}" value="0">
            <input type="checkbox" name="{{ $key }}" value="1" {{ ($preferences?->$key ?? true) ? 'checked' : '' }} style="width:1rem;height:1rem;accent-color:var(--primary);">
            <span style="font-size:0.9rem;font-weight:500;">{{ $label }}</span>
        </label>
        @endforeach
    </div>

    {{-- In-App Notifications --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;"><i class="fa-solid fa-bell" style="color:var(--accent-soft);margin-right:0.5rem;"></i> In-App Notifications</h3>
        @foreach([
            'in_app_investment_updates' => 'Investment Updates',
            'in_app_meeting_updates' => 'Meeting Updates',
            'in_app_message_notifications' => 'Message Notifications',
            'in_app_verification_updates' => 'Verification Updates',
        ] as $key => $label)
        <label style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0;cursor:pointer;border-bottom:1px solid var(--border);">
            <input type="hidden" name="{{ $key }}" value="0">
            <input type="checkbox" name="{{ $key }}" value="1" {{ ($preferences?->$key ?? true) ? 'checked' : '' }} style="width:1rem;height:1rem;accent-color:var(--primary);">
            <span style="font-size:0.9rem;font-weight:500;">{{ $label }}</span>
        </label>
        @endforeach
    </div>
</div>

<div style="margin-top:1.5rem;">
    <button type="submit" style="padding:0.65rem 1.5rem;background:var(--primary);color:white;border:none;border-radius:var(--radius-md);font-size:0.9rem;font-weight:600;cursor:pointer;font-family:var(--font-sans);">
        <i class="fa-solid fa-floppy-disk"></i> Save Preferences
    </button>
</div>
</form>
@endsection