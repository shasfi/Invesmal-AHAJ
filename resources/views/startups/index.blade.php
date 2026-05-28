@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.public')

@section('title', ($pageTitle ?? 'Discover Startups') . ' - Invesmal')

@push('styles')
@include('partials.styles-module', ['entries' => [
        'resources/css/startups/discovery.css',
        'resources/css/startups/startup-cards.css',
    ]])
@endpush

@section('content')
<div class="discovery-page">

{{-- CINEMATIC HERO --}}
<section class="discovery-hero">
    <div class="discovery-hero-bg">
        <div class="discovery-hero-blob discovery-hero-blob--1"></div>
        <div class="discovery-hero-blob discovery-hero-blob--2"></div>
        <div class="discovery-hero-blob discovery-hero-blob--3"></div>
    </div>
    <div class="discovery-hero-content">
        <div class="discovery-hero-eyebrow">
            <i class="fa-solid fa-circle"></i> LIVE MARKETPLACE
        </div>
        <h1 class="discovery-hero-title">
            Discover the <span>next big thing</span>
        </h1>
        <p class="discovery-hero-subtitle">
            Browse university-born startups across Tech, AgriTech, FinTech, HealthTech & more. Find your next investment, partnership, or inspiration.
        </p>

        <div class="live-search-wrap" x-data="{ query: '', results: [], open: false, loading: false }" @click.outside="open = false">
            <i class="fa-solid fa-magnifying-glass live-search-icon"></i>
            <input
                type="text"
                class="live-search-input"
                placeholder="Search by name, mission, or industry..."
                x-model="query"
                @input.debounce.300ms="
                    if (query.length < 2) { open = false; results = []; return; }
                    loading = true;
fetch('{{ route('startups.search') }}?q=' + encodeURIComponent(query) + '&stage={{ $stage ?? '' }}&industry={{ $industry ?? '' }}')
                        .then(r => r.json())
                        .then(data => { results = data.startups || []; open = results.length > 0; loading = false; })
                        .catch(() => { loading = false; })
                "
                @focus="if (results.length > 0) open = true"
            >
            <div class="live-search-dropdown" :class="{ active: open }">
                <template x-if="loading">
                    <div class="live-search-empty">Searching...</div>
                </template>
                <template x-if="!loading && results.length === 0 && query.length >= 2">
                    <div class="live-search-empty">
                        <i class="fa-solid fa-rocket" style="display:block;font-size:1.5rem;margin-bottom:0.5rem;opacity:0.4;"></i>
                        No startups found matching "{{-- x-text removed, use span --}}<span x-text="query"></span>"
                    </div>
                </template>
                <template x-for="startup in results" :key="startup.id">
<a :href="'/startups/' + startup.id" class="live-search-result">
                        <img :src="startup.logo_url" :alt="startup.name" class="live-search-result-img">
                        <div class="live-search-result-info">
                            <span class="live-search-result-name" x-text="startup.name"></span>
                            <span class="live-search-result-desc" x-text="startup.mission || startup.description || ''"></span>
                        </div>
                        <span class="live-search-result-badge" x-text="startup.stage"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>

<div class="discovery-inner">

{{-- STATS STRIP --}}
<div class="discovery-stats-strip" data-reveal="fade-up">
    <div class="discovery-stat-tile">
        <div class="discovery-stat-icon"><i class="fa-solid fa-rocket"></i></div>
        <div class="discovery-stat-text">
            <span class="discovery-stat-num">{{ $totalCount ?? 0 }}</span>
            <span class="discovery-stat-label">Startups Listed</span>
        </div>
    </div>
    <div class="discovery-stat-tile">
        <div class="discovery-stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="discovery-stat-text">
            <span class="discovery-stat-num">{{ \App\Models\Startup::where('amount_raised', '>', 0)->count() }}</span>
            <span class="discovery-stat-label">Funded</span>
        </div>
    </div>
    <div class="discovery-stat-tile">
        <div class="discovery-stat-icon"><i class="fa-solid fa-building-columns"></i></div>
        <div class="discovery-stat-text">
            <span class="discovery-stat-num">${{ number_format(\App\Models\Startup::sum('amount_raised') ?: 0) }}</span>
            <span class="discovery-stat-label">Total Raised</span>
        </div>
    </div>
    <div class="discovery-stat-tile">
        <div class="discovery-stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="discovery-stat-text">
            <span class="discovery-stat-num">{{ \App\Models\Investment::distinct('investor_id')->count('investor_id') }}</span>
            <span class="discovery-stat-label">Active Investors</span>
        </div>
    </div>
</div>

{{-- FEATURED BANNER --}}
@php
    $featuredStartup = \App\Models\Startup::with('founder')->where('is_verified', true)->whereNotNull('funding_goal')->orderByDesc('amount_raised')->first();
@endphp
@if($featuredStartup)
<div class="featured-banner" data-reveal="fade-in">
    <a href="{{ route('startups.show', $featuredStartup) }}" class="featured-banner-main">
        <div class="featured-glow"></div>
        <span class="featured-label"><i class="fa-solid fa-star"></i> Featured Startup</span>
        <h2 class="featured-name">{{ $featuredStartup->name }}</h2>
        <p class="featured-mission">{{ Str::limit($featuredStartup->mission ?? $featuredStartup->description, 160) }}</p>
        <span class="featured-cta">
            View profile <i class="fa-solid fa-arrow-right"></i>
        </span>
    </a>
    <div class="featured-banner-side">
        <div class="featured-side-card">
            <span class="featured-side-label">RAISED</span>
            <span class="featured-side-title">{{ $featuredStartup->formatted_amount_raised }}</span>
            <span class="featured-side-desc">{{ $featuredStartup->funding_percent }}% of {{ $featuredStartup->formatted_funding_goal }}</span>
        </div>
        <div class="featured-side-card">
            <span class="featured-side-label">INVESTORS</span>
            <span class="featured-side-title">{{ $featuredStartup->investor_count }}+ investors</span>
            <span class="featured-side-desc">{{ $featuredStartup->industry ?? 'Uncategorized' }} Â· Stage: {{ ucfirst($featuredStartup->stage) }}</span>
        </div>
    </div>
</div>
@endif

{{-- TRENDING STARTUPS --}}
@php
    $trending = \App\Models\Startup::with('founder')->trending()->limit(8)->get();
@endphp
@if($trending->isNotEmpty())
<div class="trending-strip" data-reveal="fade-up">
    <div class="trending-strip-header">
        <h2 class="trending-strip-title">
            <i class="fa-solid fa-fire"></i> Trending Startups
        </h2>
        <a href="{{ route($listRoute ?? 'startups.discover') }}" class="trending-strip-link">
            View all <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    <div class="trending-scroll">
        @foreach($trending as $index => $t)
        <a href="{{ route('startups.show', $t) }}" class="trending-scroll-card">
            <div class="trending-card-glow"></div>
            <span class="trending-card-rank">{{ $index + 1 }}</span>
            <div class="trending-card-top">
                <img src="{{ $t->logo_url }}" alt="{{ $t->name }}" class="trending-card-logo" loading="lazy">
                <div>
                    <h3 class="trending-card-name">{{ $t->name }}</h3>
                    <span class="trending-card-industry">{{ $t->industry ?? 'Uncategorized' }}</span>
                </div>
            </div>
            <p class="trending-card-desc">{{ Str::limit($t->mission ?? $t->description, 100) }}</p>
            <div class="trending-card-stats">
                <span><i class="fa-solid fa-chart-line"></i> {{ $t->funding_percent }}%</span>
                <span><i class="fa-solid fa-users"></i> {{ $t->investor_count }}</span>
                <span>{{ ucfirst($t->stage) }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- INVESTOR PICKS (Recently Funded) --}}
@php
    $investorPicks = \App\Models\Startup::with('founder')->recentlyFunded()->limit(3)->get();
@endphp
@if($investorPicks->isNotEmpty())
<div class="trending-strip" data-reveal="fade-up">
    <div class="trending-strip-header">
        <h2 class="trending-strip-title">
            <i class="fa-solid fa-trophy"></i> Recently Funded
        </h2>
        <a href="{{ route($listRoute ?? 'startups.discover') }}" class="trending-strip-link">
            See all <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    <div class="trending-scroll">
        @foreach($investorPicks as $ip)
        <a href="{{ route('startups.show', $ip) }}" class="trending-scroll-card">
            <div class="trending-card-glow"></div>
            <div class="trending-card-top">
                <img src="{{ $ip->logo_url }}" alt="{{ $ip->name }}" class="trending-card-logo" loading="lazy">
                <div>
                    <h3 class="trending-card-name">{{ $ip->name }}</h3>
                    <span class="trending-card-industry">{{ $ip->industry ?? 'Uncategorized' }}</span>
                </div>
            </div>
            <p class="trending-card-desc">{{ Str::limit($ip->mission ?? $ip->description, 100) }}</p>
            <div class="trending-card-stats">
                <span><i class="fa-solid fa-circle-check" style="color:var(--success);"></i> {{ $ip->formatted_amount_raised }}</span>
                <span>{{ ucfirst($ip->stage) }}</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- FILTERS BAR (below hero strips, before industries) --}}
<div class="discovery-header" data-reveal="fade-in">
    <h2 class="discovery-title">{{ $pageTitle ?? 'All Startups' }}</h2>
    <p class="discovery-subtitle">{{ $pageSubtitle ?? 'Browse by category â€” Tech, AgriTech, FinTech, HealthTech & more' }}</p>

    @if(auth()->check() && auth()->user()->role === 'student_founder')
        <a href="{{ route('startups.create') }}" class="btn-primary" style="display:inline-flex;margin-top:0.75rem;padding:0.6rem 1.15rem;font-size:0.85rem;">
            <i class="fa-solid fa-plus"></i> New Startup
        </a>
    @endif

    <form method="GET" action="{{ route($listRoute ?? 'startups.discover') }}" class="discovery-filters">
        <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" class="search-input-field" placeholder="Search by name or mission..." value="{{ $search ?? '' }}">
        </div>
        <select name="stage" class="filter-select" onchange="this.form.submit()">
            <option value="">All Stages</option>
            <option value="idea" {{ ($stage ?? '') === 'idea' ? 'selected' : '' }}>Idea</option>
            <option value="mvp" {{ ($stage ?? '') === 'mvp' ? 'selected' : '' }}>MVP</option>
            <option value="funded" {{ ($stage ?? '') === 'funded' ? 'selected' : '' }}>Funded</option>
        </select>
        <select name="industry" class="filter-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($industries as $ind)
                <option value="{{ $ind }}" {{ ($industry ?? '') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary" style="padding:0.65rem 1.25rem;">Search</button>
        @if($search || $stage || $industry)
            <a href="{{ route($listRoute ?? 'startups.discover') }}" class="btn-secondary" style="padding:0.65rem 1rem;">Clear</a>
        @endif
    </form>

    @if(!($industry ?? ''))
    <div class="industry-chips">
        @foreach(\App\Models\Startup::INDUSTRY_CATEGORIES as $cat)
            <a href="{{ route($listRoute ?? 'startups.discover', ['industry' => $cat]) }}" class="industry-chip {{ ($industry ?? '') === $cat ? 'active' : '' }}">
                <i class="fa-solid {{ \App\Models\Startup::industryIcon($cat) }}"></i> {{ $cat }}
            </a>
        @endforeach
    </div>
    @endif
</div>

{{-- INDUSTRY SECTIONS WITH CARDS --}}
@include('startups.partials.by-industry', ['startupsByIndustry' => $startupsByIndustry, 'totalCount' => $totalCount])

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll reveal observer
    var revealEls = document.querySelectorAll('[data-reveal]');
    if (revealEls.length > 0 && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function(el) { observer.observe(el); });
    } else {
        revealEls.forEach(function(el) { el.classList.add('revealed'); });
    }
});
</script>
@endpush