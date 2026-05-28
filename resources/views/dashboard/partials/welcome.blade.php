@php
$quickLinks = match($role) {
    'admin' => [
        ['route' => 'admin.verification.index', 'icon' => 'fa-circle-check', 'label' => 'Verification'],
        ['route' => 'startups.index', 'icon' => 'fa-rocket', 'label' => 'Startups'],
        ['route' => 'users.index', 'icon' => 'fa-users-gear', 'label' => 'Users'],
        ['route' => 'reports.index', 'icon' => 'fa-file-lines', 'label' => 'Reports'],
        ['route' => 'ai.insights', 'icon' => 'fa-robot', 'label' => 'AI Insights'],
    ],
    'student_founder' => [
        ['route' => 'startups.create', 'icon' => 'fa-plus', 'label' => 'New Startup'],
        ['route' => 'pitch_decks.index', 'icon' => 'fa-file-powerpoint', 'label' => 'Pitch Decks'],
        ['route' => 'investments.index', 'icon' => 'fa-hand-holding-dollar', 'label' => 'Investments'],
        ['route' => 'conversations.index', 'icon' => 'fa-comments', 'label' => 'Messages'],
        ['route' => 'meetings.index', 'icon' => 'fa-calendar-check', 'label' => 'Meetings'],
    ],
    'investor' => [
        ['route' => 'startups.discover', 'icon' => 'fa-magnifying-glass-dollar', 'label' => 'Discover'],
        ['route' => 'investments.index', 'icon' => 'fa-hand-holding-dollar', 'label' => 'Investments'],
        ['route' => 'ai.insights', 'icon' => 'fa-chart-line', 'label' => 'AI Insights'],
        ['route' => 'conversations.index', 'icon' => 'fa-comments', 'label' => 'Messages'],
        ['route' => 'reports.index', 'icon' => 'fa-file-lines', 'label' => 'Reports'],
    ],
    'mentor' => [
        ['route' => 'startups.discover', 'icon' => 'fa-seedling', 'label' => 'Startups'],
        ['route' => 'meetings.index', 'icon' => 'fa-calendar-check', 'label' => 'Meetings'],
        ['route' => 'conversations.index', 'icon' => 'fa-comments', 'label' => 'Messages'],
        ['route' => 'ai.sentiment.index', 'icon' => 'fa-face-smile', 'label' => 'Sentiment'],
        ['route' => 'ai.insights', 'icon' => 'fa-chart-line', 'label' => 'AI Insights'],
    ],
    default => [],
};
$about = match($role) {
    'admin' => 'Manage users, verify startups, monitor investments, and view platform analytics.',
    'student_founder' => 'Create your startup profile, generate AI pitch decks, connect with investors, and track funding.',
    'investor' => 'Discover startups, open profiles, submit investment offers, track founder approval, then message or meet to close the deal.',
    'mentor' => 'Guide founders, review startups by category, join meetings, and analyze conversation sentiment.',
    default => 'Welcome to Invesmal.',
};
@endphp

<div class="dashboard-welcome modern-card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:flex-start;justify-content:space-between;">
            <div style="flex:1;min-width:260px;">
                <p style="font-size:0.75rem;font-weight:700;color:var(--accent-soft);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.5rem;">Invesmal — FYP Platform</p>
                <h2 style="font-size:1.35rem;font-weight:700;margin-bottom:0.5rem;">Your {{ $roleLabel }} workspace</h2>
                <p style="color:var(--text-secondary);font-size:0.95rem;line-height:1.6;max-width:520px;">{{ $about }}</p>
            </div>
            <div class="quick-links-grid">
                @foreach($quickLinks as $link)
                    <a href="{{ route($link['route']) }}" class="quick-link-card">
                        <i class="fa-solid {{ $link['icon'] }}"></i>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
