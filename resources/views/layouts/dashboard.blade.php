<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard — Invesmal')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite([
        'resources/css/global/theme.css',
        'resources/css/components/dashboard-shared.css',
        'resources/css/components/forms-extended.css',
        'resources/css/dashboard/dashboard-partials.css',
        'resources/js/dashboard.js',
        'resources/js/invesmal-ui.js',
    ])
    @include('partials.vite-assets', ['layout' => 'dashboard'])
    @stack('styles')
</head>
<body class="invesmal-app" style="background:var(--bg-primary);color:var(--text);font-family:var(--font-sans);min-height:100vh;display:flex;">

    @php
        $user = auth()->user();
        $role = $user?->role;
        $navItems = match($role) {
            \App\Models\User::ROLE_ADMIN => [
                ['route' => 'dashboard.admin', 'icon' => 'fa-solid fa-chart-pie', 'label' => 'Dashboard'],
                ['route' => 'admin.verification.index', 'icon' => 'fa-solid fa-circle-check', 'label' => 'Verification'],
                ['route' => 'admin.moderation.index', 'icon' => 'fa-solid fa-shield-halved', 'label' => 'Moderation'],
                ['route' => 'admin.activity-logs.index', 'icon' => 'fa-solid fa-clock-rotate-left', 'label' => 'Activity Logs'],
                ['route' => 'startups.index', 'icon' => 'fa-solid fa-rocket', 'label' => 'Manage Startups'],
                ['route' => 'users.index', 'icon' => 'fa-solid fa-users-gear', 'label' => 'Manage Users'],
                ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
                ['route' => 'ai.insights', 'icon' => 'fa-solid fa-robot', 'label' => 'AI Insights'],
            ],
            \App\Models\User::ROLE_STUDENT_FOUNDER => [
                ['route' => 'dashboard', 'icon' => 'fa-solid fa-lightbulb', 'label' => 'My Dashboard'],
                ['route' => 'startups.index', 'icon' => 'fa-solid fa-rocket', 'label' => 'My Startups'],
                ['route' => 'users.investors', 'icon' => 'fa-solid fa-handshake', 'label' => 'My Investors'],
                ['route' => 'pitch_decks.index', 'icon' => 'fa-solid fa-file-powerpoint', 'label' => 'Pitch Decks'],
                ['route' => 'investments.index', 'icon' => 'fa-solid fa-hand-holding-dollar', 'label' => 'Investments'],
                ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
                ['route' => 'ai.sentiment.index', 'icon' => 'fa-solid fa-face-smile', 'label' => 'Sentiment'],
                ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
                ['route' => 'documents.index', 'icon' => 'fa-solid fa-folder-open', 'label' => 'Documents'],
                ['route' => 'ai.insights', 'icon' => 'fa-solid fa-chart-line', 'label' => 'AI Insights'],
                ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
            ],
            \App\Models\User::ROLE_INVESTOR => [
                ['route' => 'dashboard', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Dashboard'],
                ['route' => 'startups.discover', 'icon' => 'fa-solid fa-magnifying-glass-dollar', 'label' => 'Discover'],
                ['route' => 'users.founders', 'icon' => 'fa-solid fa-people-group', 'label' => 'Founders'],
                ['route' => 'investments.index', 'icon' => 'fa-solid fa-hand-holding-dollar', 'label' => 'Investments'],
                ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
                ['route' => 'ai.sentiment.index', 'icon' => 'fa-solid fa-face-smile', 'label' => 'Sentiment'],
                ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
                ['route' => 'ai.insights', 'icon' => 'fa-solid fa-robot', 'label' => 'AI Insights'],
                ['route' => 'reports.index', 'icon' => 'fa-solid fa-file-lines', 'label' => 'Reports'],
            ],
            \App\Models\User::ROLE_MENTOR => [
                ['route' => 'dashboard', 'icon' => 'fa-solid fa-chalkboard-user', 'label' => 'Dashboard'],
                ['route' => 'startups.discover', 'icon' => 'fa-solid fa-seedling', 'label' => 'Startups'],
                ['route' => 'conversations.index', 'icon' => 'fa-solid fa-comments', 'label' => 'Messages'],
                ['route' => 'ai.sentiment.index', 'icon' => 'fa-solid fa-face-smile', 'label' => 'Sentiment'],
                ['route' => 'meetings.index', 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Meetings'],
                ['route' => 'ai.insights', 'icon' => 'fa-solid fa-chart-line', 'label' => 'AI Insights'],
            ],
            default => [
                ['route' => 'dashboard', 'icon' => 'fa-solid fa-house', 'label' => 'Dashboard'],
            ],
        };
    @endphp

    <div id="sidebar-overlay" onclick="toggleSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:90;transition:opacity 0.3s ease;opacity:0;"></div>

    <aside id="sidebar" style="width:260px;min-height:100vh;background:linear-gradient(180deg, rgba(12,18,16,0.98) 0%, rgba(10,14,12,0.95) 100%);border-right:1px solid rgba(127,163,154,0.12);display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;z-index:100;transition:transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);overflow-y:auto;">
        <div style="padding:1.5rem 1.25rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(127,163,154,0.12);position:relative;">
            <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none;color:var(--text);transition:transform 0.2s ease;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--accent-soft));border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;box-shadow:var(--glow-primary);transition:box-shadow 0.3s ease;" onmouseover="this.style.boxShadow='0 0 30px rgba(127,163,154,0.5)'" onmouseout="this.style.boxShadow='var(--glow-primary)'">
                    <span style="font-weight:700;font-size:1rem;">I</span>
                </div>
                <span style="font-size:1.25rem;font-weight:700;letter-spacing:-0.02em;">Invesmal</span>
            </a>
            <button onclick="toggleSidebar()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1.1rem;display:none;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;" id="sidebar-close-btn" onmouseover="this.style.background='var(--surface)';this.style.color='var(--text)'" onmouseout="this.style.background='transparent';this.style.color='var(--muted)'"><i class="fa-solid fa-times"></i></button>
        </div>
        <nav style="flex:1;padding:1rem 0.75rem;">
            @foreach($navItems as $index => $item)
                <a href="{{ route($item['route']) }}" class="sidebar-nav-link" style="display:flex;align-items:center;gap:0.875rem;padding:0.75rem 1rem;border-radius:var(--radius-md);color:{{ request()->routeIs($item['route']) ? 'var(--text)' : 'var(--text-secondary)' }};text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);margin-bottom:0.25rem;position:relative;{{ request()->routeIs($item['route']) ? 'background:linear-gradient(135deg, var(--primary), var(--primary-light));box-shadow:var(--glow-primary);' : '' }}" data-index="{{ $index }}">
                    <i class="{{ $item['icon'] }}" style="width:22px;text-align:center;transition:transform 0.2s ease;"></i>
                    <span style="position:relative;z-index:1;">{{ $item['label'] }}</span>
                    @if(request()->routeIs($item['route']))
                        <style>
                            .sidebar-nav-link[data-index="{{ $index }}"]::before {
                                content: '';
                                position: absolute;
                                inset: 0;
                                background: linear-gradient(135deg, rgba(127,163,154,0.1), transparent);
                                border-radius: var(--radius-md);
                                animation: pulse-border 2s ease-in-out infinite;
                            }
                            @keyframes pulse-border {
                                0%, 100% { opacity: 0.5; }
                                50% { opacity: 1; }
                            }
                        </style>
                    @endif
                </a>
            @endforeach
        </nav>
        @if($user)
        <div style="padding:1rem 0.75rem;border-top:1px solid rgba(127,163,154,0.12);">
            <a href="{{ route('users.profile', $user) }}" style="display:flex;align-items:center;gap:0.75rem;text-decoration:none;color:var(--text);margin-bottom:0.75rem;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid rgba(127,163,154,0.3);transition:border-color 0.2s ease;" onmouseover="this.style.borderColor='var(--accent-soft)'" onmouseout="this.style.borderColor='rgba(127,163,154,0.3)'">
                <div>
                    <div style="font-size:0.875rem;font-weight:600;">{{ $user->name }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="sidebar-logout-btn" style="display:flex;align-items:center;gap:0.625rem;width:100%;padding:0.625rem 0.875rem;background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:0.875rem;border-radius:var(--radius-md);transition:all 0.2s ease;" onmouseover="this.style.background='rgba(180,106,106,0.1)';this.style.color='var(--danger)'" onmouseout="this.style.background='transparent';this.style.color='var(--text-secondary)'"><i class="fa-solid fa-arrow-right-from-bracket" style="width:22px;text-align:center;transition:transform 0.2s ease;" onmouseover="this.style.transform='translateX(3px)'" onmouseout="this.style.transform='translateX(0)'"></i> <span>Logout</span></button>
            </form>
        </div>
        @endif
    </aside>

    <div style="margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;transition:margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">
        <nav style="background:rgba(10,14,12,0.85);backdrop-filter:blur(24px);border-bottom:1px solid rgba(127,163,154,0.12);padding:0.75rem 1.75rem;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:50;transition:all 0.3s ease;">
            <button onclick="toggleSidebar()" style="display:none;background:none;border:none;color:var(--text);cursor:pointer;font-size:1.25rem;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;" id="hamburger-btn" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-bars"></i></button>
            <div style="display:flex;align-items:center;gap:0.875rem;margin-left:auto;">
                <button id="theme-toggle" onclick="toggleTheme()" style="background:none;border:none;color:var(--text-secondary);font-size:1.1rem;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;cursor:pointer;" title="Toggle theme" onmouseover="this.style.color='var(--accent-soft)';this.style.background='var(--surface)'" onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                    <i class="fa-solid fa-moon" id="theme-icon"></i>
                </button>
                <a href="{{ route('notification.preferences.edit') }}" class="topbar-icon-btn" style="color:var(--text-secondary);font-size:1.1rem;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;" title="Notification preferences" onmouseover="this.style.color='var(--accent-soft)';this.style.background='var(--surface)'" onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'"><i class="fa-solid fa-gear"></i></a>
                <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" style="position:relative;color:var(--text-secondary);font-size:1.1rem;padding:0.5rem;border-radius:var(--radius-md);transition:all 0.2s ease;" title="Notifications" onmouseover="this.style.color='var(--accent-soft)';this.style.background='var(--surface)'" onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                    <i class="fa-solid fa-bell"></i>
                    <span id="notification-badge" style="position:absolute;top:-4px;right:-4px;background:linear-gradient(135deg, var(--danger), #b46a6a);color:white;font-size:0.65rem;width:18px;height:18px;border-radius:50%;display:none;align-items:center;justify-content:center;font-weight:600;box-shadow:0 2px 8px rgba(180,106,106,0.4);">0</span>
                </a>
            </div>
        </nav>
        <div style="max-width:1200px;margin:0 auto;width:100%;padding:1.5rem 1.75rem 0;">
            @if(session('status'))
                <div style="padding:0.875rem 1.125rem;background:rgba(92,143,106,0.15);border:1px solid rgba(92,143,106,0.25);border-radius:var(--radius-md);color:var(--success);margin-bottom:1rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.5rem;animation:slideInDown 0.3s ease-out;"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
            @endif
            @if(session('success'))
                <div style="padding:0.875rem 1.125rem;background:rgba(92,143,106,0.15);border:1px solid rgba(92,143,106,0.25);border-radius:var(--radius-md);color:var(--success);margin-bottom:1rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.5rem;animation:slideInDown 0.3s ease-out;"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="padding:0.875rem 1.125rem;background:rgba(180,106,106,0.15);border:1px solid rgba(180,106,106,0.25);border-radius:var(--radius-md);color:var(--danger);margin-bottom:1rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.5rem;animation:slideInDown 0.3s ease-out;"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div style="padding:0.875rem 1.125rem;background:rgba(180,106,106,0.15);border:1px solid rgba(180,106,106,0.25);border-radius:var(--radius-md);color:var(--danger);margin-bottom:1rem;font-size:0.9rem;font-weight:500;display:flex;align-items:center;gap:0.5rem;animation:slideInDown 0.3s ease-out;"><i class="fa-solid fa-circle-exclamation"></i> @foreach($errors->all() as $error){{ $error }}@if(!$loop->last), @endif @endforeach</div>
            @endif
        </div>
        <main style="max-width:1200px;margin:0 auto;width:100%;padding:0 1.75rem 3rem;flex:1;" class="page-transition-enter">
            @yield('content')
        </main>
    </div>

    <style>
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .sidebar-nav-link:not([style*="background:linear-gradient"]):hover {
            background: var(--surface);
            color: var(--text);
            transform: translateX(4px);
        }
        .sidebar-nav-link:not([style*="background:linear-gradient"]):hover i {
            transform: scale(1.1);
            color: var(--accent-soft);
        }
    </style>

    <script>
        function toggleSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('sidebar-overlay');
            const m = document.querySelector('[style*="margin-left"]');
            const isMobile = window.innerWidth <= 768;
            
            if (s.style.transform === 'translateX(0px)' || !s.style.transform) {
                s.style.transform = 'translateX(-260px)';
                if (o) {
                    o.style.display = 'block';
                    if (isMobile) {
                        setTimeout(() => o.style.opacity = '1', 10);
                    }
                }
                if (m) m.style.marginLeft = '0px';
            } else {
                s.style.transform = 'translateX(0px)';
                if (o) {
                    o.style.opacity = '0';
                    setTimeout(() => o.style.display = 'none', 300);
                }
                if (m) m.style.marginLeft = '260px';
            }
        }
        
        function handleResize() {
            const h = document.getElementById('hamburger-btn');
            const c = document.getElementById('sidebar-close-btn');
            const s = document.getElementById('sidebar');
            const m = document.querySelector('[style*="margin-left"]');
            const o = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth <= 768) {
                if (h) h.style.display = 'flex';
                if (c) c.style.display = 'block';
                if (s) s.style.transform = 'translateX(-260px)';
                if (m) m.style.marginLeft = '0px';
            } else {
                if (h) h.style.display = 'none';
                if (c) c.style.display = 'none';
                if (s) s.style.transform = 'translateX(0px)';
                if (m) m.style.marginLeft = '260px';
                if (o) {
                    o.style.display = 'none';
                    o.style.opacity = '0';
                }
            }
        }
        
        // Close sidebar when clicking overlay on mobile
        document.getElementById('sidebar-overlay')?.addEventListener('click', function() {
            const s = document.getElementById('sidebar');
            const m = document.querySelector('[style*="margin-left"]');
            s.style.transform = 'translateX(-260px)';
            this.style.opacity = '0';
            setTimeout(() => this.style.display = 'none', 300);
            if (m) m.style.marginLeft = '0px';
        });
        
        window.addEventListener('resize', handleResize);
        handleResize();

        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            if (newTheme === 'light') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }

        // Initialize theme from localStorage
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const themeIcon = document.getElementById('theme-icon');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                if (savedTheme === 'light') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>