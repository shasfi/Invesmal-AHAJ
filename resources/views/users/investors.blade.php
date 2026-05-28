@extends('layouts.dashboard')

@section('title', 'My Investors — Invesmal')

@section('content')

    <header class="dashboard-topbar">
        <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars" style="font-size: 18px;"></i>
            </button>
            <h1 class="topbar-title">My Investors</h1>
        </div>
        <div class="topbar-right">
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </header>

    @if($investors->count() > 0)
        <div class="bento-row">
            @foreach($investors as $investor)
                <div class="bento-card">
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                        <img src="{{ $investor->avatar_url }}" alt="{{ $investor->name }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                        <div>
                            <h3 class="bento-title">{{ $investor->name }}</h3>
                            <span class="role-badge role-{{ $investor->role }}">
                                {{ ucwords(str_replace('_', ' ', $investor->role)) }}
                            </span>
                        </div>
                    </div>
                    <div class="profile-detail">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $investor->email }}</span>
                    </div>
                    @if($investor->university)
                    <div class="profile-detail">
                        <i class="fa-solid fa-building-columns"></i>
                        <span>{{ $investor->university }}</span>
                    </div>
                    @endif
                    @if($investor->bio)
                    <div class="profile-detail">
                        <i class="fa-solid fa-quote-left"></i>
                        <span>{{ $investor->bio }}</span>
                    </div>
                    @endif
                    <div style="margin-top:1rem;">
                        <a href="{{ route('users.profile', $investor) }}" class="btn-secondary" style="width:100%;text-align:center;">
                            <i class="fa-solid fa-eye"></i> View Full Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-handshake" style="font-size:3rem;margin-bottom:1rem;color:var(--text-muted);"></i>
            <h3>No Investors Yet</h3>
            <p style="color:var(--text-secondary);">Once investors invest in your startups, they will appear here.</p>
        </div>
    @endif

@endsection
