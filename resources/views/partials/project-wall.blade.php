{{-- Animated project wall: five columns drifting in alternate directions
     (three on tablet, two on mobile), driven by resources/js/project-wall.js.
     Each column is rendered twice; the second copy is inert and hidden from
     assistive technology so the loop is seamless without duplicate stops.
     Repeats of a tile are mouse-only, so keyboard and screen-reader users
     meet each image once (always in column 1, which every layout shows). --}}
@php $seenTiles = []; @endphp
<section class="wall section" aria-labelledby="wall-title" data-project-wall>
    <div class="wrap">
        <div class="section-head" data-reveal>
            <div>
                <p class="eyebrow">From the studio floor</p>
                <h2 class="h-xl" id="wall-title">{{ page_copy_html('home.wall_title') }}</h2>
            </div>
            <div class="wall__head-aside">
                <p class="muted">{{ page_copy('home.wall_intro') }}</p>
                <div class="btn-row">
                    <button class="btn btn--sm btn--outline" type="button" data-wall-toggle aria-pressed="false" hidden>
                        <x-icon name="pause" data-wall-icon="pause" />
                        <x-icon name="play" data-wall-icon="play" hidden />
                        <span data-wall-label>Pause motion</span>
                    </button>
                    <a class="text-link" href="{{ route('work') }}">See all work <x-icon name="arrow" /></a>
                </div>
            </div>
        </div>
    </div>

    <div class="wall__viewport">
        <div class="wall__columns">
            @foreach ($wallColumns as $columnIndex => $tiles)
                <div class="wall__col" data-wall-column data-direction="{{ $columnIndex % 2 === 0 ? 'up' : 'down' }}">
                    <div class="wall__track" data-wall-track>
                        @foreach ([false, true] as $isCopy)
                            <ul class="wall__group" data-wall-group @if ($isCopy) aria-hidden="true" inert @endif>
                                @foreach ($tiles as $tile)
                                    @php
                                        $tileKey = $tile['slug'].'|'.$tile['src'];
                                        $isRepeat = $isCopy || isset($seenTiles[$tileKey]);
                                        $seenTiles[$tileKey] = true;
                                    @endphp
                                    <li class="wall__tile wall__tile--{{ $tile['shape'] }}" @if ($isRepeat && ! $isCopy) aria-hidden="true" @endif>
                                        <button type="button" class="wall__card" data-open-project="{{ $tile['slug'] }}" @if ($isRepeat) tabindex="-1" @endif>
                                            @if ($tile['design'])
                                                <span class="design-art {{ $loop->index % 2 ? 'design-art--alt' : '' }}" aria-hidden="true">
                                                    <span class="design-art__ring"></span>
                                                    <span class="design-art__title">{{ $tile['title'] }}</span>
                                                </span>
                                            @else
                                                <img src="{{ $tile['src'] }}" alt="" loading="lazy" decoding="async">
                                            @endif
                                            <span class="wall__caption">
                                                <span class="wall__title">{{ $tile['title'] }}</span>
                                                <span class="wall__category">{{ $tile['category'] }}</span>
                                            </span>
                                            <span class="sr-only">Open {{ $tile['title'] }} — {{ $tile['alt'] }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
