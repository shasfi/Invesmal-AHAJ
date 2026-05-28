@php
    $user = auth()->user();
    $role = $user?->role;

    $navItems = [
        'admin' => [
            ['route' => 'dashboard.admin', 'icon' => 'fa-solid fa-chart-pie', 'label' => 'Dashboard'],
            ['route' => 'users.index', 'icon' => 'fa-solid fa-users-gear', 'label' => 'Manage Users'],
            ['route' => 'startups.index', 'icon' => 'fa-solid fa-rocket', 'label' => 'Manage Startups'],
            ['route' => 'admin.verification.index', 'icon' => 'fa-solid fa-circle-check', 'label' => 'Verification'],
            ['route' => 'admin.moderation.index', 'icon' => 'fa-solid fa-shield-halved', 'label' => 'Moderation'],
            ['route' => 'admin.monitoring.index', 'icon' => 'fa-solid fa-chart-simple', 'label' => 'Monitoring'],
            ['route' => 'admin.activity-logs.index', 'icon' => 'fa-solid fa-clock-rotate-left', 'label' => 'Activity Logs'],
        ],
        'student_founder' => [
            ['route' => 'dashboard.founder', 'icon' => 'fa-solid fa-lightbulb', 'label' => 'My Dashboard'],
            ['route' => 'startups.index', 'icon' => 'fa-solid fa-rocket', 'label' => 'My Startups'],
            ['route' => 'pitch_decks.index', 'icon' => 'fa-solid fa-file-powerpoint', 'label' => 'Pitch Decks'],
            ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
            ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
            ['route' => 'documents.index', 'icon' => 'fa-solid fa-folder-open', 'label' => 'Documents'],
            ['route' => 'investments.index', 'icon' => 'fa-solid fa-hand-holding-dollar', 'label' => 'Investments'],
        ],
        'investor' => [
            ['route' => 'dashboard.investor', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Dashboard'],
            ['route' => 'startups.discover', 'icon' => 'fa-solid fa-magnifying-glass-dollar', 'label' => 'Discover'],
            ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
            ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
            ['route' => 'investments.index', 'icon' => 'fa-solid fa-hand-holding-dollar', 'label' => 'Investments'],
        ],
        'mentor' => [
            ['route' => 'dashboard.mentor', 'icon' => 'fa-solid fa-chalkboard-user', 'label' => 'Dashboard'],
            ['route' => 'startups.discover', 'icon' => 'fa-solid fa-seedling', 'label' => 'Startups'],
            ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
            ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
        ]
    ];
    $currentNav = $navItems[$role] ?? [];
@endphp

<aside id="sidebar" class="sidebar">
    {{-- Header --}}
    <div class="sidebar-header">
        <div class="sidebar-logo-icon"></div>
        <h2 class="sidebar-logo-text">Invesmal</h2>
        <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Toggle sidebar">
            <i class="fa-solid fa-angles-left"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <ul class="sidebar-nav-list">
            @foreach($currentNav as $item)
                <li>
                    <a href="{{ route($item['route']) }}" class="sidebar-nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }} nav-icon"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Footer / User Profile --}}
    @if($user)
    <div class="sidebar-footer">
        <a href="{{ route('users.profile', $user) }}" class="user-profile-link">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar">
            <div class="user-info">
                <span class="user-name">{{ $user->name }}</span>
                <span class="user-role">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            </div>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button id="logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-button">
            <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i>
            <span>Logout</span>
        </button>
    </div>
    @endif
</aside>
