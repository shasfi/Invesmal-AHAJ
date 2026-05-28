@php
    $groups = $startupsByIndustry ?? [];
    $total = $totalCount ?? collect($groups)->sum(fn ($items) => count($items));
@endphp

@if(empty($groups) || $total === 0)
    <div class="empty-state">
        <i class="fa-solid fa-rocket empty-icon"></i>
        <h3>No startups found</h3>
        <p>Try adjusting your search or filters.</p>
    </div>
@else
    @foreach($groups as $category => $categoryStartups)
        <section class="industry-section" id="cat-{{ Str::slug($category) }}">
            <div class="industry-section-header">
                <h3 class="industry-section-title">
                    <i class="fa-solid {{ \App\Models\Startup::industryIcon($category) }}"></i>
                    {{ $category }}
                </h3>
                <span class="industry-section-count">{{ $categoryStartups->count() }} {{ Str::plural('startup', $categoryStartups->count()) }}</span>
            </div>
            <div class="startup-grid">
                @foreach($categoryStartups as $startup)
                    @include('startups.partials.startup-card', ['startup' => $startup])
                @endforeach
            </div>
        </section>
    @endforeach
@endif