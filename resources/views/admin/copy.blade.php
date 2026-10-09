<x-layouts.admin title="Page copy">
    <x-admin.header title="Page copy" subtitle="Approved wording for each page. Plain text only; line breaks are kept." />
    <nav aria-label="Pages" class="mb-6 flex flex-wrap gap-2">
        @foreach ($pages as $key => $label)
            <a href="{{ route('admin.copy.edit', $key) }}" @class(['rounded-full px-4 py-2 text-sm font-semibold', 'bg-ink text-paper' => $key === $page, 'border border-ink/20 hover:bg-white' => $key !== $page]) @if ($key === $page) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    <form method="POST" action="{{ route('admin.copy.update', $page) }}" class="max-w-3xl space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
        @csrf @method('PUT')
        @foreach ($entries as $entry)
            <x-admin.field :label="$entry->label" name="copy[{{ $entry->key }}]" :type="$entry->type === 'text' ? 'text' : 'textarea'" :rows="$entry->type === 'lines' ? 6 : 3" :value="$entry->content" maxlength="{{ $entry->max_length }}" :help="'Up to '.$entry->max_length.' characters.'.($entry->type === 'lines' ? ' One item per line.' : '')" />
        @endforeach
        <button class="rounded-full bg-berry px-8 py-3 font-semibold text-paper hover:bg-plum">Save copy</button>
    </form>
</x-layouts.admin>
