@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — Sparklenz admin</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-paper font-sans text-ink antialiased">
    <a href="#admin-main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-ink focus:px-4 focus:py-2 focus:text-paper">Skip to content</a>
    @auth
        <div class="lg:flex">
            <aside class="border-b border-ink/10 bg-ink text-paper lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:flex-none lg:border-0">
                <div class="flex items-center justify-between gap-4 px-5 py-4 lg:block lg:px-6 lg:py-8">
                    <a href="{{ route('admin.dashboard') }}" class="block w-32 lg:w-40">
                        <img src="{{ asset('images/brand/logo-light.png') }}" alt="Sparklenz admin" width="803" height="186">
                    </a>
                    <a href="{{ route('home') }}" class="text-sm text-paper/70 underline-offset-4 hover:underline lg:mt-3 lg:inline-block" target="_blank" rel="noopener">View site ↗</a>
                </div>
                @php
                    $nav = [
                        ['admin.dashboard', 'Dashboard', 'admin.dashboard'],
                        ['admin.projects.index', 'Work', 'admin.projects.*'],
                        ['admin.services.index', 'Services & rates', 'admin.services.*'],
                        ['admin.packages.index', 'Packages', 'admin.packages.*'],
                        ['admin.team.index', 'Team', 'admin.team.*'],
                        ['admin.copy.edit', 'Page copy', 'admin.copy.*'],
                        ['admin.settings.edit', 'Contact & social', 'admin.settings.*'],
                        ['admin.enquiries.index', 'Enquiries', 'admin.enquiries.*'],
                    ];
                    $newCount = \App\Models\Enquiry::where('status', 'new')->count();
                @endphp
                <nav aria-label="Admin" class="flex gap-1 overflow-x-auto px-3 pb-3 text-sm lg:flex-col lg:px-3">
                    @foreach ($nav as [$route, $label, $pattern])
                        <a href="{{ route($route) }}" @class([
                            'flex items-center justify-between gap-3 whitespace-nowrap rounded-lg px-3 py-2.5 transition',
                            'bg-paper/12 font-semibold text-paper' => request()->routeIs($pattern),
                            'text-paper/75 hover:bg-paper/8 hover:text-paper' => ! request()->routeIs($pattern),
                        ]) @if (request()->routeIs($pattern)) aria-current="page" @endif>
                            {{ $label }}
                            @if ($route === 'admin.enquiries.index' && $newCount)
                                <span class="rounded-full bg-berry px-2 py-0.5 text-xs font-bold text-paper">{{ $newCount }}<span class="sr-only"> new</span></span>
                            @endif
                        </a>
                    @endforeach
                    <form method="POST" action="{{ route('admin.logout') }}" class="lg:mt-6">
                        @csrf
                        <button class="w-full whitespace-nowrap rounded-lg px-3 py-2.5 text-left text-paper/75 transition hover:bg-paper/8 hover:text-paper">Sign out</button>
                    </form>
                </nav>
            </aside>
            <main id="admin-main" class="min-w-0 flex-1 px-5 py-8 lg:px-12 lg:py-10">
                <x-admin.flash />
                {{ $slot }}
            </main>
        </div>
    @else
        <main id="admin-main">{{ $slot }}</main>
    @endauth
</body>
</html>
