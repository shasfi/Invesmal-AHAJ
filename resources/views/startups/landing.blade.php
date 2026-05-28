@extends('layouts.public')

@section('title', 'Invesmal - Discover the Next Billion Dollar Startup')

@push('styles')
@include('partials.styles-module', ['entries' => ['resources/css/public/landing.css']])
@endpush

@section('content')

{{-- Hero Section --}}
<section class="landing-hero">
    <div class="landing-hero-bg">
        <div class="landing-hero-blob-1"></div>
        <div class="landing-hero-blob-2"></div>
    </div>

    <div class="landing-hero-inner">
        <div class="landing-hero-text">
            <h1 class="landing-hero-title">Discover the Next<br><span class="landing-gradient-text">Billion Dollar Startup</span></h1>
            <p class="landing-hero-subtitle">The premier platform connecting visionary founders with world-class investors. Explore curated startups, track funding rounds, and invest in the future.</p>

            <div class="landing-search-wrap" x-data="liveSearch()">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    x-model="query"
                    @input="debouncedSearch()"
                    placeholder="Search startups by name, industry, or mission..."
                    class="landing-search-input"
                >
                <div class="landing-chips">
                    <button type="button" class="landing-chip" @click="filterStage('')">All</button>
                    <button type="button" class="landing-chip" @click="filterStage('idea')">Idea</button>
                    <button type="button" class="landing-chip" @click="filterStage('mvp')">MVP</button>
                    <button type="button" class="landing-chip" @click="filterStage('funded')">Funded</button>
                </div>

                {{-- Live Search Results Dropdown --}}
                <div class="landing-search-dropdown" x-show="results.length > 0 || searching" x-cloak>
                    <template x-if="searching">
                        <div class="landing-search-loading"><div class="landing-skeleton"></div><div class="landing-skeleton"></div></div>
                    </template>
                    <template x-for="s in results" :key="s.id">
                        <a :href="`/startups/${s.id}`" class="landing-search-result">
                            <img :src="s.logo_url" :alt="s.name" class="landing-search-result-logo">
                            <div class="landing-search-result-meta">
                                <span class="landing-search-result-name" x-text="s.name"></span>
                                <span class="landing-search-result-industry" x-text="s.industry || 'Uncategorized'"></span>
                            </div>
                            <span class="landing-search-result-stage" x-text="s.stage"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <div class="landing-hero-stats">
            <div class="landing-stat-float-card">
                <span class="landing-stat-float-num">{{ number_format($stats['total_startups']) }}</span>
                <span class="landing-stat-float-label">Startups</span>
            </div>
            <div class="landing-stat-float-card">
                <span class="landing-stat-float-num">{{ number_format($stats['active_investors']) }}</span>
                <span class="landing-stat-float-label">Investors</span>
            </div>
            <div class="landing-stat-float-card">
                <span class="landing-stat-float-num">${{ $stats['total_raised'] > 0 ? number_format($stats['total_raised'] / 1000000, 1) . 'M' : '0' }}</span>
                <span class="landing-stat-float-label">Raised</span>
            </div>
        </div>
    </div>
</section>

{{-- Featured Startup Banner --}}
@if($featured)
<section class="landing-section">
    <div class="landing-section-inner">
        <div class="landing-featured-card">
            <div class="landing-featured-badge">
                <i class="fa-solid fa-star"></i> Featured Startup
            </div>
            <div class="landing-featured-inner">
                <div class="landing-featured-left">
                    <img src="{{ $featured->logo_url }}" alt="{{ $featured->name }}" class="landing-featured-logo">
                    <div class="landing-featured-info">
                        <h2 class="landing-featured-name">{{ $featured->name }}</h2>
                        <p class="landing-featured-desc">{{ Str::limit($featured->mission ?? $featured->description, 120) }}</p>
                        <div class="landing-featured-tags">
                            <span class="stage-badge stage-{{ $featured->stage }}">{{ ucfirst($featured->stage) }}</span>
                            @if($featured->industry)<span class="industry-tag">{{ $featured->industry }}</span>@endif
                            @if($featured->is_verified)<span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified</span>@endif
                        </div>
                    </div>
                </div>
                <div class="landing-featured-right">
                    <div class="landing-featured-funding">
                        <div class="landing-featured-raised">
                            <span class="landing-featured-amount">{{ $featured->formatted_amount_raised }}</span>
                            <span class="landing-featured-goal">of {{ $featured->formatted_funding_goal }} raised</span>
                        </div>
                        <div class="landing-featured-progress-wrap">
                            <div class="landing-featured-progress-bar" style="width: {{ $featured->funding_percent }}%"></div>
                        </div>
                        <div class="landing-featured-stats-row">
                            <span><strong>{{ $featured->funding_percent }}%</strong> funded</span>
                            <span><strong>{{ $featured->investor_count }}</strong> investors</span>
                            @if($featured->days_left !== null)
                                <span><strong>{{ $featured->days_left }}</strong> days left</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('startups.show', $featured) }}" class="landing-featured-cta">View Startup <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Main Discovery Grid --}}
<section class="landing-section">
    <div class="landing-section-inner">
        <div class="landing-section-header">
            <h2 class="landing-section-title">Explore Startups by Category</h2>
            <p class="landing-section-desc">CleanTech, AgriTech, FinTech, HealthTech, EdTech & more</p>
        </div>

        <form method="GET" action="{{ route('landing') }}" class="discovery-filters" x-data="{ stage: '{{ $stage ?? '' }}', industry: '{{ $industry ?? '' }}' }">
            <select name="stage" class="filter-select" x-model="stage" @change="$el.form.submit()">
                <option value="">All Stages</option>
                <option value="idea">Idea</option>
                <option value="mvp">MVP</option>
                <option value="funded">Funded</option>
            </select>
            <select name="industry" class="filter-select" x-model="industry" @change="$el.form.submit()">
                <option value="">All Industries</option>
                @foreach($industries as $ind)
                    <option value="{{ $ind }}">{{ $ind }}</option>
                @endforeach
            </select>
            @if($stage || $industry)
                <a href="{{ route('landing') }}" class="filter-clear"><i class="fa-solid fa-xmark"></i> Clear</a>
            @endif
        </form>

        @include('startups.partials.by-industry', [
            'startupsByIndustry' => $startupsByIndustry,
            'totalCount' => collect($startupsByIndustry)->sum(fn ($items) => count($items)),
        ])
    </div>
</section>

{{-- Trending Startups --}}
@if($trending->isNotEmpty())
<section class="landing-section">
    <div class="landing-section-inner">
        <div class="landing-section-header">
            <span class="landing-section-eyebrow"><i class="fa-solid fa-fire"></i> Trending Now</span>
            <h2 class="landing-section-title">Startups gaining the most investor attention</h2>
        </div>
        <div class="startup-grid">
            @foreach($trending as $startup)
                <a href="{{ route('startups.show', $startup) }}" class="startup-card" data-reveal>
                    <div class="card-glow"></div>
                    <div class="card-top">
                        <img src="{{ $startup->logo_url }}" alt="{{ $startup->name }}" class="card-logo" loading="lazy">
                        <div class="card-meta">
                            <span class="card-name">{{ $startup->name }}</span>
                            <div class="card-badges">
                                <span class="stage-badge stage-{{ $startup->stage }}">{{ ucfirst($startup->stage) }}</span>
                                @if($startup->is_verified)
                                    <span class="verified-mini"><i class="fa-solid fa-circle-check"></i></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="card-description">{{ Str::limit($startup->mission ?? $startup->description, 90) ?: 'No description yet.' }}</p>
                    @if($startup->funding_goal)
                    <div class="card-funding">
                        <div class="card-funding-top">
                            <span class="card-raised">{{ $startup->formatted_amount_raised }}</span>
                            <span class="card-goal">of {{ $startup->formatted_funding_goal }}</span>
                        </div>
                        <div class="card-progress-wrap">
                            <div class="card-progress-bar" style="width: {{ $startup->funding_percent }}%"></div>
                        </div>
                        <div class="card-funding-stats">
                            <span>{{ $startup->funding_percent }}% funded</span>
                            <span>{{ $startup->investor_count }} investors</span>
                        </div>
                    </div>
                    @endif
                    <div class="card-bottom">
                        <span class="card-industry">{{ $startup->industry ?? 'Uncategorized' }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Recently Funded --}}
@if($recentlyFunded->isNotEmpty())
<section class="landing-section">
    <div class="landing-section-inner">
        <div class="landing-section-header">
            <span class="landing-section-eyebrow"><i class="fa-solid fa-chart-line"></i> Recently Funded</span>
            <h2 class="landing-section-title">Latest success stories from the ecosystem</h2>
        </div>
        <div class="landing-recent-grid">
            @foreach($recentlyFunded as $startup)
                <a href="{{ route('startups.show', $startup) }}" class="landing-recent-card">
                    <img src="{{ $startup->logo_url }}" alt="{{ $startup->name }}" class="landing-recent-logo">
                    <div class="landing-recent-info">
                        <h4 class="landing-recent-name">{{ $startup->name }}</h4>
                        <p class="landing-recent-raised">Raised {{ $startup->formatted_amount_raised }}</p>
                    </div>
                    <div class="landing-recent-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@guest
{{-- CTA Footer --}}
<section class="landing-cta">
    <div class="landing-cta-inner">
        <h2>Ready to invest in the future?</h2>
        <p>Join thousands of investors discovering the next generation of founders.</p>
        <div class="landing-cta-buttons">
            <a href="{{ route('register') }}" class="landing-cta-btn-primary">Create Account</a>
            <a href="{{ route('startups.discover') }}" class="landing-cta-btn-secondary">Browse All Startups</a>
        </div>
    </div>
</section>
@endguest

@endsection

@push('scripts')
<script>
function liveSearch() {
    return {
        query: '',
        results: [],
        searching: false,
        timeout: null,
        stage: '',
        debouncedSearch() {
            clearTimeout(this.timeout);
            if (!this.query.trim()) {
                this.results = [];
                return;
            }
            this.timeout = setTimeout(() => this.doSearch(), 250);
        },
        async doSearch() {
            this.searching = true;
            try {
                const res = await fetch(`/discover/search?q=${encodeURIComponent(this.query)}&stage=${this.stage}`);
                const data = await res.json();
                this.results = data.startups || [];
            } catch (e) {
                this.results = [];
            }
            this.searching = false;
        },
        filterStage(stage) {
            this.stage = stage;
            this.debouncedSearch();
        }
    }
}
</script>
@endpush