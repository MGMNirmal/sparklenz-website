<x-layouts.site title="Our Work" page="work" :projects="$projects" description="Films, stills and design from Sparklenz. Watch projects in the on-site screening room.">
    @php
        $filters = [
            'all' => 'All work',
            'video' => 'Film',
            'image' => 'Stills',
            'design' => 'Design',
        ];
        $counts = $projects->countBy(fn ($project) => $project->media_type->value);
    @endphp

    @include('partials.page-hero', ['eyebrow' => 'The work, in focus', 'copyPage' => 'work', 'pill' => str_pad((string) $projects->count(), 2, '0', STR_PAD_LEFT).' projects'])

    <section class="section" style="padding-top: 0" aria-label="Portfolio" data-open-on-load="{{ $openSlug }}">
        <div class="wrap" x-data="{ filter: 'all' }">
            @if ($projects->isNotEmpty())
                <div class="filter-bar" role="group" aria-label="Filter projects" x-cloak x-show="true">
                    @foreach ($filters as $key => $label)
                        @if ($key === 'all' || ($counts[$key] ?? 0) > 0)
                            <button class="filter-chip" type="button" :aria-pressed="(filter === '{{ $key }}').toString()" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}" @click="filter = '{{ $key }}'; $dispatch('work-filtered')">
                                {{ $label }} <small>{{ $key === 'all' ? $projects->count() : $counts[$key] }}</small>
                            </button>
                        @endif
                    @endforeach
                </div>

                <div class="work-grid" aria-live="polite">
                    @foreach ($projects as $project)
                        <x-project-card :project="$project" :index="$loop->index" :eager="$loop->index < 2"
                                        x-show="filter === 'all' || filter === '{{ $project->media_type->value }}'" />
                    @endforeach
                </div>

                @if ($projects->contains('is_demo', true))
                    <div class="work-note">
                        <p>Projects marked “Concept project” use fictional brands, stock photography (Unsplash) and artwork made for this preview. They do not represent completed client work.</p>
                        <span class="label">A preview of possibilities</span>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <x-icon name="film" style="width: 40px; height: 40px; color: var(--berry)" />
                    <p class="lead">{{ page_copy('work.empty_state') }}</p>
                    <a class="btn btn--berry" href="{{ route('contact') }}">Ask about our work <x-icon name="arrow" /></a>
                </div>
            @endif
        </div>
    </section>

    @include('partials.cta')
</x-layouts.site>
