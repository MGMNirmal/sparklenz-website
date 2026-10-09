@php
    /** @var \App\Models\Project $project */
    $editing = $project->exists;
    $youtubeValue = $project->youtube_id ? \App\Support\YouTube::watchUrl($project->youtube_id) : '';
@endphp
<x-layouts.admin :title="$editing ? 'Edit '.$project->title : 'Add project'">
    <x-admin.header :title="$editing ? $project->title : 'Add project'" subtitle="Covers and gallery images are re-encoded to WebP and resized automatically.">
        <a href="{{ route('admin.projects.index') }}" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">Back to work</a>
        @if ($editing && $project->status->value === 'published')
            <a href="{{ route('work', ['project' => $project->slug]) }}" target="_blank" rel="noopener" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">View on site ↗</a>
        @endif
    </x-admin.header>

    <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_340px]">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="space-y-8">
            <section class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
                <h2 class="font-display text-lg font-semibold">Details</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Title" name="title" :value="$project->title" required maxlength="160" />
                    <x-admin.field label="URL slug" name="slug" :value="$project->slug" maxlength="180" help="Leave empty to generate from the title." />
                    <x-admin.field label="Client / brand label" name="client_label" :value="$project->client_label" maxlength="120" />
                    <x-admin.field label="Category" name="category" :value="$project->category" required maxlength="120" placeholder="e.g. Drone coverage · Hotel" />
                </div>
                <x-admin.field label="Short description" name="summary" type="textarea" rows="2" :value="$project->summary" required help="Shown under the title in the screening room (max 400 characters)." />
                <x-admin.field label="More detail (optional)" name="description" type="textarea" rows="4" :value="$project->description" help="Plain text. Credits, the brief, what was delivered." />
            </section>

            <section class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
                <h2 class="font-display text-lg font-semibold">Film & preview</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Media type" name="media_type" type="select" :value="$project->media_type?->value" :options="collect(\App\Enums\MediaType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])" />
                    <x-admin.field label="Video orientation" name="video_orientation" type="select" :value="$project->video_orientation" :options="['landscape' => 'Landscape (16:9)', 'portrait' => 'Portrait (9:16, e.g. Shorts / Reels)']" />
                </div>
                <x-admin.field label="YouTube link" name="youtube_url" :value="$youtubeValue" placeholder="https://youtu.be/… or https://www.youtube.com/shorts/…"
                               help="Paste a youtube.com, youtu.be or Shorts link. The video must be public or unlisted, with embedding allowed. It plays inside this website." />
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-admin.field label="Hover teaser (MP4 / WebM)" name="teaser" type="file" accept="video/mp4,video/webm"
                                       help="Optional silent 6–10 second loop, ideally under 4 MB (max {{ round(config('sparklenz.max_teaser_kb') / 1024) }} MB). Plays muted when visitors hover the project." />
                        @if ($project->teaser_path)
                            <div class="mt-3 rounded-lg bg-paper p-3">
                                <video src="{{ $project->teaserUrl() }}" muted loop playsinline controls class="max-h-40 rounded"></video>
                                <x-admin.checkbox name="remove_teaser" label="Remove this teaser" class="mt-2" />
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
                <h2 class="font-display text-lg font-semibold">Cover image</h2>
                <div class="grid gap-5 md:grid-cols-[200px_minmax(0,1fr)]">
                    <div>
                        @if ($project->cover_path)
                            <img src="{{ $project->coverThumbUrl() }}" alt="" class="aspect-[4/5] w-full rounded-lg object-cover">
                        @else
                            <div class="grid aspect-[4/5] place-items-center rounded-lg bg-paper p-4 text-center text-sm text-ink/60">No cover. Design projects show typographic artwork; films fall back to the YouTube thumbnail.</div>
                        @endif
                    </div>
                    <div class="space-y-5">
                        <x-admin.field label="Upload cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" help="JPG, PNG or WebP, at least 800 × 450 px, up to {{ round(config('sparklenz.max_image_kb') / 1024) }} MB." />
                        <x-admin.field label="Cover description (alt text)" name="cover_alt" :value="$project->cover_alt" maxlength="250" help="Describe what the image shows, e.g. “Aerial view of a hotel pool at sunset”." />
                        @if ($project->cover_path)
                            <x-admin.checkbox name="remove_cover" label="Remove the cover image" />
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5" x-data="{ rows: [0] }">
                <h2 class="font-display text-lg font-semibold">Image gallery</h2>
                <p class="text-sm text-ink/60">Shown in the screening room for stills and design projects. Minimum 600 × 400 px.</p>
                @if ($editing && $project->media->isNotEmpty())
                    <ul class="grid gap-4 md:grid-cols-2">
                        @foreach ($project->media as $item)
                            <li class="flex gap-4 rounded-xl border border-ink/10 p-3">
                                <img src="{{ $item->url() }}" alt="" class="h-24 w-24 flex-none rounded-lg object-cover">
                                <div class="grid flex-1 gap-2">
                                    <x-admin.field label="Alt text" name="media[{{ $item->id }}][alt]" :value="$item->alt" required />
                                    <x-admin.field label="Caption" name="media[{{ $item->id }}][caption]" :value="$item->caption" />
                                    <x-admin.field label="Order" name="media[{{ $item->id }}][sort_order]" type="number" min="0" :value="$item->sort_order" />
                                    <button type="submit" form="delete-media-{{ $item->id }}" class="justify-self-start text-sm font-semibold text-rose-700 hover:underline">Remove image</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid gap-4 rounded-xl border border-dashed border-ink/20 p-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold" :for="'gallery-' + index">New image</label>
                            <input type="file" :id="'gallery-' + index" :name="'gallery[' + index + ']'" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm file:mr-4 file:rounded-full file:border-0 file:bg-ink file:px-4 file:py-2 file:text-sm file:font-semibold file:text-paper">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold" :for="'gallery-alt-' + index">Alt text</label>
                            <input type="text" :id="'gallery-alt-' + index" :name="'gallery_alt[' + index + ']'" maxlength="250" class="mt-1.5 block w-full rounded-lg border border-ink/20 bg-white px-3 py-2.5">
                        </div>
                    </div>
                </template>
                <noscript>
                    <div class="grid gap-4 md:grid-cols-2">
                        <input type="file" name="gallery[0]" accept="image/jpeg,image/png,image/webp" aria-label="New gallery image">
                        <input type="text" name="gallery_alt[0]" aria-label="Alt text for new gallery image" class="rounded-lg border border-ink/20 px-3 py-2">
                    </div>
                </noscript>
                <button type="button" class="rounded-full border border-ink/20 px-4 py-2 text-sm font-semibold hover:bg-paper" x-cloak @click="rows.push(rows.length)">Add another image</button>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 xl:sticky xl:top-8">
                <h2 class="font-display text-lg font-semibold">Publishing</h2>
                <x-admin.field label="Status" name="status" type="select" :value="$project->status?->value" :options="['draft' => 'Draft (hidden)', 'published' => 'Published']" />
                <x-admin.checkbox name="is_featured" label="Feature on the home page" :checked="$project->is_featured" />
                <x-admin.checkbox name="is_demo" label="Concept / demo project" :checked="$project->is_demo" help="Labelled “Concept project” and hidden in production unless demo content is enabled." />
                <button class="w-full rounded-full bg-berry px-6 py-3 font-semibold text-paper hover:bg-plum">{{ $editing ? 'Save changes' : 'Create project' }}</button>
            </section>
        </aside>
    </form>

    @if ($editing)
        @foreach ($project->media as $item)
            <form id="delete-media-{{ $item->id }}" method="POST" action="{{ route('admin.projects.media.destroy', [$project, $item]) }}" onsubmit="return confirm('Remove this image?')">
                @csrf @method('DELETE')
            </form>
        @endforeach
        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="mt-10 border-t border-ink/10 pt-6" onsubmit="return confirm('Delete this project and its media permanently?')">
            @csrf @method('DELETE')
            <button class="text-sm font-semibold text-rose-700 hover:underline">Delete project</button>
        </form>
    @endif
</x-layouts.admin>
