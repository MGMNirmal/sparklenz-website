@php $editing = $member->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit '.$member->name : 'Add person'">
    <x-admin.header :title="$editing ? $member->name : 'Add person'">
        <a href="{{ route('admin.team.index') }}" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">Back to team</a>
    </x-admin.header>
    <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.team.update', $member) : route('admin.team.store') }}" class="max-w-3xl space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <x-admin.field label="Name" name="name" :value="$member->name" required />
            <x-admin.field label="Role" name="role" :value="$member->role" required />
        </div>
        <x-admin.field label="Short bio" name="bio" type="textarea" rows="3" :value="$member->bio" />
        <div class="grid gap-5 md:grid-cols-[120px_minmax(0,1fr)]">
            @if ($member->photoUrl())
                <img src="{{ $member->photoUrl() }}" alt="" class="size-28 rounded-xl object-cover">
            @else
                <span class="grid size-28 place-items-center rounded-xl bg-paper text-sm text-ink/50">No photo</span>
            @endif
            <div class="space-y-4">
                <x-admin.field label="Portrait" name="photo" type="file" accept="image/jpeg,image/png,image/webp" help="JPG, PNG or WebP, at least 400 × 400 px." />
                <x-admin.field label="Portrait description (alt text)" name="photo_alt" :value="$member->photo_alt" />
                @if ($member->photo_path)
                    <x-admin.checkbox name="remove_photo" label="Remove portrait" />
                @endif
            </div>
        </div>
        <x-admin.field label="Order" name="sort_order" type="number" min="0" :value="$member->sort_order" />
        <x-admin.checkbox name="is_published" label="Show on the About page" :checked="$member->is_published" />
        <x-admin.checkbox name="is_demo" label="Placeholder profile" :checked="$member->is_demo" help="Labelled as a placeholder and hidden in production unless demo content is enabled." />
        <button class="rounded-full bg-berry px-8 py-3 font-semibold text-paper hover:bg-plum">{{ $editing ? 'Save' : 'Add person' }}</button>
    </form>
    @if ($editing)
        <form method="POST" action="{{ route('admin.team.destroy', $member) }}" class="mt-8" onsubmit="return confirm('Remove this profile?')">
            @csrf @method('DELETE')
            <button class="text-sm font-semibold text-rose-700 hover:underline">Remove profile</button>
        </form>
    @endif
</x-layouts.admin>
