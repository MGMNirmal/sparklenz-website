<x-layouts.site title="About & Team" page="about" description="Meet Sparklenz, a Sri Lankan creative studio for video, drone, design and social content — our story, our approach and the team.">
    @include('partials.page-hero', ['eyebrow' => 'About Sparklenz', 'copyPage' => 'about', 'pill' => 'Beyond the frame'])

    <section aria-labelledby="story-title">
        <div class="wrap">
            <div class="about-film frame on-dark" data-reveal>
                @if ($storyProject?->cover_path)
                    <img src="{{ $storyProject->coverUrl() }}" alt="{{ $storyProject->cover_alt }}" loading="lazy" decoding="async" data-parallax>
                @endif
                <div class="frame__corners"><span></span></div>
                <div class="about-film__caption">
                    <span class="label">Ideas · Creativity · Results</span>
                    <h2>The best stories start with a spark.</h2>
                    @if ($storyProject?->is_demo)
                        <span class="badge badge--concept" style="justify-self: start; color: var(--ink)">Sample imagery</span>
                    @endif
                </div>
            </div>

            <div class="statement">
                <div data-reveal><p class="eyebrow">Our way of seeing</p></div>
                <div data-reveal>
                    <h2 class="h-lg" id="story-title">{{ page_copy_html('about.story_title') }}</h2>
                    <p>{{ page_copy('about.story_body') }}</p>
                </div>
            </div>

            <ul class="values" aria-label="How we work">
                @foreach ([1, 2, 3] as $value)
                    <li class="value" data-reveal>
                        <span class="frame-no">0{{ $value }}</span>
                        <h3>{{ page_copy('about.value_'.$value.'_title') }}</h3>
                        <p>{{ page_copy('about.value_'.$value.'_body') }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section team-section" style="margin-top: clamp(72px, 10vw, 136px)" aria-labelledby="team-title">
        <div class="wrap">
            <div class="section-head" data-reveal>
                <div>
                    <p class="eyebrow">The people behind the perspective</p>
                    <h2 class="h-xl" id="team-title">{{ page_copy_html('about.team_title') }}</h2>
                </div>
                <p class="muted">{{ page_copy('about.team_intro') }}</p>
            </div>

            @if ($team->isNotEmpty())
                <ul class="team-grid">
                    @foreach ($team as $member)
                        <li class="team-card" data-reveal>
                            <div class="team-card__portrait frame">
                                @if ($member->photoUrl())
                                    <img src="{{ $member->photoUrl() }}" alt="{{ $member->photo_alt ?: 'Portrait of '.$member->name }}" loading="lazy" decoding="async">
                                @else
                                    <span class="team-card__monogram" aria-hidden="true">{{ $member->initials() }}</span>
                                @endif
                                @if ($member->is_demo)
                                    <span class="badge">Placeholder profile</span>
                                @endif
                                <div class="frame__corners"><span></span></div>
                            </div>
                            <h3>{{ $member->name }}</h3>
                            <p>{{ $member->role }}</p>
                            @if ($member->bio)
                                <p class="team-card__bio muted">{{ $member->bio }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="empty-state"><p>Team profiles are on their way.</p></div>
            @endif
        </div>
    </section>

    @include('partials.process')
    @include('partials.cta')
</x-layouts.site>
