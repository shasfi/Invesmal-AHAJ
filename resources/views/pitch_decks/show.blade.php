@extends('layouts.dashboard')

@section('title', $deck->title . ' â€” Pitch Deck Details')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-detail">
    <div class="pd-detail__topbar">
        <a href="{{ route('pitch_decks.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> Back to decks</a>
        <div class="pd-detail__topbar-actions">
            @if($deck->content_json)
                <a href="{{ route('pitch_decks.edit', $deck) }}" class="pp-btn pp-btn--sm pp-btn--primary">Edit Deck</a>
            @endif
            @if($deck->status !== 'analyzed' && $deck->content_json)
                <a href="{{ route('pitch_decks.analyze', $deck) }}" class="pp-btn pp-btn--sm pp-btn--accent">Analyze</a>
            @endif
        </div>
    </div>

    <div class="pd-detail__hero">
        <div class="pd-detail__header">
            <span class="pd-badge pd-badge--{{ $deck->status }}">{{ ucfirst($deck->status) }}</span>
            <h1>{{ $deck->title }}</h1>
            @if($deck->content_json && isset($deck->content_json['tagline']))
                <p class="pd-tagline-display">{{ $deck->content_json['tagline'] }}</p>
            @endif
        </div>
        <div class="pd-detail__meta">
            <div class="pd-meta-item">
                <span class="pd-meta-label">Score</span>
                <span class="pd-meta-value">
                    @if($deck->ai_score)
                        <span class="pd-score-ring" style="--score: {{ $deck->ai_score }}%; --score-color: {{ $deck->ai_score >= 70 ? 'var(--success)' : ($deck->ai_score >= 40 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $deck->ai_score }}
                        </span>
                    @else
                        <span class="text-muted">Not scored</span>
                    @endif
                </span>
            </div>
            <div class="pd-meta-item">
                <span class="pd-meta-label">Last Updated</span>
                <span class="pd-meta-value">{{ $deck->updated_at->format('M d, Y') }}</span>
            </div>
            <div class="pd-meta-item">
                <span class="pd-meta-label">Sections</span>
                <span class="pd-meta-value">{{ $deck->content_json && isset($deck->content_json['sections']) ? count($deck->content_json['sections']) : 0 }}</span>
            </div>
            <div class="pd-meta-item">
                <span class="pd-meta-label">File</span>
                <span class="pd-meta-value">{{ $deck->file_type ? strtoupper($deck->file_type) : 'Generated' }}</span>
            </div>
        </div>
    </div>

    @if($deck->content_json && isset($deck->content_json['executive_summary']))
        <div class="pd-detail__summary">
            <h2>Executive Summary</h2>
            <p>{{ $deck->content_json['executive_summary'] }}</p>
        </div>
    @endif

    @if($deck->content_json && isset($deck->content_json['sections']))
        <div class="pd-detail__sections">
            <h2>Pitch Deck Slides</h2>
            <div class="pd-slides-grid">
                @foreach($deck->content_json['sections'] as $section)
                    <div class="pd-slide-card">
                        <h3>{{ $section['title'] ?? 'Untitled' }}</h3>
                        <p>{{ $section['content'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($deck->ai_analysis && is_array($deck->ai_analysis))
        <div class="pd-detail__analysis-preview">
            <h2>AI Analysis Insights</h2>
            <div class="pd-insight-strip">
                <div class="pd-insight">
                    <span class="pd-insight__label">Overall Score</span>
                    <span class="pd-insight__value">{{ $deck->ai_analysis['overall_score'] ?? '?' }}/100</span>
                </div>
                <div class="pd-insight">
                    <span class="pd-insight__label">Investor Readiness</span>
                    <span class="pd-insight__value">{{ $deck->ai_analysis['investor_readiness'] ?? 'N/A' }}</span>
                </div>
                <div class="pd-insight">
                    <span class="pd-insight__label">Top Strength</span>
                    <span class="pd-insight__value">{{ $deck->ai_analysis['strengths'][0] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="pd-detail__actions-bottom">
        @if($deck->content_json)
            <a href="{{ route('pitch_decks.edit', $deck) }}" class="pp-btn pp-btn--primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
        @endif
        <form action="{{ route('pitch_decks.destroy', $deck) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="pp-btn pp-btn--danger" onclick="return confirm('Delete this pitch deck?')">
                <i class="fa-solid fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>
@endsection