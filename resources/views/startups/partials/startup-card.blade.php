<a href="{{ route('startups.show', $startup) }}" class="startup-card-v2" data-reveal="fade-up">
    <div class="card-v2-glow"></div>
    <div class="card-v2-top">
        <img src="{{ $startup->logo_url }}" alt="{{ $startup->name }}" class="card-v2-logo" loading="lazy">
        <div class="card-v2-meta">
            <span class="card-v2-name">{{ $startup->name }}</span>
            <div class="card-v2-badges">
                <span class="card-v2-badge">{{ ucfirst($startup->stage) }}</span>
                @if($startup->is_verified)
                    <span class="verified-mini"><i class="fa-solid fa-circle-check"></i></span>
                @endif
            </div>
        </div>
    </div>
    <p class="card-v2-desc">{{ Str::limit($startup->mission ?? $startup->description, 90) ?: 'No description yet.' }}</p>
    @if($startup->funding_goal)
    <div class="card-v2-funding">
        <div class="card-v2-funding-top">
            <span class="card-v2-raised">{{ $startup->formatted_amount_raised }}</span>
            <span class="card-v2-goal">of {{ $startup->formatted_funding_goal }}</span>
        </div>
        <div class="card-v2-progress-wrap">
            <div class="card-v2-progress" style="width: {{ $startup->funding_percent }}%"></div>
        </div>
        <div class="card-v2-funding-stats">
            <span>{{ $startup->funding_percent }}% funded</span>
            <span>{{ $startup->investor_count }} investors</span>
            @if($startup->days_left !== null)
                <span>{{ $startup->days_left }}d left</span>
            @endif
        </div>
    </div>
    @endif
    <div class="card-v2-bottom">
        <span class="card-v2-industry"><i class="fa-solid {{ \App\Models\Startup::industryIcon($startup->industry ?? '') }}"></i> {{ $startup->industry ?? 'Uncategorized' }}</span>
        <span class="card-v2-cta">Explore <i class="fa-solid fa-arrow-right"></i></span>
    </div>
</a>