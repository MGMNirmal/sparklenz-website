@props(['project', 'index' => 0, 'eager' => false])
@php
    /** @var \App\Models\Project $project */
    $number = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
    $hasFilm = $project->hasFilm();
    $type = $project->media_type->value;
    $actionLabel = match (true) {
        $hasFilm => 'Watch film',
        $type === 'image' => 'View stills',
        default => 'View project',
    };
    $landscape = $project->cover_width && $project->cover_height && $project->cover_width > $project->cover_height;
@endphp
<article {{ $attributes->class(['work-card', 'work-card--landscape' => $landscape]) }}
         data-project-card data-slug="{{ $project->slug }}" data-type="{{ $type }}"
         @if ($project->teaserUrl()) data-teaser="{{ $project->teaserUrl() }}" @endif
         @if ($hasFilm) data-youtube="{{ $project->youtube_id }}" @endif
         data-reveal>
    <div class="work-card__media frame" data-card-media data-open-project="{{ $project->slug }}">
        @if ($project->cover_path)
            <img src="{{ $project->coverThumbUrl() }}"
                 srcset="{{ $project->coverThumbUrl() }} 900w, {{ $project->coverUrl() }} {{ $project->cover_width ?? 2000 }}w"
                 sizes="(max-width: 720px) 100vw, 50vw"
                 alt="{{ $project->cover_alt }}" width="{{ $project->cover_width }}" height="{{ $project->cover_height }}"
                 loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" data-parallax>
        @elseif ($project->coverUrl())
            <img src="{{ $project->coverUrl() }}" alt="{{ $project->cover_alt ?? '' }}" loading="lazy" decoding="async">
        @else
            <div class="design-art {{ $index % 2 ? 'design-art--alt' : '' }}" role="img" aria-label="{{ $project->cover_alt ?? $project->title }}">
                <div class="design-art__ring"></div>
                <div class="design-art__title">{{ $project->title }}</div>
                <div class="design-art__foot"><span>{{ $project->client_label }}</span><span>Sparklenz</span></div>
            </div>
        @endif
        <div class="work-card__preview" data-preview-slot aria-hidden="true"></div>
        <div class="frame__corners"><span></span></div>
        <div class="work-card__top">
            <span class="frame-no">SPK—{{ $number }}</span>
            @if ($project->is_demo)
                <span class="badge badge--concept">Concept project</span>
            @endif
        </div>
        <div class="work-card__state">
            @if ($hasFilm || $project->teaserUrl())
                <x-icon name="play" /> {{ $project->teaserUrl() ? 'Motion preview' : 'Film' }}
            @elseif ($type === 'image')
                <x-icon name="image" /> Stills
            @else
                <x-icon name="spark" /> Design
            @endif
        </div>
    </div>
    <div class="work-card__info">
        <div>
            <h3>{{ $project->title }}</h3>
            <p>{{ $project->category }}</p>
        </div>
        <div class="work-card__actions">
            <button class="btn btn--sm {{ $hasFilm ? 'btn--berry' : 'btn--outline' }}" type="button" data-open-project="{{ $project->slug }}" data-autoplay="{{ $hasFilm ? 'true' : 'false' }}">
                {{ $actionLabel }} <x-icon name="{{ $hasFilm ? 'play' : 'arrow' }}" />
                <span class="sr-only">: {{ $project->title }}</span>
            </button>
        </div>
    </div>
</article>
