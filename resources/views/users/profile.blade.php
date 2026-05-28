@extends('layouts.dashboard')

@section('title', $user->name . ' — Profile — Invesmal')

@section('content')

    <header class="dashboard-topbar">
        <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars" style="font-size: 18px;"></i>
            </button>
            <h1 class="topbar-title">User Profile</h1>
        </div>
        <div class="topbar-right">
            @if(auth()->id() === $user->id || auth()->user()->role === 'admin')
                <a href="{{ route('users.edit', $user) }}" class="btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
            @elseif(auth()->user()->role === 'investor' && $user->role === 'student_founder')
                <span style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 0.75rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);font-size:0.875rem;color:var(--text-muted);">
                    <i class="fa-solid fa-lock"></i> Read-only view
                </span>
            @endif
            <a href="{{ auth()->user()->role === 'admin' ? route('users.index') : route('dashboard') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </header>

    <div class="profile-card">
        <div class="profile-header">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="profile-avatar">
            <div class="profile-info">
                <h2 class="profile-name">{{ $user->name }}</h2>
                <span class="role-badge role-{{ $user->role }}">
                    {{ ucwords(str_replace('_', ' ', $user->role)) }}
                </span>
                @if($user->hasVerifiedEmail())
                    <span class="verified-badge"><i class="fa-solid fa-envelope-circle-check"></i> Email verified</span>
                @else
                    <span class="unverified-badge"><i class="fa-solid fa-envelope"></i> Email not verified</span>
                @endif
                @if($user->is_verified)
                    <span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Account verified</span>
                @endif
            </div>
        </div>
        <div class="profile-body">
            <div class="profile-detail">
                <i class="fa-solid fa-envelope"></i>
                <span>{{ $user->email }}</span>
            </div>
            @if($user->university)
            <div class="profile-detail">
                <i class="fa-solid fa-building-columns"></i>
                <span>{{ $user->university }}</span>
            </div>
            @endif
            @if($user->bio)
            <div class="profile-detail">
                <i class="fa-solid fa-quote-left"></i>
                <span>{{ $user->bio }}</span>
            </div>
            @endif
            <div class="profile-detail text-muted">
                <i class="fa-solid fa-calendar"></i>
                <span>Joined {{ $user->created_at->format('F j, Y') }}</span>
            </div>
        </div>
    </div>

    @if($user->startups->count() > 0)
    <div class="profile-section">
        <h3 class="section-title">Startups</h3>
        <div class="bento-row">
            @foreach($user->startups as $startup)
            <div class="bento-card">
                <h4 class="bento-title">{{ $startup->name }}</h4>
                <p class="bento-subtitle">{{ $startup->description }}</p>
                <span class="stage-badge stage-{{ $startup->stage }}">{{ ucfirst($startup->stage) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

@endsection