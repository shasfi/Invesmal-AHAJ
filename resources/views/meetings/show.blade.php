@extends('layouts.dashboard')

@section('title', $meeting->title . ' â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/meetings/meetings.css',
    ]])
@endpush

@section('content')
<div class="meeting-detail-page">
    <header class="page-header minimal-header">
        <a href="{{ route('meetings.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Meetings
        </a>
    </header>

    <div class="meeting-detail-layout">
        {{-- Main Detail Card --}}
        <div class="meeting-detail-card glass-card">
            <div class="meeting-detail-header">
                @php
                    $statusColors = ['pending' => 'status-warning', 'accepted' => 'status-success', 'declined' => 'status-danger', 'cancelled' => 'status-muted'];
                    $statusLabels = ['pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined', 'cancelled' => 'Cancelled'];
                @endphp
                <span class="status-badge large {{ $statusColors[$meeting->status] ?? 'status-muted' }}">
                    {{ $statusLabels[$meeting->status] ?? $meeting->status }}
                </span>
            </div>

            <h1 class="meeting-detail-title">{{ $meeting->title }}</h1>

            <div class="meeting-detail-grid">
                <div class="detail-block">
                    <label>Date & Time</label>
                    <p class="detail-value highlight">{{ $meeting->scheduled_at->format('F j, Y') }}</p>
                    <p class="detail-value">{{ $meeting->scheduled_at->format('g:i A') }}</p>
                </div>
                <div class="detail-block">
                    <label>Scheduled By</label>
                    <p class="detail-value">{{ $meeting->scheduler->name }}</p>
                    <span class="detail-role">{{ ucfirst(str_replace('_', ' ', $meeting->scheduler->role)) }}</span>
                </div>
                <div class="detail-block">
                    <label>Invited</label>
                    <p class="detail-value">{{ $meeting->invitee->name }}</p>
                    <span class="detail-role">{{ ucfirst(str_replace('_', ' ', $meeting->invitee->role)) }}</span>
                </div>
                @if($meeting->startup)
                    <div class="detail-block">
                        <label>Related Startup</label>
                        <a href="{{ route('startups.show', $meeting->startup) }}" class="detail-link">{{ $meeting->startup->name }}</a>
                    </div>
                @endif
                @if($meeting->location)
                    <div class="detail-block full-width">
                        <label>Location</label>
                        <p class="detail-value">{{ $meeting->location }}</p>
                    </div>
                @endif
                @if($meeting->notes)
                    <div class="detail-block full-width">
                        <label>Notes</label>
                        <p class="detail-notes">{{ $meeting->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="meeting-actions">
                @if($meeting->status === 'pending')
                    @if(auth()->id() === $meeting->invitee_id)
                        <form action="{{ route('meetings.accept', $meeting) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-success">
                                <i class="fa-solid fa-check"></i> Accept
                            </button>
                        </form>
                    @endif
                    @if(auth()->id() === $meeting->invitee_id || auth()->id() === $meeting->scheduler_id)
                        <form action="{{ route('meetings.decline', $meeting) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-outline">
                                <i class="fa-solid fa-xmark"></i> Decline
                            </button>
                        </form>
                    @endif
                @endif

                @if(in_array($meeting->status, ['pending', 'accepted']))
                    @if(auth()->id() === $meeting->scheduler_id || auth()->id() === $meeting->invitee_id)
                        <form action="{{ route('meetings.cancel', $meeting) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-danger-outline">
                                <i class="fa-solid fa-ban"></i> Cancel
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        {{-- Sidebar â€” Related Info --}}
        <aside class="meeting-sidebar">
            @if($meeting->startup)
                <div class="sidebar-module glass-card">
                    <h4 class="sidebar-module-title">Startup</h4>
                    <a href="{{ route('startups.show', $meeting->startup) }}" class="sidebar-startup-link">
                        <img src="{{ $meeting->startup->logo_url }}" alt="{{ $meeting->startup->name }}" class="sidebar-startup-logo">
                        <div>
                            <p class="sidebar-startup-name">{{ $meeting->startup->name }}</p>
                            <span class="sidebar-startup-stage">{{ ucfirst($meeting->startup->stage) }}</span>
                        </div>
                    </a>
                </div>
            @endif

            <div class="sidebar-module glass-card">
                <h4 class="sidebar-module-title">Quick Actions</h4>
                <a href="{{ route('conversations.store') }}" class="sidebar-action-link" onclick="event.preventDefault(); document.getElementById('start-conversation-form').submit();">
                    <i class="fa-solid fa-comment"></i> Message {{ auth()->id() === $meeting->scheduler_id ? $meeting->invitee->name : $meeting->scheduler->name }}
                </a>
                <form id="start-conversation-form" action="{{ route('conversations.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="participant_id" value="{{ auth()->id() === $meeting->scheduler_id ? $meeting->invitee_id : $meeting->scheduler_id }}">
                </form>
            </div>
        </aside>
    </div>
</div>