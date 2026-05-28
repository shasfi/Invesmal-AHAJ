@extends('layouts.dashboard')

@section('title', 'Founders I Invested In — Invesmal')

@section('content')

    <header class="dashboard-topbar">
        <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars" style="font-size: 18px;"></i>
            </button>
            <h1 class="topbar-title">Founders I Invested In</h1>
        </div>
        <div class="topbar-right">
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </header>

    @if($founders->count() > 0)
        <div class="bento-row">
            @foreach($founders as $founder)
                <div class="bento-card">
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                        <img src="{{ $founder->avatar_url }}" alt="{{ $founder->name }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                        <div>
                            <h3 class="bento-title">{{ $founder->name }}</h3>
                            <span class="role-badge role-{{ $founder->role }}">
                                {{ ucwords(str_replace('_', ' ', $founder->role)) }}
                            </span>
                        </div>
                    </div>
                    <div class="profile-detail">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $founder->email }}</span>
                    </div>
                    @if($founder->university)
                    <div class="profile-detail">
                        <i class="fa-solid fa-building-columns"></i>
                        <span>{{ $founder->university }}</span>
                    </div>
                    @endif
                    @if($founder->bio)
                    <div class="profile-detail">
                        <i class="fa-solid fa-quote-left"></i>
                        <span>{{ $founder->bio }}</span>
                    </div>
                    @endif
                    @if($founder->startups->count() > 0)
                    <div style="margin-top:1rem;">
                        <h4 style="font-size:0.875rem;font-weight:600;margin-bottom:0.5rem;color:var(--text-secondary);">Startups:</h4>
                        @foreach($founder->startups as $startup)
                            <div style="padding:0.5rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:0.5rem;">
                                <span style="font-weight:600;color:var(--text);">{{ $startup->name }}</span>
                                <span style="display:inline-block;margin-left:0.5rem;padding:0.25rem 0.5rem;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:0.75rem;color:var(--text-muted);">{{ ucfirst($startup->stage) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif
                    <div style="margin-top:1rem;">
                        <a href="{{ route('users.profile', $founder) }}" class="btn-secondary" style="width:100%;text-align:center;">
                            <i class="fa-solid fa-eye"></i> View Full Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-rocket" style="font-size:3rem;margin-bottom:1rem;color:var(--text-muted);"></i>
            <h3>No Investments Yet</h3>
            <p style="color:var(--text-secondary);">Once you invest in startups, the founders will appear here.</p>
        </div>
    @endif

@endsection
