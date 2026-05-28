@extends('layouts.dashboard')

@php
$statusColors = [
    'pending' => 'status-warning',
    'accepted' => 'status-success',
    'declined' => 'status-danger',
    'cancelled' => 'status-muted',
];
$statusLabels = [
    'pending' => 'Pending',
    'accepted' => 'Accepted',
    'declined' => 'Declined',
    'cancelled' => 'Cancelled',
];
@endphp

@section('title', 'Meetings - Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/meetings/meetings.css',
    ]])
@endpush

@section('content')

<div class="dashboard-topbar">
    <div class="topbar-left">
        <div>
            <h1 class="topbar-title">Meetings</h1>
            <p class="topbar-subtitle">Schedule & manage your sessions</p>
        </div>
    </div>
    <div class="topbar-right">
        <a href="{{ route('meetings.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> New Meeting
        </a>
    </div>
</div>

<div class="meeting-tabs" role="tablist">
    <button class="meeting-tab active" data-tab="upcoming" role="tab" aria-selected="true">Upcoming</button>
    <button class="meeting-tab" data-tab="past" role="tab">Past</button>
    <button class="meeting-tab" data-tab="all" role="tab">All</button>
</div>

{{-- Upcoming --}}
<div class="meeting-tab-panel active" id="tab-upcoming">
    @if($upcoming->isEmpty())
        <div class="empty-cell">No upcoming meetings.</div>
    @else
        <h3 class="meeting-section-title"><i class="fa-solid fa-calendar-check"></i> Upcoming</h3>
        <div class="meeting-grid">
            @foreach($upcoming as $meeting)
                @php
                    $isScheduler = auth()->id() === $meeting->scheduler_id;
                    $otherUser = $isScheduler ? $meeting->invitee : $meeting->scheduler;
                @endphp
                <a href="{{ route('meetings.show', $meeting) }}" class="meeting-card glass-card">
                    <div class="meeting-card-header">
                        <span class="meeting-status-badge {{ $statusColors[$meeting->status] ?? 'status-muted' }}">
                            {{ $statusLabels[$meeting->status] ?? $meeting->status }}
                        </span>
                        <span class="meeting-date">{{ $meeting->scheduled_at->format('M j, Y') }}</span>
                    </div>
                    <h3 class="meeting-card-title">{{ $meeting->title }}</h3>
                    <div class="meeting-card-meta">
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>{{ $meeting->scheduled_at->format('g:i A') }}</span>
                        </div>
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-user"></i>
                            <span>{{ $otherUser?->name ?? 'Unknown' }}</span>
                        </div>
                        @if($meeting->location)
                            <div class="meeting-meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>{{ \Illuminate\Support\Str::limit($meeting->location, 40) }}</span>
                            </div>
                        @endif
                        @if($meeting->startup)
                            <div class="meeting-meta-item">
                                <i class="fa-solid fa-rocket"></i>
                                <span>{{ $meeting->startup->name }}</span>
                            </div>
                        @endif
                    </div>
                    @if($meeting->status === 'pending' && !$isScheduler)
                        <div class="meeting-card-actions">
                            <span class="action-hint">Action needed â€” tap to respond</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Past --}}
<div class="meeting-tab-panel" id="tab-past">
    @if($past->isEmpty())
        <div class="empty-cell">No past meetings.</div>
    @else
        <h3 class="meeting-section-title"><i class="fa-solid fa-clock-rotate-left"></i> Past</h3>
        <div class="meeting-grid">
            @foreach($past as $meeting)
                @php
                    $isScheduler = auth()->id() === $meeting->scheduler_id;
                    $otherUser = $isScheduler ? $meeting->invitee : $meeting->scheduler;
                @endphp
                <a href="{{ route('meetings.show', $meeting) }}" class="meeting-card glass-card">
                    <div class="meeting-card-header">
                        <span class="meeting-status-badge {{ $statusColors[$meeting->status] ?? 'status-muted' }}">
                            {{ $statusLabels[$meeting->status] ?? $meeting->status }}
                        </span>
                        <span class="meeting-date">{{ $meeting->scheduled_at->format('M j, Y') }}</span>
                    </div>
                    <h3 class="meeting-card-title">{{ $meeting->title }}</h3>
                    <div class="meeting-card-meta">
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>{{ $meeting->scheduled_at->format('g:i A') }}</span>
                        </div>
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-user"></i>
                            <span>{{ $otherUser?->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- All --}}
<div class="meeting-tab-panel" id="tab-all">
    @if($meetings->isEmpty())
        <div class="empty-cell">No meetings.</div>
    @else
        <h3 class="meeting-section-title"><i class="fa-solid fa-list"></i> All Meetings</h3>
        <div class="meeting-grid">
            @foreach($meetings as $meeting)
                @php
                    $isScheduler = auth()->id() === $meeting->scheduler_id;
                    $otherUser = $isScheduler ? $meeting->invitee : $meeting->scheduler;
                @endphp
                <a href="{{ route('meetings.show', $meeting) }}" class="meeting-card glass-card">
                    <div class="meeting-card-header">
                        <span class="meeting-status-badge {{ $statusColors[$meeting->status] ?? 'status-muted' }}">
                            {{ $statusLabels[$meeting->status] ?? $meeting->status }}
                        </span>
                        <span class="meeting-date">{{ $meeting->scheduled_at->format('M j, Y') }}</span>
                    </div>
                    <h3 class="meeting-card-title">{{ $meeting->title }}</h3>
                    <div class="meeting-card-meta">
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>{{ $meeting->scheduled_at->format('g:i A') }}</span>
                        </div>
                        <div class="meeting-meta-item">
                            <i class="fa-solid fa-user"></i>
                            <span>{{ $otherUser?->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.meeting-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.meeting-tab').forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        document.querySelectorAll('.meeting-tab-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});
</script>
@endpush