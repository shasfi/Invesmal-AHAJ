@php
    $manifestPath = public_path('build/manifest.json');
    $layout = $layout ?? 'dashboard';
@endphp
@if (file_exists($manifestPath))
    @php
        $viteManifest = json_decode(file_get_contents($manifestPath), true);
        $resolve = fn (string $key) => $viteManifest[$key]['file'] ?? null;
        $themeCss = $resolve('resources/css/global/theme.css');
        $dashboardSharedCss = $resolve('resources/css/components/dashboard-shared.css');
        $formsExtendedCss = $resolve('resources/css/components/forms-extended.css');
        $dashboardPartialsCss = $resolve('resources/css/dashboard/dashboard-partials.css');
        $publicSharedCss = $resolve('resources/css/components/public-shared.css');
        $navbarCss = $resolve('resources/css/components/navbar.css');
        $footerCss = $resolve('resources/css/components/footer.css');
        $dashboardJs = $resolve('resources/js/dashboard.js');
        $uiJs = $resolve('resources/js/invesmal-ui.js');
    @endphp
    @if ($themeCss)
        <link rel="stylesheet" href="{{ asset('build/' . $themeCss) }}" data-vite-fallback="theme">
    @endif
    @if ($layout === 'dashboard')
        @if ($dashboardSharedCss)
            <link rel="stylesheet" href="{{ asset('build/' . $dashboardSharedCss) }}" data-vite-fallback="dashboard-shared">
        @endif
        @if ($formsExtendedCss)
            <link rel="stylesheet" href="{{ asset('build/' . $formsExtendedCss) }}" data-vite-fallback="forms-extended">
        @endif
        @if ($dashboardPartialsCss)
            <link rel="stylesheet" href="{{ asset('build/' . $dashboardPartialsCss) }}" data-vite-fallback="dashboard-partials">
        @endif
    @else
        @if ($publicSharedCss)
            <link rel="stylesheet" href="{{ asset('build/' . $publicSharedCss) }}" data-vite-fallback="public-shared">
        @endif
        @if ($navbarCss)
            <link rel="stylesheet" href="{{ asset('build/' . $navbarCss) }}" data-vite-fallback="navbar">
        @endif
        @if ($footerCss)
            <link rel="stylesheet" href="{{ asset('build/' . $footerCss) }}" data-vite-fallback="footer">
        @endif
    @endif
    @if ($dashboardJs)
        <script src="{{ asset('build/' . $dashboardJs) }}" defer data-vite-fallback="dashboard-js"></script>
    @endif
    @if ($uiJs)
        <script src="{{ asset('build/' . $uiJs) }}" defer data-vite-fallback="ui-js"></script>
    @endif
@endif
