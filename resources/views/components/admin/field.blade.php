@props(['label', 'name', 'help' => null, 'type' => 'text', 'value' => null, 'required' => false, 'rows' => 4, 'options' => null, 'errorKey' => null])
@php
    $id = 'f-'.str_replace(['[', ']', '.'], ['-', '', '-'], $name);
    $key = $errorKey ?? str_replace(['[', ']'], ['.', ''], $name);
    $hasError = $errors->has($key);
    $base = 'mt-1.5 block w-full rounded-lg border bg-white px-3 py-2.5 text-[15px] shadow-sm focus:border-berry focus:outline-none focus:ring-4 focus:ring-berry/15 '.($hasError ? 'border-rose-400' : 'border-ink/20');
@endphp
<div {{ $attributes->class('min-w-0') }}>
    <label for="{{ $id }}" class="block text-sm font-semibold">{{ $label }}@if ($required) <span class="text-berry">*</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" class="{{ $base }}" @required($required) @if ($hasError) aria-invalid="true" @endif @if ($help) aria-describedby="{{ $id }}-help" @endif>{{ old($key, $value) }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" class="{{ $base }}" @required($required) @if ($help) aria-describedby="{{ $id }}-help" @endif>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) old($key, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($type === 'file')
        <input id="{{ $id }}" name="{{ $name }}" type="file" {{ $attributes->only(['accept', 'multiple']) }} class="mt-1.5 block w-full text-sm file:mr-4 file:rounded-full file:border-0 file:bg-ink file:px-4 file:py-2 file:text-sm file:font-semibold file:text-paper hover:file:bg-plum" @if ($help) aria-describedby="{{ $id }}-help" @endif>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($key, $value) }}" class="{{ $base }}" @required($required) @if ($hasError) aria-invalid="true" @endif @if ($help) aria-describedby="{{ $id }}-help" @endif {{ $attributes->only(['min', 'max', 'step', 'maxlength', 'inputmode', 'placeholder', 'autocomplete']) }}>
    @endif
    @if ($help)
        <p id="{{ $id }}-help" class="mt-1.5 text-sm text-ink/60">{{ $help }}</p>
    @endif
    @error($key)
        <p class="mt-1.5 text-sm font-medium text-rose-700">{{ $message }}</p>
    @enderror
</div>
