{{-- Small floating drone illustration for the home hero (decorative). --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 220 160', 'fill' => 'none', 'aria-hidden' => 'true', 'focusable' => 'false']) }}>
    <defs>
        <linearGradient id="drone-body" x1="70" y1="50" x2="150" y2="110" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#FFFFFF"/>
            <stop offset=".6" stop-color="#EBD5DE"/>
            <stop offset="1" stop-color="#CFA5B7"/>
        </linearGradient>
        <linearGradient id="drone-arm" x1="20" y1="40" x2="200" y2="70" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#E7CBD6"/>
            <stop offset=".5" stop-color="#F7EEF2"/>
            <stop offset="1" stop-color="#E0BFCD"/>
        </linearGradient>
        <radialGradient id="drone-lens" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(106 116) scale(16)">
            <stop offset="0" stop-color="#9B5B86"/>
            <stop offset="1" stop-color="#2A1224"/>
        </radialGradient>
        <linearGradient id="drone-light" x1="96" y1="76" x2="124" y2="80" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#B95A7E"/>
            <stop offset="1" stop-color="#853953"/>
        </linearGradient>
    </defs>

    {{-- Arms and motors --}}
    <path d="M78 74 34 50M142 74l44-24" stroke="url(#drone-arm)" stroke-width="12" stroke-linecap="round"/>
    <rect x="22" y="40" width="26" height="18" rx="7" fill="#D9B5C5"/>
    <rect x="172" y="40" width="26" height="18" rx="7" fill="#D9B5C5"/>

    {{-- Propellers --}}
    <g class="drone__prop" style="transform-origin: 35px 38px">
        <ellipse cx="35" cy="38" rx="32" ry="5" fill="#612D53" opacity=".22"/>
        <ellipse cx="35" cy="38" rx="22" ry="2.5" fill="#853953" opacity=".35"/>
    </g>
    <g class="drone__prop drone__prop--reverse" style="transform-origin: 185px 38px">
        <ellipse cx="185" cy="38" rx="32" ry="5" fill="#612D53" opacity=".22"/>
        <ellipse cx="185" cy="38" rx="22" ry="2.5" fill="#853953" opacity=".35"/>
    </g>
    <rect x="32" y="36" width="6" height="8" rx="3" fill="#853953"/>
    <rect x="182" y="36" width="6" height="8" rx="3" fill="#853953"/>

    {{-- Body --}}
    <path d="M66 82c0-20 20-34 44-34s44 14 44 34-20 26-44 26-44-6-44-26Z" fill="url(#drone-body)"/>
    <path d="M80 66c8-8 20-12 32-12 10 0 20 3 27 9" stroke="#FFFFFF" stroke-width="5" stroke-linecap="round" opacity=".85"/>
    <rect x="96" y="74" width="28" height="7" rx="3.5" fill="url(#drone-light)"/>

    {{-- Gimbal camera --}}
    <rect x="102" y="100" width="16" height="8" rx="3" fill="#C99AAE"/>
    <circle cx="110" cy="118" r="16" fill="#EBD5DE"/>
    <circle cx="110" cy="118" r="11" fill="url(#drone-lens)"/>
    <circle cx="106" cy="114" r="3" fill="#FFFFFF" opacity=".7"/>
</svg>
