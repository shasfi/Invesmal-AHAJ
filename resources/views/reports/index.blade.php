@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')

@section('content')
@php $s = $stats ?? []; @endphp

<div style="margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;"><i class="fa-solid fa-chart-bar" style="color:var(--primary);margin-right:0.5rem;"></i> Reports & Analytics</h1>
        <p style="color:var(--muted);font-size:0.85rem;">Platform statistics and data insights</p>
    </div>
    <div style="display:flex;gap:0.5rem;">
        <a href="{{ route('reports.export-csv') }}" style="padding:0.5rem 1rem;background:var(--success);color:white;border-radius:var(--radius-md);text-decoration:none;font-size:0.85rem;font-weight:500;">
            <i class="fa-solid fa-file-csv"></i> Export CSV
        </a>
        <a href="{{ route('reports.export-pdf') }}" style="padding:0.5rem 1rem;background:var(--danger);color:white;border-radius:var(--radius-md);text-decoration:none;font-size:0.85rem;font-weight:500;">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

{{-- Stats Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;">Users</div>
        <div style="font-size:1.5rem;font-weight:700;">{{ $s['total_users'] ?? 0 }}</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;">Startups</div>
        <div style="font-size:1.5rem;font-weight:700;">{{ $s['total_startups'] ?? 0 }}</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;">Investments</div>
        <div style="font-size:1.5rem;font-weight:700;">{{ $s['total_investments'] ?? 0 }}</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;">Meetings</div>
        <div style="font-size:1.5rem;font-weight:700;">{{ $s['total_meetings'] ?? 0 }}</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;">Total Raised</div>
        <div style="font-size:1.5rem;font-weight:700;color:var(--success);">${{ number_format(($s['total_raised'] ?? 0) / 1000000, 1) }}M</div>
    </div>
</div>

{{-- Stage Distribution --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">Startups by Stage</h3>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
        @foreach(($s['by_stage'] ?? []) as $stage => $count)
        <div style="text-align:center;min-width:80px;">
            <div style="font-size:1.5rem;font-weight:700;">{{ $count }}</div>
            <div style="color:var(--muted);font-size:0.75rem;text-transform:capitalize;">{{ $stage }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Investment Status --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">Investment Status</h3>
    <div style="display:flex;gap:1.5rem;">
        @foreach(($s['investments_by_status'] ?? []) as $status => $count)
        <div>
            <span style="font-weight:700;font-size:1.1rem;">{{ $count }}</span>
            <span style="color:var(--muted);font-size:0.8rem;margin-left:0.25rem;text-transform:capitalize;">{{ $status }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Trending Startups --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">Trending Startups</h3>
    @foreach(($trendingStartups ?? []) as $startup)
    <div style="display:flex;align-items:center;gap:1rem;padding:0.5rem 0;border-bottom:1px solid var(--border);font-size:0.85rem;">
        <span style="font-weight:600;flex:1;">{{ $startup->name }}</span>
        <span style="color:var(--muted);">{{ $startup->industry }}</span>
        <span style="font-weight:600;color:var(--success);">{{ $startup->funding_percent }}%</span>
    </div>
    @endforeach
</div>
@endsection