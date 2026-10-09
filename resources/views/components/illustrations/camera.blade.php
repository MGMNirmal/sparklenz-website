{{-- Glossy camera illustration for the home hero (decorative). --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 360 320', 'fill' => 'none', 'aria-hidden' => 'true', 'focusable' => 'false']) }}>
    <defs>
        <linearGradient id="cam-body" x1="40" y1="70" x2="320" y2="270" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#FDF8FA"/>
            <stop offset=".55" stop-color="#EEDCE3"/>
            <stop offset="1" stop-color="#D5B0C0"/>
        </linearGradient>
        <linearGradient id="cam-top" x1="120" y1="36" x2="230" y2="100" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#FFFFFF"/>
            <stop offset="1" stop-color="#E3C7D2"/>
        </linearGradient>
        <linearGradient id="cam-grip" x1="20" y1="80" x2="80" y2="260" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#9A4A68"/>
            <stop offset="1" stop-color="#612D53"/>
        </linearGradient>
        <linearGradient id="cam-ring" x1="100" y1="80" x2="260" y2="260" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#FBF1F5"/>
            <stop offset=".6" stop-color="#D8AFC0"/>
            <stop offset="1" stop-color="#B9849C"/>
        </linearGradient>
        <linearGradient id="cam-barrel" x1="110" y1="100" x2="250" y2="240" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#8E4466"/>
            <stop offset="1" stop-color="#3E1C36"/>
        </linearGradient>
        <radialGradient id="cam-glass" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(166 154) rotate(50) scale(74)">
            <stop offset="0" stop-color="#9B5B86"/>
            <stop offset=".45" stop-color="#4B2042"/>
            <stop offset="1" stop-color="#1F0E1B"/>
        </radialGradient>
        <radialGradient id="cam-glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(196 196) rotate(90) scale(30)">
            <stop offset="0" stop-color="#E7A3BE" stop-opacity=".9"/>
            <stop offset="1" stop-color="#E7A3BE" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="cam-button" x1="262" y1="44" x2="300" y2="70" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#B95A7E"/>
            <stop offset="1" stop-color="#853953"/>
        </linearGradient>
        <radialGradient id="cam-shadow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(180 300) scale(150 16)">
            <stop offset="0" stop-color="#612D53" stop-opacity=".28"/>
            <stop offset="1" stop-color="#612D53" stop-opacity="0"/>
        </radialGradient>
    </defs>

    <ellipse cx="180" cy="300" rx="150" ry="16" fill="url(#cam-shadow)"/>

    {{-- Top plate and shutter button --}}
    <rect x="112" y="34" width="124" height="62" rx="22" fill="url(#cam-top)"/>
    <rect x="148" y="46" width="52" height="18" rx="9" fill="#612D53" opacity=".18"/>
    <rect x="262" y="44" width="40" height="26" rx="11" fill="url(#cam-button)"/>
    <rect x="268" y="47" width="22" height="6" rx="3" fill="#FFFFFF" opacity=".45"/>

    {{-- Body --}}
    <rect x="22" y="72" width="316" height="196" rx="46" fill="url(#cam-body)"/>
    <path d="M68 74h224c20 0 36 10 42 26-60-10-190-12-308-2 8-15 24-24 42-24Z" fill="#FFFFFF" opacity=".7"/>
    <path d="M22 132c0-30 12-52 34-58v192c-22-6-34-28-34-58V132Z" fill="url(#cam-grip)" opacity=".9"/>
    <rect x="62" y="96" width="46" height="24" rx="9" fill="#FFFFFF"/>
    <rect x="66" y="100" width="38" height="16" rx="6" fill="#F1E2E8"/>
    <circle cx="292" cy="108" r="9" fill="#853953" opacity=".22"/>

    {{-- Lens --}}
    <circle cx="180" cy="172" r="94" fill="#612D53" opacity=".12"/>
    <circle cx="180" cy="168" r="90" fill="url(#cam-ring)"/>
    <circle cx="180" cy="168" r="90" stroke="#FFFFFF" stroke-opacity=".7" stroke-width="2"/>
    <circle cx="180" cy="168" r="72" fill="url(#cam-barrel)"/>
    <circle cx="180" cy="168" r="64" stroke="#E7B6C7" stroke-opacity=".35" stroke-width="1.5" stroke-dasharray="2 6"/>
    <circle cx="180" cy="168" r="54" fill="url(#cam-glass)"/>
    <path d="M180 132l31 18v36l-31 18-31-18v-36z" stroke="#E7B6C7" stroke-opacity=".22" stroke-width="1.5"/>
    <circle cx="196" cy="186" r="30" fill="url(#cam-glow)"/>
    <path d="M146 146c10-16 28-24 46-22" stroke="#FFFFFF" stroke-opacity=".75" stroke-width="7" stroke-linecap="round"/>
    <circle cx="154" cy="166" r="6" fill="#FFFFFF" opacity=".55"/>
    <circle cx="206" cy="196" r="3" fill="#FFFFFF" opacity=".4"/>
</svg>
