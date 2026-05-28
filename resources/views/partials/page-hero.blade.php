@props(['title', 'subtitle' => null, 'icon' => 'fa-layer-group'])

<div class="page-hero">
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;">
        <div>
            <h1 class="page-hero__title">
                @if($icon)<i class="fa-solid {{ $icon }}" style="color:var(--accent-soft);margin-right:0.5rem;font-size:0.9em;"></i>@endif
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="page-hero__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        @if(trim($slot))
            <div class="page-hero__actions">{{ $slot }}</div>
        @endif
    </div>
</div>
