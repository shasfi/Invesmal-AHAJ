@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/components/pitch-buttons.css',
        'resources/css/documents/documents.css',
    ]])
@endpush

@section('title', 'Sentiment Analysis — Invesmal')

@section('content')
<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:700;"><i class="fa-solid fa-face-smile" style="color:var(--primary);margin-right:0.5rem;"></i> Conversation Sentiment</h1>
    <p style="color:var(--muted);font-size:0.85rem;">AI-powered analysis of your investor–startup communications</p>
</div>

@if($conversations->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-comments empty-icon"></i>
        <h3>No conversations yet</h3>
        <p>Start messaging to enable sentiment analysis.</p>
        <a href="{{ route('conversations.index') }}" class="btn-primary">Open Messages</a>
    </div>
@else
    <div class="document-grid">
        @foreach($conversations as $conversation)
            <div class="document-card">
                <h3 style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">
                    {{ $conversation->subject ?? 'Conversation #' . $conversation->id }}
                </h3>
                <p style="color:var(--muted);font-size:0.8rem;margin-bottom:0.75rem;">
                    {{ $conversation->participants->pluck('name')->join(' · ') }}
                </p>
                @if($conversation->sentiment_label)
                    <span class="stage-badge stage-{{ strtolower($conversation->sentiment_label) === 'positive' ? 'funded' : (strtolower($conversation->sentiment_label) === 'negative' ? 'idea' : 'mvp') }}">
                        {{ $conversation->sentiment_label }} · {{ $conversation->sentiment_score }}%
                    </span>
                @else
                    <span class="industry-tag">Not analyzed yet</span>
                @endif
                <div class="document-actions" style="margin-top:1rem;">
                    <a href="{{ route('ai.sentiment.show', $conversation) }}" class="pp-btn pp-btn--sm pp-btn--primary">View Analysis</a>
                    <a href="{{ route('conversations.show', $conversation) }}" class="pp-btn pp-btn--sm pp-btn--ghost">Open Chat</a>
                </div>
            </div>
        @endforeach
    </div>
    @if($conversations->hasPages())
        <div class="pagination-wrap">{{ $conversations->links() }}</div>
    @endif
@endif
@endsection
