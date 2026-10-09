@props(['status'])
@php
    $classes = match ($status->value) {
        'new' => 'bg-berry text-paper',
        'read' => 'bg-amber-100 text-amber-900',
        'replied' => 'bg-emerald-100 text-emerald-900',
    };
@endphp
<span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $classes }}">{{ $status->label() }}</span>
