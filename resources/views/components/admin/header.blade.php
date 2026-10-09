@props(['title', 'subtitle' => null])
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="font-display text-3xl font-semibold tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 max-w-2xl text-ink/65">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap gap-3">{{ $slot }}</div>
    @endif
</div>
