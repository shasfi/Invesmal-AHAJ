@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/components/pitch-buttons.css',
    ]])
@endpush

@section('title', 'AI Insights')

@section('content')
@php
$trending = $trending ?? [];
$recommendations = $recommendations ?? [];
$stats = $stats ?? [];
@endphp

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:700;"><i class="fa-solid fa-chart-line" style="color:var(--primary);margin-right:0.5rem;"></i> AI Analytics & Insights</h1>
    <p style="color:var(--muted);font-size:0.85rem;">Data-driven intelligence powered by AI</p>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:0.5rem;">Total Analyzed</div>
        <div style="font-size:1.5rem;font-weight:700;">{{ $stats['total_analyzed'] ?? 0 }}</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:0.5rem;">Avg Readiness Score</div>
        <div style="font-size:1.5rem;font-weight:700;color:var(--success);">{{ $stats['avg_score'] ?? 0 }}%</div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <div style="color:var(--muted);font-size:0.75rem;margin-bottom:0.5rem;">Top Industry</div>
        <div style="font-size:1.5rem;font-weight:700;color:var(--accent-soft);">{{ $stats['top_industry'] ?? 'N/A' }}</div>
    </div>
</div>

{{-- Trending Startups Ranking --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">🏆 Engagement-Based Ranking</h3>
    @if(count($trending))
        @foreach($trending as $index => $startup)
        <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem 0;border-bottom:1px solid var(--border);">
            <span style="font-size:1.25rem;font-weight:700;min-width:30px;color:var(--primary);">#{{ $index + 1 }}</span>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:0.9rem;">{{ $startup->name }}</div>
                <div style="color:var(--muted);font-size:0.75rem;">{{ $startup->industry }} · {{ $startup->investor_count ?? 0 }} investors</div>
            </div>
            <span style="font-weight:700;font-size:0.9rem;color:var(--success);">{{ $startup->funding_percent }}% funded</span>
        </div>
        @endforeach
    @else
        <p style="color:var(--muted);font-size:0.85rem;">No trending data available.</p>
    @endif
</div>

{{-- Competitive Comparison --}}
@if(!empty($comparisons))
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;"><i class="fa-solid fa-scale-balanced"></i> Competitive Comparison</h3>
    @foreach($comparisons as $comp)
        <div style="padding:0.75rem 0;border-bottom:1px solid var(--border);">
            <div style="font-weight:600;color:var(--accent-soft);margin-bottom:0.5rem;">{{ $comp['industry'] }}</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:0.85rem;">
                <div>
                    <span style="color:var(--muted);">Leader</span>
                    <div style="font-weight:600;">{{ $comp['leader']->name }}</div>
                    <div style="color:var(--muted);">{{ $comp['leader']->formatted_amount_raised ?? '$0' }} raised</div>
                </div>
                <div>
                    <span style="color:var(--muted);">Challenger</span>
                    <div style="font-weight:600;">{{ $comp['challenger']->name }}</div>
                    <div style="color:var(--muted);">{{ $comp['challenger']->formatted_amount_raised ?? '$0' }} raised</div>
                </div>
            </div>
            <p style="color:var(--muted);font-size:0.75rem;margin-top:0.5rem;">{{ $comp['metric'] }}</p>
        </div>
    @endforeach
</div>
@endif

{{-- Industry Trends --}}
@if(isset($byIndustry) && count($byIndustry))
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">📈 Domain Trend Analysis</h3>
    @foreach($byIndustry as $row)
        <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--border);font-size:0.85rem;">
            <span>{{ $row->industry }}</span>
            <span>{{ $row->total }} startups · avg ${{ number_format($row->avg_raised ?? 0) }}</span>
        </div>
    @endforeach
</div>
@endif

{{-- AI Recommendations --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;"><i class="fa-solid fa-robot" style="color:var(--accent-soft);margin-right:0.5rem;"></i> AI Recommendations</h3>
    @if(count($recommendations))
        @foreach($recommendations as $rec)
        <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid var(--border);">
            <div style="background:var(--accent-soft);color:white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;">{{ $loop->iteration }}</div>
            <div>
                <div style="font-weight:600;font-size:0.85rem;">{{ $rec['title'] ?? '' }}</div>
                <div style="color:var(--muted);font-size:0.8rem;">{{ $rec['description'] ?? '' }}</div>
            </div>
        </div>
        @endforeach
    @else
        <p style="color:var(--muted);font-size:0.85rem;">No recommendations generated yet. Upload pitch decks to receive AI insights.</p>
    @endif
</div>
@endsection