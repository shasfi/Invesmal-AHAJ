@extends('layouts.dashboard')

@section('title', $deck->title . ' â€” Edit Pitch Deck')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-editor">
    <div class="pd-editor__topbar">
        <a href="{{ route('pitch_decks.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> Back to decks</a>
        <div class="pd-editor__topbar-actions">
            <span class="pd-badge pd-badge--{{ $deck->status }}">{{ ucfirst($deck->status) }}</span>
            @if($deck->content_json && $deck->status !== 'analyzed')
                <a href="{{ route('pitch_decks.analyze', $deck) }}" class="pp-btn pp-btn--sm pp-btn--accent">
                    <i class="fa-solid fa-microscope"></i> Analyze This Deck
                </a>
            @endif
            @if($deck->status === 'analyzed')
                <a href="{{ route('pitch_decks.analysis', $deck) }}" class="pp-btn pp-btn--sm pp-btn--ghost">View Analysis</a>
            @endif
        </div>
    </div>

    <h1>{{ $deck->title }}</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($deck->content_json)
        <form action="{{ route('pitch_decks.update', $deck) }}" method="POST" class="pd-editor__form">
            @csrf
            @method('PUT')

            <!-- Executive Summary -->
            <div class="pd-editor__summary-block">
                <div class="pd-form-group">
                    <label class="pd-label pd-label--lg">Tagline</label>
                    <input type="text" name="tagline" class="form-input pd-tagline-input"
                           value="{{ old('tagline', $deck->content_json['tagline'] ?? '') }}" 
                           maxlength="500" required>
                </div>
                <div class="pd-form-group">
                    <label class="pd-label pd-label--lg">Executive Summary</label>
                    <textarea name="executive_summary" class="form-textarea pd-summary-textarea"
                              rows="4" maxlength="2000" required>{{ old('executive_summary', $deck->content_json['executive_summary'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- Sections -->
            <div class="pd-editor__sections">
                <h2 class="pd-editor__section-title">Slide Sections</h2>
                @php $sections = $deck->content_json['sections'] ?? []; @endphp
                @foreach($sections as $index => $section)
                    <div class="pd-editor__card" data-section-id="{{ $section['id'] ?? $index }}">
                        <div class="pd-editor__card-header">
                            <input type="text" 
                                   name="sections[{{ $index }}][title]" 
                                   class="pd-section-title-input"
                                   value="{{ old("sections.{$index}.title", $section['title'] ?? '') }}" 
                                   placeholder="Section title" required>
                            <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section['id'] ?? '' }}">
                        </div>
                        <textarea name="sections[{{ $index }}][content]" 
                                  class="form-textarea pd-section-content"
                                  rows="5" maxlength="5000" required
                                  placeholder="Section content...">{{ old("sections.{$index}.content", $section['content'] ?? '') }}</textarea>
                    </div>
                @endforeach
            </div>

            <div class="pd-editor__actions">
                <a href="{{ route('pitch_decks.index') }}" class="pp-btn pp-btn--ghost">Cancel</a>
                <button type="submit" class="pp-btn pp-btn--primary pp-btn--large">
                    <i class="fa-solid fa-check"></i> Save Pitch Deck
                </button>
            </div>
        </form>
    @else
        <div class="pd-empty">
            <p>No content to edit. Generate content first.</p>
            <a href="{{ route('pitch_decks.create') }}" class="pp-btn pp-btn--primary">Generate Pitch Deck</a>
        </div>
    @endif
</div>
@endsection