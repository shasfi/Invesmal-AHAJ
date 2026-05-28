@extends('layouts.dashboard')

@section('title', 'Submit Investment Offer â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/investments/investments.css']])
@endpush

@section('content')
<div class="invest-create-page">
    <header class="dashboard-topbar" style="margin-bottom:1rem;">
        <a href="{{ $startup ? route('startups.show', $startup) : route('investments.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </header>

    <div class="invest-steps-mini">
        <span class="active">1. Choose startup</span>
        <span class="active">2. Offer details</span>
        <span>3. Founder review</span>
    </div>

    <div class="form-card">
        <h1 class="form-title">Submit investment offer</h1>
        <p class="form-subtitle">Your offer goes to the founder for approval. You can track status under <strong>Investments</strong>.</p>

        @if($existingInvestment ?? null)
            <div class="existing-offer">
                <i class="fa-solid fa-circle-info"></i>
                You already have a <strong>{{ $existingInvestment->status }}</strong> offer for this startup
                (${{ number_format($existingInvestment->amount) }}).
                <a href="{{ route('investments.show', $existingInvestment) }}">View offer</a>
            </div>
        @endif

        @if($startup ?? null)
            <div class="startup-preview">
                <img src="{{ $startup->logo_url }}" alt="{{ $startup->name }}">
                <div>
                    <h3>{{ $startup->name }}</h3>
                    <p>{{ $startup->industry }} Â· {{ ucfirst($startup->stage) }} Â· Founder: {{ $startup->founder?->name }}</p>
                    @if($startup->funding_goal)
                        <p style="margin-top:0.35rem;color:var(--accent-soft);">Raising {{ $startup->formatted_funding_goal }} ({{ $startup->funding_percent }}% funded)</p>
                    @endif
                </div>
            </div>
        @endif

        @if(!($existingInvestment ?? null) || in_array($existingInvestment->status ?? '', ['rejected'], true))
        <form action="{{ route('investments.store') }}" method="POST">
            @csrf

            @if($startup ?? null)
                <input type="hidden" name="startup_id" value="{{ $startup->id }}">
            @else
            <div class="auth-form-group">
                <label for="startup_id">Startup <span class="required">*</span></label>
                <select name="startup_id" id="startup_id" required class="form-select">
                    <option value="">Select a startup to invest in...</option>
                    @foreach($startups as $s)
                        <option value="{{ $s->id }}" {{ old('startup_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} â€” {{ $s->industry }} ({{ ucfirst($s->stage) }})
                        </option>
                    @endforeach
                </select>
                @error('startup_id') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="auth-form-group">
                <label for="amount">Investment amount (USD) <span class="required">*</span></label>
                <input type="number" name="amount" id="amount" min="1000" step="500" required
                       class="form-input" placeholder="e.g. 25000" value="{{ old('amount') }}">
                <p style="font-size:0.8rem;color:var(--muted);margin-top:0.35rem;">Minimum $1,000. This is your proposed commitment.</p>
                @error('amount') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="auth-form-group">
                <label for="message">Message to founder</label>
                <textarea name="message" id="message" rows="5" maxlength="2000" class="form-textarea"
                          placeholder="Why are you interested? Timeline, terms, or questions for the founder.">{{ old('message') }}</textarea>
                @error('message') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('startups.discover') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Send offer to founder
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
