@extends('layouts.dashboard')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/components/pitch-buttons.css',
    ]])
@endpush

@section('title', 'Sentiment Analysis')

@section('content')
@php
$sentiment = $sentiment ?? null;
$conversation = $conversation ?? null;
@endphp

<div style="margin-bottom:1rem;">
    <a href="{{ route('ai.sentiment.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> All conversations</a>
    <a href="{{ route('ai.sentiment.show', ['conversation' => $conversation, 'refresh' => 1]) }}" class="pp-btn pp-btn--sm pp-btn--ghost" style="margin-left:0.5rem;">Refresh analysis</a>
</div>

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:700;"><i class="fa-solid fa-face-smile" style="color:var(--primary);margin-right:0.5rem;"></i> Sentiment Analysis</h1>
    <p style="color:var(--muted);font-size:0.85rem;">AI-powered conversation and communication insights</p>
</div>

@if($conversation && $sentiment)
    {{-- Score Card --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;text-align:center;">
            <div style="font-size:2.5rem;font-weight:700;margin-bottom:0.5rem;
                @if(($sentiment['score'] ?? 0) >= 70) color:var(--success);
                @elseif(($sentiment['score'] ?? 0) >= 40) color:var(--warning);
                @else color:var(--danger);
                @endif
            ">{{ $sentiment['score'] ?? 0 }}%</div>
            <div style="color:var(--muted);font-size:0.8rem;font-weight:500;">Overall Sentiment Score</div>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;text-align:center;">
            <div style="font-size:2.5rem;margin-bottom:0.5rem;">
                @if(($sentiment['label'] ?? '') === 'Positive') 😊
                @elseif(($sentiment['label'] ?? '') === 'Negative') 😟
                @else 😐
                @endif
            </div>
            <div style="color:var(--muted);font-size:0.8rem;font-weight:500;">Classification: <strong>{{ $sentiment['label'] ?? 'Neutral' }}</strong></div>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:var(--accent-soft);margin-bottom:0.5rem;">{{ $sentiment['message_count'] ?? $conversation->messages->count() }}</div>
            <div style="color:var(--muted);font-size:0.8rem;font-weight:500;">Messages Analyzed</div>
        </div>
    </div>

    {{-- Breakdown --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;">Sentiment Breakdown</h3>
        <div style="display:flex;height:12px;border-radius:6px;overflow:hidden;margin-bottom:1rem;">
            <div style="background:var(--success);width:{{ $sentiment['positive_percent'] ?? 33 }}%;"></div>
            <div style="background:var(--warning);width:{{ $sentiment['neutral_percent'] ?? 34 }}%;"></div>
            <div style="background:var(--danger);width:{{ $sentiment['negative_percent'] ?? 33 }}%;"></div>
        </div>
        <div style="display:flex;gap:1.5rem;font-size:0.85rem;">
            <span style="color:var(--success);">🟢 Positive {{ $sentiment['positive_percent'] ?? 33 }}%</span>
            <span style="color:var(--warning);">🟡 Neutral {{ $sentiment['neutral_percent'] ?? 34 }}%</span>
            <span style="color:var(--danger);">🔴 Negative {{ $sentiment['negative_percent'] ?? 33 }}%</span>
        </div>
    </div>

    @if(!empty($sentiment['summary']))
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">AI Summary</h3>
        <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;">{{ $sentiment['summary'] }}</p>
    </div>
    @endif

    {{-- Conversation Info --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:0.5rem;">Conversation</h3>
        <p style="color:var(--muted);font-size:0.85rem;">
            Participants: {{ $conversation->participants->pluck('name')->join(', ') }}<br>
            Started: {{ $conversation->created_at?->format('M d, Y') }}
        </p>
    </div>

    @if(!empty($sentiment['insights']))
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;">
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;"><i class="fa-solid fa-lightbulb" style="color:var(--accent-soft);margin-right:0.5rem;"></i> Communication Insights</h3>
        @foreach($sentiment['insights'] as $insight)
            <div style="padding:0.5rem 0;border-bottom:1px solid var(--border);font-size:0.85rem;color:var(--muted);">
                {{ $insight }}
            </div>
        @endforeach
    </div>
    @endif
@else
    <div style="text-align:center;padding:3rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);">
        <div style="font-size:3rem;margin-bottom:1rem;">🤖</div>
        <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">No Analysis Available</h3>
        <p style="color:var(--muted);font-size:0.85rem;">Sentiment analysis runs automatically when conversations have sufficient messages. Start a conversation to trigger analysis.</p>
    </div>
@endif
@endsection