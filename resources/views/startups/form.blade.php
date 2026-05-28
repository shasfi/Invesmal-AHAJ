@extends('layouts.dashboard')

@php $editing = isset($startup); @endphp

@section('title', ($editing ? 'Edit' : 'Create') . ' Startup — Invesmal')

@section('content')

<div class="dashboard-topbar">
    <div class="topbar-left">
        <div>
            <h1 class="topbar-title">{{ $editing ? 'Edit Startup' : 'Create Startup' }}</h1>
            <p class="topbar-subtitle">{{ $editing ? 'Update your venture details' : 'Launch your first venture' }}</p>
        </div>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ $editing ? route('startups.update', $startup) : route('startups.store') }}" enctype="multipart/form-data">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="form-row">
            <div class="auth-form-group">
                <label for="name">Startup Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $startup->name ?? '') }}" required>
            </div>
            <div class="auth-form-group">
                <label for="founder_id">Founder</label>
                <select id="founder_id" name="founder_id" class="form-select">
                    <option value="">Select Founder</option>
                    @foreach($founders ?? [] as $founder)
                        <option value="{{ $founder->id }}" {{ (old('founder_id', $startup->founder_id ?? '') == $founder->id) ? 'selected' : '' }}>{{ $founder->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="auth-form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" class="form-textarea" rows="4" required>{{ old('description', $startup->description ?? '') }}</textarea>
        </div>

        <div class="form-row">
            <div class="auth-form-group">
                <label for="industry">Industry</label>
                <input type="text" id="industry" name="industry" class="form-input" value="{{ old('industry', $startup->industry ?? '') }}">
            </div>
            <div class="auth-form-group">
                <label for="stage">Stage</label>
                <select id="stage" name="stage" class="form-select">
                    <option value="idea" {{ (old('stage', $startup->stage ?? '') == 'idea') ? 'selected' : '' }}>Idea</option>
                    <option value="mvp" {{ (old('stage', $startup->stage ?? '') == 'mvp') ? 'selected' : '' }}>MVP</option>
                    <option value="funded" {{ (old('stage', $startup->stage ?? '') == 'funded') ? 'selected' : '' }}>Funded</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="auth-form-group">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" class="form-input" value="{{ old('website', $startup->website ?? '') }}" placeholder="https://">
            </div>
            <div class="auth-form-group">
                <label for="team_size">Team Size</label>
                <input type="number" id="team_size" name="team_size" class="form-input" value="{{ old('team_size', $startup->team_size ?? '') }}" min="1">
            </div>
        </div>

        @php
            $userDecks = \App\Models\PitchDeck::where('user_id', auth()->id())
                ->whereIn('status', ['generated', 'analyzed', 'final'])
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp
        @if($userDecks->isNotEmpty())
        <div class="auth-form-group">
            <label for="pitch_deck_id">Attach Pitch Deck</label>
            <select id="pitch_deck_id" name="pitch_deck_id" class="form-select">
                <option value="">— None —</option>
                @foreach($userDecks as $deck)
                    <option value="{{ $deck->id }}" {{ old('pitch_deck_id', $startup->pitch_deck_id ?? '') == $deck->id ? 'selected' : '' }}>
                        {{ $deck->title }} {{ $deck->ai_score ? '(Score: ' . $deck->ai_score . ')' : '' }}
                    </option>
                @endforeach
            </select>
            <p class="hint">Select a finalized pitch deck to display on your public startup profile. <a href="{{ route('pitch_decks.create') }}">Generate a new one →</a></p>
        </div>
        @else
        <div class="auth-form-group">
            <p class="hint" style="margin-top: 0.5rem;">
                <i class="fa-solid fa-lightbulb"></i> 
                <a href="{{ route('pitch_decks.create') }}">Generate an AI pitch deck</a> first, then attach it to your startup for public display.
            </p>
        </div>
        @endif

        <div class="auth-form-group">
            <label for="logo">Logo</label>
            <div class="file-upload-wrap">
                <label class="file-label">
                    <i class="fa-solid fa-image"></i> Choose File
                    <input type="file" id="logo" name="logo" class="file-input" accept="image/*">
                </label>
                <span class="file-name">{{ $editing && $startup->logo ? 'Current: ' . basename($startup->logo) : 'No file chosen' }}</span>
            </div>
            @if($editing && $startup->logo)
                <img src="{{ $startup->logo_url }}" alt="Current logo" style="width: 64px; height: 64px; border-radius: var(--radius-md); margin-top: 0.5rem; border: 1px solid var(--border);">
            @endif
            <p class="hint">Recommended: 512×512 PNG or JPG</p>
        </div>

        <div class="form-actions">
            <a href="{{ $editing ? route('startups.show', $startup) : route('dashboard') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">{{ $editing ? 'Update Startup' : 'Create Startup' }}</button>
        </div>
    </form>
</div>

@endsection