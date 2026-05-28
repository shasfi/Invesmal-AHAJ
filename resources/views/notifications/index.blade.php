@extends('layouts.dashboard')

@section('title', 'Notifications — Invesmal')

@section('content')
    <header class="dashboard-topbar">
        <div class="topbar-left">
            <button class="hamburger-btn" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 class="topbar-title">Notifications</h1>
                <p class="topbar-subtitle">Stay updated on your investments and startups</p>
            </div>
        </div>
        <div class="topbar-right">
            @if($notifications->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit" class="btn-primary" style="font-size: 0.8rem; padding: 0.45rem 1rem;">
                        <i class="fa-solid fa-check-double"></i> Mark All Read
                    </button>
                </form>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($notifications->isEmpty())
        <div class="modern-card">
            <div class="card-body" style="text-align: center; padding: 3rem 1.5rem;">
                <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; color: var(--muted); margin-bottom: 1rem; display: block;"></i>
                <p style="color: var(--muted); font-size: 1rem;">No notifications yet.</p>
            </div>
        </div>
    @else
        <div class="activity-list" style="gap: 0.5rem;">
            @foreach($notifications as $notification)
                <div class="modern-card" style="opacity: {{ $notification->read_at ? '0.6' : '1' }};">
                    <div class="card-body" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.25rem;">
                        <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: {{ $notification->type === 'success' ? 'rgba(92,143,106,0.2)' : ($notification->type === 'warning' ? 'rgba(200,155,93,0.2)' : 'rgba(127,163,154,0.2)') }}; color: {{ $notification->type === 'success' ? 'var(--success)' : ($notification->type === 'warning' ? 'var(--warning)' : 'var(--accent-soft)') }};">
                            <i class="fa-solid fa-{{ $notification->type === 'success' ? 'circle-check' : ($notification->type === 'warning' ? 'triangle-exclamation' : 'bell') }}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                <span style="font-weight: 600; color: var(--text);">{{ $notification->title }}</span>
                                <span style="font-size: 0.75rem; color: var(--muted); white-space: nowrap;">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            @if($notification->body)
                                <p style="font-size: 0.85rem; color: var(--muted); margin-top: 0.25rem;">{{ $notification->body }}</p>
                            @endif
                            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" style="font-size: 0.8rem; font-weight: 600; color: var(--accent-warm);">View Details</a>
                                @endif
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; font-size: 0.8rem; font-weight: 600; color: var(--muted); cursor: pointer; font-family: var(--font-sans);">Mark Read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 1rem;">{{ $notifications->links() }}</div>
    @endif
@endsection