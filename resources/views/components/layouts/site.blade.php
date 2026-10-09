@props(['title' => null, 'description' => null, 'page' => null, 'projects' => null])
@php
    $fullTitle = $title ? $title.' — Sparklenz' : 'Sparklenz — Beyond the Frame';
    $metaDescription = $description ?: site_setting('meta_description', 'Sparklenz — creative studio in Sri Lanka.');
    $canonical = url()->current();
@endphp
<!DOCTYPE html>
<html lang="en-LK">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="theme-color" content="#F3F4F4">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Sparklenz">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ asset('images/brand/og-image.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    @unless (app()->isProduction())
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preload" href="{{ asset('fonts/manrope-600.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/dmsans-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preconnect" href="https://i.ytimg.com">
    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="site" data-page="{{ $page }}">
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.header', ['page' => $page])

    <main id="main" tabindex="-1">
        {{ $slot }}
    </main>

    @include('partials.footer')

    @if ($projects)
        @include('partials.screening-room')
        <script type="application/json" id="project-data">{!! json_encode($projects->map->toScreeningPayload()->values(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif
</body>
</html>
