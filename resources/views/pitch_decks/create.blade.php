@extends('layouts.dashboard')

@section('title', 'Generate Pitch Deck â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/pitch_decks/pitch-decks.css']])
@endpush

@section('content')
<div class="pd-generate">
    <div class="pd-generate__header">
        <a href="{{ route('pitch_decks.index') }}" class="pp-back-link"><i class="fa-solid fa-arrow-left"></i> Back to decks</a>
        <h1>Generate AI Pitch Deck</h1>
        <p>Describe your startup idea and let AI craft a professional, investor-grade pitch deck.</p>
    </div>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @unless(app(\App\Services\AIService::class)->isConfigured())
        <div class="alert-error" style="background:rgba(127,163,154,0.12);border-color:rgba(127,163,154,0.3);color:var(--accent-soft);">
            <strong>Demo mode:</strong> No OpenAI key in <code>.env</code>. A sample deck will be created. Add <code>OPENAI_API_KEY</code> for real AI â€” see <code>docs/SETUP_API_MAIL_SOCIAL.md</code>.
        </div>
    @endunless

    <form action="{{ route('pitch_decks.generate') }}" method="POST" class="pd-generate__form">
        @csrf

        <div class="pd-form-group">
            <label for="title" class="pd-label">Deck Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-input" 
                   value="{{ old('title') }}" 
                   placeholder="e.g., EcoCharge â€” EV Battery as a Service"
                   required maxlength="255">
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="pd-form-group">
            <label for="startup_description" class="pd-label">
                Describe Your Startup <span class="required">*</span>
                <span class="label-hint">Minimum 30 characters. Be as detailed as possible for best results.</span>
            </label>
            <textarea id="startup_description" name="startup_description" class="form-textarea pd-textarea--large"
                      rows="12" placeholder="Describe your startup in detail... 

What problem does it solve? 
Who are your target customers? 
What's your solution? 
What's your business model? 
What traction do you have? 
Who is on your team?
What market are you targeting?"
                      required minlength="30" maxlength="5000">{{ old('startup_description') }}</textarea>
            <div class="pd-char-count"><span id="charCount">0</span> / 5000</div>
            @error('startup_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="pd-form-actions">
            <a href="{{ route('pitch_decks.index') }}" class="pp-btn pp-btn--ghost">Cancel</a>
            <button type="submit" class="pp-btn pp-btn--primary pp-btn--large">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Pitch Deck
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const textarea = document.getElementById('startup_description');
    const counter = document.getElementById('charCount');
    textarea.addEventListener('input', () => {
        counter.textContent = textarea.value.length;
    });
    counter.textContent = textarea.value.length;
</script>
@endpush
