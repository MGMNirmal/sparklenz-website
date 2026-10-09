@props(['name'])
@php
    $paths = [
        'arrow' => '<path d="M6 18 18 6M8 6h10v10"/>',
        'right' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'left' => '<path d="M20 12H4m6-6-6 6 6 6"/>',
        'down' => '<path d="M12 4v16m-6-6 6 6 6-6"/>',
        'up' => '<path d="M12 20V4m-6 6 6-6 6 6"/>',
        'play' => '<path d="M8 5.5v13a.5.5 0 0 0 .77.42l10.2-6.5a.5.5 0 0 0 0-.84L8.77 5.08A.5.5 0 0 0 8 5.5Z" fill="currentColor" stroke="none"/>',
        'pause' => '<path d="M8 5v14M16 5v14"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'menu' => '<path d="M4 9h16M4 15h16"/>',
        'check' => '<path d="m5 12.5 4.2 4.2L19 7"/>',
        'image' => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="m4 17 5-5 4 4 3-3 4 4"/><circle cx="15.5" cy="9" r="1.5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
        'whatsapp' => '<path d="M20 11.5a8 8 0 0 1-11.7 7.1L4 20l1.4-4.2A8 8 0 1 1 20 11.5Z"/><path d="M9 8.6c.8 2.9 2.3 4.4 5.2 5.4l1.2-1.4-1.9-1-.9.9c-1.2-.6-1.9-1.3-2.5-2.5l.9-.9-1-1.9Z"/>',
        'spark' => '<path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4"/>',
        'film' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M17 9h4M3 15h4M17 15h4"/>',
    ];
@endphp
<svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.6', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true', 'focusable' => 'false']) }}>{!! $paths[$name] ?? '' !!}</svg>
