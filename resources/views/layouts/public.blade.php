<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Invesmal')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite([
        'resources/css/global/theme.css',
        'resources/css/components/public-shared.css',
        'resources/css/components/navbar.css',
        'resources/css/components/footer.css',
        'resources/js/dashboard.js',
        'resources/js/invesmal-ui.js',
    ])
    @include('partials.vite-assets', ['layout' => 'public'])

    @stack('styles')
</head>
<body class="invesmal-app public-root" style="background:var(--bg-primary);color:var(--text);font-family:var(--font-sans);min-height:100vh">

    {{-- Shared Public Navbar --}}
    @include('partials.public-navbar')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Shared Public Footer --}}
    @include('partials.public-footer')

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>