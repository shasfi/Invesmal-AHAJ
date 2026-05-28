@extends('layouts.dashboard')

@section('title', $deck->title . ' â€” AI Analysis')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-analysis">
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif
    @if(empty($analysis))
        <div class="alert-error" style="margin-bottom:1.5rem;">
            Analysis is not ready yet. @if($deck->status !== 'analyzed')
                <a href="{{ route('pitch_decks.analyze', $deck) }}">Run analysis now</a>
            @else
                Refresh the page in a moment or check your OpenAI API key in .env.
            @endif
        </div>
    @endif
    <div class="pd-analysis__topbar">
        <a href="{{ route('pitch_decks.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> Back to decks</a>
        <div class="pd-analysis__topbar-actions">
            <a href="{{ route('pitch_decks.edit', $deck) }}" class="pp-btn pp-btn--sm pp-btn--primary">Edit Deck</a>
        </div>
    </div>

    <div class="pd-analysis__hero">
        <div class="pd-analysis__score-hero">
            <div class="pd-score-circle" style="--score: {{ $analysis['overall_score'] ?? 0 }}%; --score-color: {{ ($analysis['overall_score'] ?? 0) >= 70 ? 'var(--success)' : (($analysis['overall_score'] ?? 0) >= 40 ? 'var(--warning)' : 'var(--danger)') }}">
                <span class="pd-score-number">{{ $analysis['overall_score'] ?? '?' }}</span>
                <span class="pd-score-label">Pitch Score</span>
            </div>
            <div class="pd-readiness-badge pd-readiness--{{ strtolower(str_replace(' ', '-', $analysis['investor_readiness'] ?? 'not-rated')) }}">
                {{ $analysis['investor_readiness'] ?? 'Not Rated' }}
            </div>
        </div>
        <div class="pd-analysis__verdict">
            <h1>{{ $deck->title }}</h1>
            <p class="pd-verdict-text">{{ $analysis['verdict'] ?? 'No verdict available.' }}</p>
        </div>
    </div>

    <div class="pd-bento-grid">
        <!-- Strengths & Weaknesses -->
        <div class="pd-bento-card pd-bento--strengths">
            <h3><i class="fa-solid fa-circle-check"></i> Strengths</h3>
            <ul>
                @forelse($analysis['strengths'] ?? [] as $strength)
                    <li>{{ $strength }}</li>
                @empty
                    <li class="text-muted">No strengths identified.</li>
                @endforelse
            </ul>
        </div>

        <div class="pd-bento-card pd-bento--weaknesses">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Weaknesses</h3>
            <ul>
                @forelse($analysis['weaknesses'] ?? [] as $weakness)
                    <li>{{ $weakness }}</li>
                @empty
                    <li class="text-muted">No weaknesses identified.</li>
                @endforelse
            </ul>
        </div>

        <!-- Category Scores -->
        <div class="pd-bento-card pd-bento--categories pd-bento--wide">
            <h3><i class="fa-solid fa-chart-simple"></i> Category Breakdown</h3>
            <div class="pd-category-grid">
                @php
                    $cats = $analysis['categories'] ?? [];
                    $labels = [
                        'clarity' => 'Clarity',
                        'problem_statement' => 'Problem Statement',
                        'solution_fit' => 'Solution Fit',
                        'market_opportunity' => 'Market Opportunity',
                        'business_model' => 'Business Model',
                        'competitive_advantage' => 'Competitive Advantage',
                        'team_strength' => 'Team Strength',
                        'financial_projections' => 'Financial Projections',
                        'ask_clarity' => 'Ask Clarity',
                    ];
                @endphp
                @foreach($cats as $key => $cat)
                    <div class="pd-category-bar">
                        <div class="pd-category-bar__header">
                            <span class="pd-category-bar__label">{{ $labels[$key] ?? $key }}</span>
                            <span class="pd-category-bar__score">{{ $cat['score'] ?? '?' }}/10</span>
                        </div>
                        <div class="pd-bar">
                            <div class="pd-bar__fill" style="--width: {{ ($cat['score'] ?? 0) * 10 }}%; --bar-color: {{ ($cat['score'] ?? 0) >= 7 ? 'var(--success)' : (($cat['score'] ?? 0) >= 4 ? 'var(--warning)' : 'var(--danger)') }}"></div>
                        </div>
                        <p class="pd-category-bar__comment">{{ $cat['comment'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Key Improvements -->
        <div class="pd-bento-card pd-bento--improvements pd-bento--tall">
            <h3><i class="fa-solid fa-lightbulb"></i> Key Improvements</h3>
            <ol>
                @forelse($analysis['key_improvements'] ?? [] as $improvement)
                    <li>{{ $improvement }}</li>
                @empty
                    <li class="text-muted">No specific improvements suggested.</li>
                @endforelse
            </ol>
        </div>

        <!-- Quick Actions -->
        <div class="pd-bento-card pd-bento--actions">
            <h3><i class="fa-solid fa-bolt"></i> Next Steps</h3>
            <div class="pd-action-list">
                <a href="{{ route('pitch_decks.edit', $deck) }}" class="pp-btn pp-btn--primary pp-btn--block">
                    <i class="fa-solid fa-pen-to-square"></i> Edit & Finalize
                </a>
                <a href="{{ route('startups.create') }}" class="pp-btn pp-btn--ghost pp-btn--block">
                    <i class="fa-solid fa-rocket"></i> Attach to Startup
                </a>
                <a href="{{ route('pitch_decks.index') }}" class="pp-btn pp-btn--ghost pp-btn--block">
                    <i class="fa-solid fa-list"></i> Back to Deck List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection