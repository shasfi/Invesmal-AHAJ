@extends('layouts.dashboard')

@section('title', 'Founder Report — Invesmal')

@section('content')
<div class="dashboard-topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Startup Performance Report</h1>
        <p class="topbar-subtitle">Funding and investor engagement for your ventures</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('reports.export-csv') }}" class="btn-secondary">Export CSV</a>
        <a href="{{ route('reports.export-pdf') }}" class="btn-primary">Export Report</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
    <div class="bento-card"><div class="bento-subtitle">Startups</div><div class="bento-title">{{ $stats['total_startups'] }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Verified</div><div class="bento-title">{{ $stats['verified'] }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Total Raised</div><div class="bento-title">${{ number_format($stats['total_raised'] ?? 0) }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Pending Requests</div><div class="bento-title">{{ $stats['pending_investments'] }}</div></div>
</div>

<div class="form-card">
    <h2 class="section-title">Your Startups</h2>
    @forelse($startups as $startup)
        <div style="padding:0.75rem 0;border-bottom:1px solid var(--border);">
            <strong>{{ $startup->name }}</strong>
            <span class="stage-badge stage-{{ $startup->stage }}">{{ ucfirst($startup->stage) }}</span>
            <p class="hint" style="margin-top:0.35rem;">{{ $startup->funding_percent }}% funded · {{ $startup->investments->count() }} investment requests</p>
            <a href="{{ route('startups.show', $startup) }}" class="action-link">View profile</a>
        </div>
    @empty
        <p class="hint">No startups yet. <a href="{{ route('startups.create') }}">Create your first startup</a></p>
    @endforelse
</div>
@endsection
