@props(['label', 'name', 'checked' => false, 'help' => null])
<label {{ $attributes->class('flex items-start gap-3 rounded-lg border border-ink/15 bg-white px-4 py-3') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" class="mt-1 size-4 accent-berry" @checked(old($name, $checked))>
    <span>
        <span class="block text-sm font-semibold">{{ $label }}</span>
        @if ($help)
            <span class="block text-sm text-ink/60">{{ $help }}</span>
        @endif
    </span>
</label>
