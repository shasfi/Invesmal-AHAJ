@extends('layouts.dashboard')

@php
$roleLabel = match($role) {
    'student_founder' => 'Student Founder',
    'investor' => 'Investor',
    'mentor' => 'Mentor',
    'admin' => 'Administrator',
    default => 'User',
};
@endphp

@section('title', 'Dashboard — Invesmal')

@section('content')

{{-- Top bar --}}
<header class="dashboard-topbar">
    <div class="topbar-left">
        <div>
            <h1 class="topbar-title">Dashboard</h1>
            <p class="topbar-subtitle">Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>
    <div class="topbar-right">
        <span class="role-badge role-{{ $role }}">{{ $roleLabel }}</span>
    </div>
</header>

@include('dashboard.partials.welcome', ['role' => $role, 'roleLabel' => $roleLabel])

{{-- ===================== ADMIN DASHBOARD ===================== --}}
@if($role === 'admin')
<div class="bento-grid admin-bento-grid">
    {{-- Stat Cards --}}
    <div class="bento-item span-3 modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-users"></i><span>Total Users</span></div>
        <div class="stat-value">{{ $totalUsers }}</div>
    </div>
    <div class="bento-item span-3 modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-rocket"></i><span>Total Startups</span></div>
        <div class="stat-value">{{ $totalStartups }}</div>
    </div>
    <div class="bento-item span-3 modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-user-clock"></i><span>Pending</span></div>
        <div class="stat-value">{{ $pendingVerifications }}</div>
    </div>
    <div class="bento-item span-3 modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-graduation-cap"></i><span>Founders</span></div>
        <div class="stat-value">{{ $foundersCount }}</div>
    </div>

    {{-- Role Distribution --}}
    <div class="bento-item span-8 modern-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-chart-pie icon"></i> Role Distribution</h3>
        </div>
        <div class="card-body">
            <div class="role-distribution">
                <div class="role-bar-row">
                    <span class="role-bar-label">Founders</span>
                    <div class="role-bar-track"><div class="role-bar-fill role-fill-founder" style="width: {{ $totalUsers > 0 ? round(($foundersCount / $totalUsers) * 100) : 0 }}%"></div></div>
                    <span class="role-bar-count">{{ $foundersCount }}</span>
                </div>
                <div class="role-bar-row">
                    <span class="role-bar-label">Investors</span>
                    <div class="role-bar-track"><div class="role-bar-fill role-fill-investor" style="width: {{ $totalUsers > 0 ? round(($investorsCount / $totalUsers) * 100) : 0 }}%"></div></div>
                    <span class="role-bar-count">{{ $investorsCount }}</span>
                </div>
                <div class="role-bar-row">
                    <span class="role-bar-label">Mentors</span>
                    <div class="role-bar-track"><div class="role-bar-fill role-fill-mentor" style="width: {{ $totalUsers > 0 ? round(($mentorsCount / $totalUsers) * 100) : 0 }}%"></div></div>
                    <span class="role-bar-count">{{ $mentorsCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="bento-item span-4 modern-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left icon"></i> Recent Users</h3>
        </div>
        <div class="card-body">
            <div class="activity-list">
                @forelse($recentUsers as $u)
                <div class="activity-item">
                    <img src="{{ $u->avatar_url }}" alt="" class="activity-avatar">
                    <div class="activity-content">
                        <span class="activity-title">{{ $u->name }}</span>
                        <span class="activity-meta">{{ $u->email }}</span>
                    </div>
                    <span class="role-badge role-{{ $u->role }}">{{ ucfirst(str_replace('_', ' ', $u->role)) }}</span>
                </div>
                @empty
                <p class="text-muted">No recent users.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Startups Table --}}
    <div class="bento-item span-12 modern-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-rocket icon"></i> Recent Startups</h3>
            <a href="{{ route('startups.index') }}" class="panel-link">View all <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Startup</th>
                            <th>Founder</th>
                            <th>Industry</th>
                            <th>Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStartups as $s)
                        <tr>
                            <td>
                                <div class="table-user">
                                    <img src="{{ $s->logo_url }}" alt="" class="table-avatar">
                                    <div class="table-name">{{ $s->name }}</div>
                                </div>
                            </td>
                            <td>{{ $s->founder->name ?? '—' }}</td>
                            <td>{{ $s->industry ?? '—' }}</td>
                            <td><span class="stage-badge stage-{{ $s->stage }}">{{ ucfirst($s->stage) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem;">No startups yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===================== STUDENT FOUNDER DASHBOARD ===================== --}}
@elseif($role === 'student_founder')
<div class="bento-grid" style="grid-template-columns: 1fr 320px; align-items: flex-start;">
    {{-- Main Column --}}
    <div class="bento-item" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @if($myStartupsCount === 0)
            <div class="modern-card" style="background: var(--surface-strong);">
                <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; gap: 2rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text);">You haven't created a startup yet</h3>
                        <p style="color: var(--muted); margin-top: 0.5rem;">Launch your first venture to connect with investors and mentors.</p>
                    </div>
                    <a href="{{ route('startups.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Create Startup</a>
                </div>
            </div>
        @endif

        @if($myStartupsCount > 0)
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-rocket icon"></i> My Startups</h3>
                    <a href="{{ route('startups.create') }}" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"><i class="fa-solid fa-plus"></i> New</a>
                </div>
                <div class="startup-card-grid card-body">
                    @foreach($myStartups as $startup)
                    <div class="modern-card startup-card">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <img src="{{ $startup->logo_url }}" alt="{{ $startup->name }} logo" style="width: 48px; height: 48px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                                <span class="stage-badge stage-{{ $startup->stage }}">{{ ucfirst(str_replace('_', ' ', $startup->stage)) }}</span>
                            </div>
                            <h4 class="startup-card-name">{{ $startup->name }}</h4>
                            <p class="startup-card-desc">{{ Str::limit($startup->description, 90) }}</p>
                        </div>
                        <div class="startup-card-footer">
                            <span style="color: var(--muted);"><i class="fa-solid fa-tag"></i> {{ $startup->industry ?? '—' }}</span>
                            <a href="{{ route('startups.edit', $startup) }}" class="startup-card-manage">Manage <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($pendingInvestmentOffers ?? collect())->isNotEmpty())
        <div class="modern-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-hand-holding-dollar icon"></i> Pending investment offers</h3>
                <a href="{{ route('investments.index') }}" class="panel-link">Review all <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
                @foreach($pendingInvestmentOffers as $offer)
                <a href="{{ route('investments.show', $offer) }}" style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 1rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);text-decoration:none;color:inherit;">
                    <div>
                        <strong>{{ $offer->investor?->name }}</strong>
                        <span style="display:block;font-size:0.8rem;color:var(--muted);">{{ $offer->startup?->name }} · ${{ number_format($offer->amount) }}</span>
                    </div>
                    <span class="stage-badge stage-mvp">Review</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar Column --}}
    <div class="bento-item" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="modern-card">
            <div class="card-header"><h3 class="card-title"><i class="fa-solid fa-chart-simple icon"></i> At a Glance</h3></div>
            <div class="card-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $myStartupsCount }}</div><div class="stat-label">My Startups</div></div>
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $ideaCount }}</div><div class="stat-label">Idea Stage</div></div>
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $fundedCount }}</div><div class="stat-label">Funded</div></div>
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $avgProgress }}%</div><div class="stat-label">Avg. Progress</div></div>
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $pitchDeckCount ?? 0 }}</div><div class="stat-label">Pitch Decks</div></div>
                <div class="stat-card" style="padding: 0;"><div class="stat-value">{{ $analyzedDecks ?? 0 }}</div><div class="stat-label">AI Analyzed</div></div>
            </div>
        </div>

        <div class="modern-card">
            <div class="card-header"><h3 class="card-title"><i class="fa-solid fa-lightbulb icon"></i> Founder Tips</h3></div>
            <div class="card-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-avatar" style="background: var(--surface-strong); display: grid; place-items: center;"><i class="fa-solid fa-file-arrow-up"></i></div>
                        <div class="activity-content"><span class="activity-title">Upload your pitch deck</span></div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-avatar" style="background: var(--surface-strong); display: grid; place-items: center;"><i class="fa-solid fa-user-group"></i></div>
                        <div class="activity-content"><span class="activity-title">Build your team profile</span></div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-avatar" style="background: var(--surface-strong); display: grid; place-items: center;"><i class="fa-solid fa-globe"></i></div>
                        <div class="activity-content"><span class="activity-title">Link your website</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== INVESTOR DASHBOARD ===================== --}}
@elseif($role === 'investor')
@include('dashboard.partials.investor')

{{-- ===================== MENTOR DASHBOARD ===================== --}}
@elseif($role === 'mentor')
<div class="bento-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="bento-item modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-rocket"></i><span>Total Startups</span></div>
        <div class="stat-value">{{ $totalStartups }}</div>
    </div>
    <div class="bento-item modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-graduation-cap"></i><span>Founders</span></div>
        <div class="stat-value">{{ $totalFounders }}</div>
    </div>
    <div class="bento-item modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-lightbulb"></i><span>Idea Stage</span></div>
        <div class="stat-value">{{ $ideaStartups }}</div>
    </div>
    <div class="bento-item modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-hammer"></i><span>MVP Stage</span></div>
        <div class="stat-value">{{ $mvpStartups }}</div>
    </div>
    <div class="bento-item modern-card stat-card">
        <div class="stat-header"><i class="fa-solid fa-calendar-check"></i><span>Meetings</span></div>
        <div class="stat-value">{{ $upcomingMeetings ?? 0 }}</div>
    </div>
</div>

<div class="modern-card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-layer-group icon"></i> Startups by Category</h3>
        <a href="{{ route('startups.discover') }}" class="panel-link">View all <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="card-body">
        @include('startups.partials.by-industry', ['startupsByIndustry' => $startupsByIndustry ?? []])
    </div>
</div>
@endif

@endsection