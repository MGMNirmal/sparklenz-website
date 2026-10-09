<x-layouts.admin title="Work">
    <x-admin.header title="Work" subtitle="Order, feature and publish portfolio projects. Featured projects appear on the home page (the first one becomes the hero poster).">
        <a href="{{ route('admin.projects.create') }}" class="rounded-full bg-berry px-5 py-2.5 font-semibold text-paper hover:bg-plum">Add project</a>
    </x-admin.header>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-ink/5">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="border-b border-ink/10 text-ink/60">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold">Order</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Project</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Media</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Featured</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr class="border-b border-ink/5 last:border-0">
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                @foreach (['up' => '↑', 'down' => '↓'] as $direction => $arrow)
                                    <form method="POST" action="{{ route('admin.projects.move', [$project, $direction]) }}">
                                        @csrf
                                        <button class="grid size-8 place-items-center rounded-full border border-ink/15 hover:bg-ink hover:text-paper disabled:opacity-30" @disabled(($direction === 'up' && $loop->parent->first) || ($direction === 'down' && $loop->parent->last))>
                                            {{ $arrow }}<span class="sr-only">Move {{ $project->title }} {{ $direction }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($project->coverThumbUrl())
                                    <img src="{{ $project->coverThumbUrl() }}" alt="" class="h-12 w-16 rounded-md object-cover">
                                @else
                                    <span class="grid h-12 w-16 place-items-center rounded-md bg-plum text-xs text-paper">Design</span>
                                @endif
                                <div>
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="font-semibold hover:text-berry">{{ $project->title }}</a>
                                    <p class="text-ink/60">{{ $project->category }} @if ($project->is_demo)<span class="ml-1 rounded bg-amber-100 px-1.5 text-xs font-semibold text-amber-900">Concept / demo</span>@endif</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-ink/70">
                            {{ $project->media_type->label() }}
                            <span class="block text-xs">{{ $project->hasFilm() ? 'YouTube ✓' : 'No film' }} · {{ $project->teaser_path ? 'Teaser ✓' : 'No teaser' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.projects.toggle', [$project, 'featured']) }}">
                                @csrf @method('PATCH')
                                <button class="rounded-full border px-3 py-1 text-xs font-semibold {{ $project->is_featured ? 'border-berry bg-berry/10 text-berry' : 'border-ink/15 text-ink/60' }}" aria-pressed="{{ $project->is_featured ? 'true' : 'false' }}">
                                    {{ $project->is_featured ? 'Featured' : 'Not featured' }}<span class="sr-only"> — toggle for {{ $project->title }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.projects.toggle', [$project, 'published']) }}">
                                @csrf @method('PATCH')
                                <button class="rounded-full px-3 py-1 text-xs font-semibold {{ $project->status->value === 'published' ? 'bg-emerald-100 text-emerald-900' : 'bg-ink/10 text-ink/70' }}">
                                    {{ $project->status->label() }} · {{ $project->status->value === 'published' ? 'Unpublish' : 'Publish' }}<span class="sr-only"> {{ $project->title }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="font-semibold text-berry hover:underline">Edit<span class="sr-only"> {{ $project->title }}</span></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-ink/60">No projects yet. Add your first piece of work.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
