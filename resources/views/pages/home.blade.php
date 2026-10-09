<x-layouts.site page="home" :projects="$allProjects">
    @php
        $tickerItems = page_copy_lines('home.ticker');
        $heroWords = page_copy_lines('home.hero_words') ?: ['move'];
    @endphp

    <section class="hero" aria-labelledby="hero-title" data-hero>
        <div class="hero__backdrop" aria-hidden="true">
            <span class="hero__glow hero__glow--blush"></span>
            <span class="hero__glow hero__glow--rose"></span>
            <span class="hero__glow hero__glow--mauve"></span>
        </div>

        <div class="wrap hero__stage">
            <div class="hero__art hero__art--camera" data-hero-art>
                <x-illustrations.camera class="hero__camera" />
            </div>

            <div class="hero__copy">
                <p class="hero__badge" data-hero-fade>{{ page_copy('home.hero_eyebrow') }}</p>
                <h1 class="hero__headline" id="hero-title">
                    <span class="sr-only">{{ page_copy('home.hero_title') }} {{ page_copy('home.hero_accent') }} {{ \Illuminate\Support\Arr::join($heroWords, ', ', ' and ') }}.</span>
                    <span class="hero__line" aria-hidden="true" data-hero-fade>{{ page_copy('home.hero_title') }}</span>
                    <span class="hero__line" aria-hidden="true" data-hero-fade>
                        {{ page_copy('home.hero_accent') }}
                        <span class="hero__changing" data-typewriter data-words='@json($heroWords)'>
                            @foreach ($heroWords as $word)
                                <span class="hero__sizer">{{ $word }}.</span>
                            @endforeach
                            <span class="hero__typed"><span class="hero__word" data-typewriter-text>{{ $heroWords[0] }}</span><span class="hero__caret"></span><span class="hero__stop">.</span></span>
                        </span>
                    </span>
                </h1>
                <p class="lead hero__intro" data-hero-fade>{{ page_copy('home.hero_intro') }}</p>
                <ul class="hero__services" aria-label="What we do" data-hero-fade>
                    @foreach (['Video', 'Drone', 'Design', 'Social'] as $service)
                        <li><x-icon name="check" />{{ $service }}</li>
                    @endforeach
                </ul>
                <div class="btn-row hero__actions" data-hero-fade>
                    <a class="btn btn--gradient" href="{{ route('contact') }}">Start a project <x-icon name="arrow" /></a>
                    <a class="btn btn--outline" href="{{ route('work') }}">Explore our work <x-icon name="right" /></a>
                </div>
            </div>

            <div class="hero__art hero__art--drone" data-hero-art>
                <x-illustrations.drone class="hero__drone" />
            </div>
        </div>

        <button class="hero__motion-toggle" type="button" data-hero-toggle aria-pressed="false" hidden>
            <x-icon name="pause" data-hero-icon="pause" />
            <x-icon name="play" data-hero-icon="play" hidden />
            <span class="sr-only" data-hero-toggle-label>Pause hero animation</span>
        </button>
    </section>

    @if ($wallColumns)
        @include('partials.project-wall')
    @endif

    @if ($tickerItems)
        <div class="ticker on-dark" data-ticker>
            <p class="sr-only">{{ implode(' · ', $tickerItems) }}</p>
            <div class="ticker__track" aria-hidden="true">
                @foreach ([false, true] as $duplicate)
                    <div class="ticker__group" @if ($duplicate) aria-hidden="true" @endif>
                        @foreach ($tickerItems as $item)
                            <span class="ticker__item">{{ $item }}</span>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <button class="ticker__pause" type="button" data-ticker-toggle aria-pressed="false">
                <x-icon name="pause" />
                <span class="sr-only" data-ticker-label>Pause scrolling text</span>
            </button>
        </div>
    @endif

    <section class="section" aria-labelledby="services-title">
        <div class="wrap">
            <div class="section-head" data-reveal>
                <div>
                    <p class="eyebrow">What we bring to the frame</p>
                    <h2 class="h-xl" id="services-title">{{ page_copy_html('home.services_title') }}</h2>
                </div>
                <p class="muted">{{ page_copy('home.services_intro') }}</p>
            </div>
            <div class="service-list">
                @foreach ($services as $service)
                    <a class="service-row" href="{{ route('services') }}#{{ $service->slug }}" data-reveal>
                        <span class="frame-no">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $service->name }}</h3>
                        <p>{{ $service->summary }}</p>
                        <span class="service-row__price">
                            @if ($service->starting_price !== null)
                                {{ $service->price_prefix }} LKR {{ lkr($service->starting_price) }}
                                <small>{{ $service->starting_unit }}</small>
                            @else
                                {{ $service->starting_label }}
                            @endif
                        </span>
                        <span class="icon-btn" aria-hidden="true"><x-icon name="arrow" /></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" style="padding-top: 0" aria-labelledby="work-title">
        <div class="wrap">
            <div class="section-head" data-reveal>
                <div>
                    <p class="eyebrow">Selected work</p>
                    <h2 class="h-xl" id="work-title">{{ page_copy_html('home.work_title') }}</h2>
                </div>
                <div style="max-width: 400px; display: grid; gap: 18px; justify-items: start;">
                    <p class="muted">{{ page_copy('home.work_intro') }}</p>
                    <a class="text-link" href="{{ route('work') }}">See all work <x-icon name="arrow" /></a>
                </div>
            </div>

            @if ($projects->isNotEmpty())
                <div class="work-grid">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" :index="$loop->index" />
                    @endforeach
                </div>
                @if ($projects->contains('is_demo', true))
                    <div class="work-note">
                        <p>Concept projects use fictional brands and stock photography to show the visual direction. Approved client work will take their place.</p>
                        <span class="label">Concept work · clearly labelled</span>
                    </div>
                @endif
            @else
                <div class="empty-state"><p>{{ page_copy('work.empty_state') }}</p></div>
            @endif
        </div>
    </section>

    <section class="section" style="padding-top: 0" aria-labelledby="studio-title">
        <div class="wrap">
            <div class="studio-panel" data-reveal>
                <div class="studio-panel__media frame">
                    @php $studioImage = $projects->firstWhere('slug', 'in-the-making') ?? $projects->whereNotNull('cover_path')->last(); @endphp
                    @if ($studioImage?->cover_path)
                        <img src="{{ $studioImage->coverThumbUrl() }}" alt="{{ $studioImage->cover_alt }}" loading="lazy" decoding="async" data-parallax>
                    @endif
                    <div class="frame__corners"><span></span></div>
                </div>
                <div class="studio-panel__copy on-dark">
                    <p class="eyebrow">Meet the studio</p>
                    <h2 class="h-lg" id="studio-title">{{ page_copy_html('home.studio_title') }}</h2>
                    <p>{{ page_copy('home.studio_body') }}</p>
                    <a class="text-link" href="{{ route('about') }}">A little about us <x-icon name="arrow" /></a>
                </div>
            </div>
        </div>
    </section>

    @include('partials.process')
    @include('partials.cta')
</x-layouts.site>
