@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.public')

@section('title', $startup->name . ' â€” Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/startups/show.css',
        'resources/css/startups/startup-cards.css',
    ]])
@endpush

@section('content')
<div class="show-page">

{{-- HERO SECTION --}}
<section class="show-hero" data-reveal="fade-in">
    <div class="show-hero-bg">
        <div class="show-blob show-blob-1"></div>
        <div class="show-blob show-blob-2"></div>
    </div>
    <div class="show-hero-inner">
        <div class="show-hero-left">
            <div class="show-hero-badges">
                <span class="stage-badge stage-{{ $startup->stage }}">{{ ucfirst($startup->stage) }}</span>
                @if($startup->industry)<span class="industry-tag">{{ $startup->industry }}</span>@endif
                @if($startup->is_verified)<span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified</span>@endif
            </div>
            <h1 class="show-hero-title">{{ $startup->name }}</h1>
            <p class="show-hero-mission">{{ $startup->mission ?? $startup->description }}</p>
            <div class="show-hero-founder">
                <img src="{{ $startup->founder->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($startup->founder->name) . '&background=184343&color=E3D2C0' }}" alt="{{ $startup->founder->name }}" class="founder-avatar">
                <div class="founder-info">
                    <span class="founder-name">{{ $startup->founder->name }}</span>
                    <span class="founder-role">Founder & CEO</span>
                </div>
            </div>
            @if($startup->website)
            <a href="{{ $startup->website }}" target="_blank" rel="noopener" class="show-hero-link">
                <i class="fa-solid fa-globe"></i> {{ parse_url($startup->website, PHP_URL_HOST) ?? $startup->website }}
            </a>
            @endif
        </div>

        {{-- Funding Panel (sticky) --}}
        <div class="show-hero-right">
            <div class="funding-panel">
                @if($startup->funding_goal)
                <div class="funding-panel-header">
                    <span class="funding-panel-label">Funding Round</span>
                    <span class="funding-panel-equity">{{ $startup->equity_offered ? $startup->equity_offered . '% equity' : 'Equity round' }}</span>
                </div>
                <div class="funding-panel-amount">
                    <span class="funding-panel-raised">{{ $startup->formatted_amount_raised }}</span>
                    <span class="funding-panel-goal">of {{ $startup->formatted_funding_goal }}</span>
                </div>
                <div class="funding-panel-progress-wrap">
                    <div class="funding-panel-progress" style="width: {{ $startup->funding_percent }}%"></div>
                </div>
                <div class="funding-panel-stats">
                    <div class="funding-stat">
                        <span class="funding-stat-num">{{ $startup->investor_count }}</span>
                        <span class="funding-stat-label">Investors</span>
                    </div>
                    <div class="funding-stat">
                        <span class="funding-stat-num">{{ $startup->funding_percent }}%</span>
                        <span class="funding-stat-label">Funded</span>
                    </div>
                    @if($startup->days_left !== null)
                    <div class="funding-stat">
                        <span class="funding-stat-num">{{ $startup->days_left }}</span>
                        <span class="funding-stat-label">Days Left</span>
                    </div>
                    @endif
                </div>
                <div class="funding-panel-actions">
                    @auth
                        @if(auth()->user()->role === 'investor')
                            @if($myInvestment ?? null)
                                <div class="funding-panel-status {{ $myInvestment->status }}">
                                    @if($myInvestment->status === 'pending')
                                        <i class="fa-solid fa-hourglass-half"></i> Your offer (${{ number_format($myInvestment->amount) }}) is pending founder review
                                    @elseif($myInvestment->status === 'approved')
                                        <i class="fa-solid fa-circle-check"></i> Investment approved â€” ${{ number_format($myInvestment->amount) }}
                                    @else
                                        <i class="fa-solid fa-circle-xmark"></i> Offer was not accepted
                                    @endif
                                </div>
                                <a href="{{ route('investments.show', $myInvestment) }}" class="funding-panel-cta">
                                    <i class="fa-solid fa-file-contract"></i> View my offer
                                </a>
                                @if($myInvestment->status !== 'pending')
                                    <a href="{{ route('investments.create.startup', $startup) }}" class="funding-panel-cta secondary">
                                        <i class="fa-solid fa-plus"></i> Submit new offer
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('investments.create.startup', $startup) }}" class="funding-panel-cta">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Invest in {{ $startup->name }}
                                </a>
                            @endif
                            <form action="{{ route('conversations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="participant_id" value="{{ $startup->founder_id }}">
                                <input type="hidden" name="subject" value="Regarding {{ $startup->name }}">
                                <button type="submit" class="funding-panel-cta secondary" style="width:100%;cursor:pointer;border:none;font-family:inherit;">
                                    <i class="fa-solid fa-comment"></i> Message founder
                                </button>
                            </form>
                            <a href="{{ route('meetings.create', ['startup_id' => $startup->id, 'invitee_id' => $startup->founder_id]) }}" class="funding-panel-cta secondary">
                                <i class="fa-solid fa-calendar-plus"></i> Schedule meeting
                            </a>
                        @elseif(in_array(auth()->user()->role, ['student_founder', 'admin', 'mentor']))
                            <span class="funding-panel-cta secondary" style="pointer-events:none;opacity:0.7;">
                                <i class="fa-solid fa-eye"></i> Viewing as {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="funding-panel-cta">
                            <i class="fa-solid fa-right-to-bracket"></i> Sign in to invest
                        </a>
                        <a href="{{ route('register') }}" class="funding-panel-cta secondary">Create investor account</a>
                    @endauth
                </div>
                @else
                <div class="funding-panel-empty">
                    <i class="fa-solid fa-rocket"></i>
                    <p>Not currently raising funds</p>
                    <span class="funding-panel-note">Follow this startup for future rounds</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- STORY SECTIONS (alternating layouts) --}}
<div class="show-content">
    <div class="show-main">

        {{-- Problem (Split: Text Left / Visual Right) --}}
        @if($startup->problem)
        <section class="show-section split-layout" data-reveal="slide-left">
            <div class="split-text">
                <span class="section-label"><i class="fa-solid fa-circle-exclamation"></i> The Problem</span>
                <h2 class="section-heading">What problem are we solving?</h2>
                <div class="section-body">{{ $startup->problem }}</div>
            </div>
            <div class="split-visual">
                <div class="split-visual-orb"></div>
                <i class="fa-solid fa-circle-exclamation split-visual-icon"></i>
            </div>
        </section>
        @endif

        {{-- Solution (Split: Visual Left / Text Right â€” reversed) --}}
        @if($startup->solution)
        <section class="show-section split-layout reversed" data-reveal="slide-right">
            <div class="split-text">
                <span class="section-label"><i class="fa-solid fa-lightbulb"></i> The Solution</span>
                <h2 class="section-heading">How we solve it</h2>
                <div class="section-body">{{ $startup->solution }}</div>
            </div>
            <div class="split-visual">
                <div class="split-visual-orb" style="background: radial-gradient(circle, rgba(227,210,192,0.12), transparent 70%);"></div>
                <i class="fa-solid fa-lightbulb split-visual-icon"></i>
            </div>
        </section>
        @endif

        {{-- Market (Bento Grid style) --}}
        @if($startup->market)
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-chart-line"></i> Market</span>
            <h2 class="section-heading">Market opportunity</h2>
            <div class="section-body">{{ $startup->market }}</div>
        </section>
        @endif

        {{-- Business Model (Split: Text Left / Stats Right) --}}
        @if($startup->business_model)
        <section class="show-section split-layout" data-reveal="slide-left">
            <div class="split-text">
                <span class="section-label"><i class="fa-solid fa-building-columns"></i> Business Model</span>
                <h2 class="section-heading">How we make money</h2>
                <div class="section-body">{{ $startup->business_model }}</div>
            </div>
            <div class="split-visual">
                <div class="split-stats">
                    <div class="split-stat-item">
                        <span class="split-stat-num-2">{{ $startup->formatted_amount_raised }}</span>
                        <span class="split-stat-label-2">Amount Raised</span>
                    </div>
                    <div class="split-stat-item">
                        <span class="split-stat-num-2">{{ $startup->funding_percent }}%</span>
                        <span class="split-stat-label-2">Funding Progress</span>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- Traction (Bento Metrics) --}}
        @if($startup->traction)
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-bolt"></i> Traction</span>
            <h2 class="section-heading">Proof of execution</h2>
            <div class="section-body" style="margin-bottom:2rem;">{{ $startup->traction }}</div>

            <div class="show-bento-metrics">
                <div class="bento-metric-main">
                    <span class="bento-metric-main-label">Total Raised</span>
                    <span class="bento-metric-main-value">{{ $startup->formatted_amount_raised }}</span>
                    <span class="bento-metric-main-desc">of {{ $startup->formatted_funding_goal }} funding goal</span>
                </div>
                <div class="bento-metric-sub">
                    <span class="bento-metric-sub-label">Investors</span>
                    <span class="bento-metric-sub-value">{{ $startup->investor_count }}</span>
                    <span class="bento-metric-sub-desc">Active backers</span>
                </div>
                <div class="bento-metric-sub">
                    <span class="bento-metric-sub-label">Funded</span>
                    <span class="bento-metric-sub-value">{{ $startup->funding_percent }}%</span>
                    <span class="bento-metric-sub-desc">Progress to goal</span>
                </div>
                <div class="bento-metric-sub">
                    <span class="bento-metric-sub-label">Stage</span>
                    <span class="bento-metric-sub-value" style="text-transform:capitalize;">{{ $startup->stage }}</span>
                    <span class="bento-metric-sub-desc">Current phase</span>
                </div>
                <div class="bento-metric-sub">
                    <span class="bento-metric-sub-label">Industry</span>
                    <span class="bento-metric-sub-value" style="font-size:1.15rem;">{{ $startup->industry ?? 'N/A' }}</span>
                    <span class="bento-metric-sub-desc">Sector focus</span>
                </div>
            </div>
        </section>
        @endif

        {{-- Vision (Large Typography Block) --}}
        @if($startup->vision)
        <section class="show-section vision-block" data-reveal="fade-in">
            <span class="section-label"><i class="fa-solid fa-eye"></i> Vision</span>
            <h2 class="section-heading">Where we're headed</h2>
            <div class="section-body">{{ $startup->vision }}</div>
        </section>
        @endif

        {{-- KPI Metrics --}}
        @if($startup->metrics && $startup->metrics->isNotEmpty())
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-gauge"></i> Key Metrics</span>
            <h2 class="section-heading">Performance indicators</h2>
            <div class="show-bento-metrics" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
                @foreach($startup->metrics as $metric)
                <div class="bento-metric-sub">
                    <span class="bento-metric-sub-label">{{ $metric->label }}</span>
                    <span class="bento-metric-sub-value">{{ $metric->value }}</span>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Team (Overlapping Avatars) --}}
        @if($startup->team && $startup->team->isNotEmpty())
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-users"></i> The Team</span>
            <h2 class="section-heading">People behind the vision</h2>
            <div class="show-team-overlap">
                <div class="team-overlap-avatars">
                    @foreach($startup->team->take(5) as $member)
                    <img src="{{ $member->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=567256&color=E3D2C0' }}" alt="{{ $member->name }}" class="team-overlap-avatar">
                    @endforeach
                    @if($startup->team->count() > 5)
                    <span class="team-overlap-more">+{{ $startup->team->count() - 5 }}</span>
                    @endif
                </div>
                <div class="team-overlap-info">
                    <span class="team-overlap-name">{{ $startup->team->first()->name }}</span>
                    <span class="team-overlap-role">{{ $startup->team->first()->pivot->role ?? 'Team Lead' }}</span>
                </div>
            </div>
        </section>
        @endif

        {{-- AI Pitch Deck Analysis (existing, polished) --}}
        @if($pitchDeckData)
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-microchip"></i> AI Pitch Intelligence</span>
            <h2 class="section-heading">Pitch Deck Analysis</h2>
            
            <div class="pd-public-bento">
                <!-- Score Card -->
                <div class="pd-public-score-card">
                    <div class="pd-public-score-ring" style="--score: {{ $pitchDeckData['score'] ?? 0 }}%; --score-color: {{ ($pitchDeckData['score'] ?? 0) >= 70 ? 'var(--success)' : (($pitchDeckData['score'] ?? 0) >= 40 ? 'var(--warning)' : 'var(--danger)') }}">
                        <span class="pd-public-score-num">{{ $pitchDeckData['score'] ?? '?' }}</span>
                        <span class="pd-public-score-label">Pitch Score</span>
                    </div>
                    @if(isset($pitchDeckData['summary']['investor_readiness']))
                    <div class="pd-public-readiness">
                        <span class="pd-public-readiness-label">Investor Readiness</span>
                        <span class="pd-public-readiness-badge">{{ $pitchDeckData['summary']['investor_readiness'] }}</span>
                    </div>
                    @endif
                </div>

                <!-- Verdict -->
                <div class="pd-public-verdict">
                    <h3>AI Verdict</h3>
                    <p>{{ $pitchDeckData['summary']['verdict'] ?? 'No verdict available.' }}</p>
                    @if(!empty($pitchDeckData['summary']['tagline']))
                        <p class="pd-public-tagline">"{{ $pitchDeckData['summary']['tagline'] }}"</p>
                    @endif
                </div>

                <!-- Market Opportunity -->
                @if(!empty($pitchDeckData['summary']['market_opportunity']))
                <div class="pd-public-market">
                    <h3><i class="fa-solid fa-chart-line"></i> Market Opportunity</h3>
                    <p>{{ $pitchDeckData['summary']['market_opportunity'] }}</p>
                </div>
                @endif

                <!-- Problem & Solution Excerpt -->
                <div class="pd-public-ps">
                    @if(!empty($pitchDeckData['summary']['problem_excerpt']))
                    <div class="pd-public-problem">
                        <h3><i class="fa-solid fa-circle-exclamation"></i> Problem</h3>
                        <p>{{ $pitchDeckData['summary']['problem_excerpt'] }}</p>
                    </div>
                    @endif
                    @if(!empty($pitchDeckData['summary']['solution_excerpt']))
                    <div class="pd-public-solution">
                        <h3><i class="fa-solid fa-lightbulb"></i> Solution</h3>
                        <p>{{ $pitchDeckData['summary']['solution_excerpt'] }}</p>
                    </div>
                    @endif
                </div>

                <!-- Strengths -->
                @if(!empty($pitchDeckData['summary']['strengths']))
                <div class="pd-public-strengths">
                    <h3><i class="fa-solid fa-star"></i> Key Strengths</h3>
                    <ul>
                        @foreach($pitchDeckData['summary']['strengths'] as $strength)
                            <li>{{ $strength }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Executive Summary -->
                @if(!empty($pitchDeckData['summary']['executive_summary']))
                <div class="pd-public-exec-summary">
                    <h3><i class="fa-solid fa-file-lines"></i> Executive Summary</h3>
                    <p>{{ $pitchDeckData['summary']['executive_summary'] }}</p>
                </div>
                @endif

                <!-- Pitch Deck Sections -->
                @if(!empty($pitchDeckData['sections']))
                <div class="pd-public-sections">
                    <h3>Pitch Deck Preview</h3>
                    <div class="pd-public-slides">
                        @for($i = 0; $i < min(4, count($pitchDeckData['sections'])); $i++)
                            @php $s = $pitchDeckData['sections'][$i]; @endphp
                            <div class="pd-public-slide-card">
                                <h4>{{ $s['title'] ?? '' }}</h4>
                                <p>{{ \Illuminate\Support\Str::limit($s['content'] ?? '', 150) }}</p>
                            </div>
                        @endfor
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Related Startups (Horizontal Scroll) --}}
        @if($relatedStartups && $relatedStartups->isNotEmpty())
        <section class="show-section" data-reveal="fade-up">
            <span class="section-label"><i class="fa-solid fa-rocket"></i> Explore More</span>
            <h2 class="section-heading">Similar startups</h2>
            <div class="related-scroll">
                @foreach($relatedStartups as $related)
                <a href="{{ route('startups.show', $related) }}" class="startup-card-v2">
                    <div class="card-v2-glow"></div>
                    <div class="card-v2-top">
                        <img src="{{ $related->logo_url }}" alt="{{ $related->name }}" class="card-v2-logo" loading="lazy">
                        <div class="card-v2-meta">
                            <span class="card-v2-name">{{ $related->name }}</span>
                            <div class="card-v2-badges">
                                <span class="card-v2-badge">{{ ucfirst($related->stage) }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="card-v2-desc">{{ Str::limit($related->description, 80) }}</p>
                    <div class="card-v2-bottom">
                        <span class="card-v2-industry">{{ $related->industry ?? 'Uncategorized' }}</span>
                        <span class="card-v2-cta">View <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for scroll reveal
    var revealEls = document.querySelectorAll('[data-reveal]');
    if (revealEls.length > 0 && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });
        revealEls.forEach(function(el) { observer.observe(el); });
    } else {
        revealEls.forEach(function(el) { el.classList.add('revealed'); });
    }
});
</script>
@endpush