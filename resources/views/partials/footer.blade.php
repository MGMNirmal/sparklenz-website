@php
    $socials = array_filter([
        'Instagram' => site_setting('instagram_url'),
        'TikTok' => site_setting('tiktok_url'),
        'Facebook' => site_setting('facebook_url'),
        'YouTube' => site_setting('youtube_url'),
    ]);
@endphp
<footer class="site-footer on-dark">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <a class="footer-brand" href="{{ route('home') }}" aria-label="Sparklenz — home">
                    <img src="{{ asset('images/brand/logo-light.png') }}" alt="Sparklenz — Beyond the Frame" width="803" height="186" loading="lazy">
                </a>
                <p>{{ page_copy_html('global.footer_tagline') }}</p>
            </div>
            <div>
                <h2 class="footer-title">Explore</h2>
                <ul class="footer-links">
                    <li><a href="{{ route('work') }}">Our work</a></li>
                    <li><a href="{{ route('services') }}">Services &amp; packages</a></li>
                    <li><a href="{{ route('about') }}">About the studio</a></li>
                    <li><a href="{{ route('contact') }}">Start a project</a></li>
                </ul>
            </div>
            <div>
                <h2 class="footer-title">Talk to us</h2>
                <ul class="footer-links">
                    @if ($email = site_setting('email'))
                        <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    @endif
                    @if ($phone = site_setting('phone_display'))
                        <li><a href="tel:{{ site_setting('phone_link') }}">{{ $phone }}</a></li>
                    @endif
                    @if ($whatsapp = whatsapp_url())
                        <li><a href="{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp <span class="sr-only">(opens in a new tab)</span>↗</a></li>
                    @endif
                    @foreach ($socials as $label => $url)
                        <li><a href="{{ $url }}" target="_blank" rel="noopener me">{{ $label }} <span class="sr-only">(opens in a new tab)</span>↗</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ now()->year }} Sparklenz. Beyond the Frame. · {{ site_setting('location', 'Sri Lanka') }}</span>
            <a href="#main" class="text-link" data-back-to-top>Back to top <x-icon name="up" /></a>
        </div>
    </div>
</footer>
