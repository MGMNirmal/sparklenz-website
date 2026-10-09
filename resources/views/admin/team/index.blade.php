<x-layouts.admin title="Team">
    <x-admin.header title="Team" subtitle="Profiles shown on the About page, in order.">
        <a href="{{ route('admin.team.create') }}" class="rounded-full bg-berry px-5 py-2.5 font-semibold text-paper hover:bg-plum">Add person</a>
    </x-admin.header>
    <ul class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($members as $member)
            <li class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink/5">
                @if ($member->photoUrl())
                    <img src="{{ $member->photoUrl() }}" alt="" class="size-16 rounded-full object-cover">
                @else
                    <span class="grid size-16 place-items-center rounded-full bg-plum font-display text-lg font-semibold text-paper">{{ $member->initials() }}</span>
                @endif
                <div class="min-w-0 flex-1">
                    <a href="{{ route('admin.team.edit', $member) }}" class="font-semibold hover:text-berry">{{ $member->name }}</a>
                    <p class="text-sm text-ink/60">{{ $member->role }}</p>
                    <p class="mt-1 flex flex-wrap gap-1 text-xs">
                        <span class="rounded px-1.5 font-semibold {{ $member->is_published ? 'bg-emerald-100 text-emerald-900' : 'bg-ink/10' }}">{{ $member->is_published ? 'Shown' : 'Hidden' }}</span>
                        @if ($member->is_demo)<span class="rounded bg-amber-100 px-1.5 font-semibold text-amber-900">Placeholder</span>@endif
                    </p>
                </div>
            </li>
        @empty
            <li class="text-ink/60">No team profiles yet.</li>
        @endforelse
    </ul>
</x-layouts.admin>
