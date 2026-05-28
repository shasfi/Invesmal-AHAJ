{{-- Shared Public Navigation Bar --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">
        <a href="{{ auth()->check() ? route('dashboard') : route('landing') }}" class="pub-nav-brand">
            <div class="pub-nav-brand-icon"></div>
            <span class="pub-nav-brand-name">Invesmal</span>
        </a>
        <div class="pub-nav-links">
            @auth
                <a href="{{ route('dashboard') }}" class="pub-nav-link"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <a href="{{ route('startups.discover') }}" class="pub-nav-link">Discover</a>
                <a href="{{ route('users.profile', auth()->user()) }}" class="pub-nav-link">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0;">
                    @csrf
                    <button type="submit" class="pub-nav-logout">Logout</button>
                </form>
            @else
                <a href="{{ route('startups.discover') }}" class="pub-nav-link">Discover</a>
                <a href="{{ route('login') }}" class="pub-nav-link">Sign In</a>
                <a href="{{ route('register') }}" class="pub-nav-cta">Get Started</a>
            @endauth
        </div>
    </div>
</nav>
