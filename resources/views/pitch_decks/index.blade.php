@extends('layouts.dashboard')

@section('title', 'My Pitch Decks â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-index">
    <div class="pd-index__hero">
        <div class="pd-index__hero-text">
            <h1>Pitch Deck Intelligence</h1>
            <p>AI-powered pitch deck generation, analysis, and optimization for investor-ready presentations.</p>
        </div>
        <div class="pd-index__hero-actions">
            <a href="{{ route('pitch_decks.create') }}" class="pp-btn pp-btn--primary">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate New
            </a>
            <a href="{{ route('pitch_decks.upload.form') }}" class="pp-btn pp-btn--ghost">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Analyze
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if($decks->isEmpty())
        <div class="pd-empty">
            <div class="pd-empty__icon"><i class="fa-solid fa-file-powerpoint"></i></div>
            <h3>No pitch decks yet</h3>
            <p>Generate an AI-powered pitch deck from your startup idea, or upload an existing one for analysis.</p>
            <div class="pd-empty__actions">
                <a href="{{ route('pitch_decks.create') }}" class="pp-btn pp-btn--primary">Generate First Deck</a>
                <a href="{{ route('pitch_decks.upload.form') }}" class="pp-btn pp-btn--ghost">Upload Existing</a>
            </div>
        </div>
    @else
        <div class="pd-grid">
            @foreach($decks as $deck)
                <div class="pd-card {{ $deck->ai_score ? 'pd-card--scored' : '' }}">
                    <div class="pd-card__status">
                        <span class="pd-badge pd-badge--{{ $deck->status }}">
                            {{ ucfirst($deck->status) }}
                        </span>
                        @if($deck->ai_score)
                            <span class="pd-card__score">
                                <span class="pd-score-ring" style="--score: {{ $deck->ai_score }}%; --score-color: {{ $deck->ai_score >= 70 ? 'var(--success)' : ($deck->ai_score >= 40 ? 'var(--warning)' : 'var(--danger)') }}">
                                    {{ $deck->ai_score }}
                                </span>
                            </span>
                        @endif
                    </div>
                    <h3 class="pd-card__title">{{ $deck->title }}</h3>
                    @if($deck->startup_description)
                        <p class="pd-card__excerpt">{{ \Illuminate\Support\Str::limit($deck->startup_description, 100) }}</p>
                    @endif
                    <div class="pd-card__meta">
                        <span><i class="fa-regular fa-clock"></i> {{ $deck->updated_at->diffForHumans() }}</span>
                        @if($deck->file_type)
                            <span><i class="fa-regular fa-file"></i> {{ strtoupper($deck->file_type) }}</span>
                        @endif
                        <span><i class="fa-solid fa-layer-group"></i> {{ $deck->content_json && isset($deck->content_json['sections']) ? count($deck->content_json['sections']) : 0 }} sections</span>
                    </div>
                    <div class="pd-card__actions">
                        @if($deck->content_json)
                            <a href="{{ route('pitch_decks.edit', $deck) }}" class="pp-btn pp-btn--sm pp-btn--primary">View & Edit</a>
                        @endif
                        @if($deck->file_path && $deck->status === 'draft')
                            <a href="{{ route('pitch_decks.analyze', $deck) }}" class="pp-btn pp-btn--sm pp-btn--accent">Analyze</a>
                        @endif
                        @if($deck->status === 'generated' && $deck->content_json)
                            <a href="{{ route('pitch_decks.analyze', $deck) }}" class="pp-btn pp-btn--sm pp-btn--accent">Analyze</a>
                        @endif
                        @if($deck->status === 'analyzed')
                            <a href="{{ route('pitch_decks.analysis', $deck) }}" class="pp-btn pp-btn--sm pp-btn--ghost">View Analysis</a>
                        @endif
                        <form action="{{ route('pitch_decks.destroy', $deck) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="pp-btn pp-btn--sm pp-btn--danger" onclick="return confirm('Delete this pitch deck?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection