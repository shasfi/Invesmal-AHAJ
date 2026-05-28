@extends('layouts.dashboard')

@section('title', 'Investor Report — Invesmal')

@section('content')
<div class="dashboard-topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Investor Activity Summary</h1>
        <p class="topbar-subtitle">Your investment portfolio overview</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('reports.export-csv') }}" class="btn-secondary">Export CSV</a>
        <a href="{{ route('reports.export-pdf') }}" class="btn-primary">Export Report</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
    <div class="bento-card"><div class="bento-subtitle">Total Interests</div><div class="bento-title">{{ $stats['total_investments'] }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Pending</div><div class="bento-title">{{ $stats['pending'] }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Approved</div><div class="bento-title" style="color:var(--success);">{{ $stats['approved'] }}</div></div>
    <div class="bento-card"><div class="bento-subtitle">Committed</div><div class="bento-title">${{ number_format($stats['total_committed'] ?? 0) }}</div></div>
</div>

<div class="form-card">
    <h2 class="section-title">Recent Investments</h2>
    @forelse($investments as $inv)
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
            <div>
                <strong>{{ $inv->startup->name }}</strong>
                <span class="stage-badge stage-{{ $inv->status === 'approved' ? 'funded' : ($inv->status === 'pending' ? 'mvp' : 'idea') }}">{{ ucfirst($inv->status) }}</span>
            </div>
            <a href="{{ route('investments.show', $inv) }}" class="action-link">View</a>
        </div>
    @empty
        <p class="hint">No investments yet. <a href="{{ route('startups.discover') }}">Discover startups</a></p>
    @endforelse
</div>
@endsection
