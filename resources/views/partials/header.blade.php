@php
    $links = [
        'home' => 'Home',
        'work' => 'Our work',
        'services' => 'Services',
        'about' => 'About',
        'contact' => 'Contact',
    ];
@endphp
<header class="site-header" data-site-header x-data="{ open: false }" @keydown.escape.window="open = false">
    <nav class="nav wrap" aria-label="Main">
        <a class="brand" href="{{ route('home') }}" aria-label="Sparklenz — home">
            <img src="{{ asset('images/brand/logo-dark.png') }}" alt="Sparklenz — Beyond the Frame" width="812" height="196">
        </a>
        <div class="nav-links">
            @foreach ($links as $name => $label)
                <a href="{{ route($name) }}" @if ($page === $name) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </div>
        <div class="nav-actions">
            <a class="btn btn--berry btn--sm" href="{{ route('contact') }}">Start a project <x-icon name="arrow" /></a>
            <button class="menu-toggle" type="button" aria-controls="mobile-menu" :aria-expanded="open.toString()" aria-expanded="false" @click="open = true">
                <x-icon name="menu" />
                <span class="sr-only">Open menu</span>
            </button>
        </div>
    </nav>

    <template x-teleport="body">
    <div class="mobile-menu on-dark" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu"
         x-show="open" x-cloak x-trap.noscroll.inert="open"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="mobile-menu__top">
            <a class="brand" href="{{ route('home') }}" aria-label="Sparklenz — home">
                <img src="{{ asset('images/brand/logo-light.png') }}" alt="Sparklenz — Beyond the Frame" width="803" height="186">
            </a>
            <button class="icon-btn" type="button" @click="open = false">
                <x-icon name="close" />
                <span class="sr-only">Close menu</span>
            </button>
        </div>
        <nav aria-label="Mobile">
            @foreach ($links as $name => $label)
                <a href="{{ route($name) }}" @if ($page === $name) aria-current="page" @endif><span>0{{ $loop->iteration }}</span>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="mobile-menu__foot">
            @if ($email = site_setting('email'))
                <a href="mailto:{{ $email }}">{{ $email }}</a>
            @endif
            @if ($phone = site_setting('phone_display'))
                <a href="tel:{{ site_setting('phone_link') }}">{{ $phone }}</a>
            @endif
        </div>
    </div>
    </template>
</header>
