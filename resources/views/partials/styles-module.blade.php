{{-- Load one or more Vite CSS module entries. Usage: @include('partials.styles-module', ['entries' => ['resources/css/foo/bar.css']]) --}}
@foreach ($entries ?? [] as $entry)
    @vite([$entry])
@endforeach
