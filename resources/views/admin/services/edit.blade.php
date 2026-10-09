@php
    $rates = old('rates', $service->rates->map(fn ($rate) => $rate->only(['id', 'group_label', 'label', 'detail', 'price_prefix', 'price', 'previous_price', 'unit']))->all());
    $rates = array_values($rates);
    for ($i = 0; $i < 3; $i++) {
        $rates[] = ['id' => null, 'group_label' => end($rates)['group_label'] ?? null, 'label' => null, 'detail' => null, 'price_prefix' => null, 'price' => null, 'previous_price' => null, 'unit' => null];
    }
@endphp
<x-layouts.admin :title="'Edit '.$service->name">
    <x-admin.header :title="$service->name" subtitle="Update the summary, inclusions, notes and every rate row.">
        <a href="{{ route('admin.services.index') }}" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">All services</a>
    </x-admin.header>

    <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-8">
        @csrf @method('PUT')
        <section class="grid gap-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 md:grid-cols-2">
            <x-admin.field label="Name" name="name" :value="$service->name" required />
            <x-admin.field label="Summary (one line)" name="summary" :value="$service->summary" required />
            <x-admin.field class="md:col-span-2" label="Introduction" name="intro" type="textarea" rows="3" :value="$service->intro" />
            <div class="grid gap-4 sm:grid-cols-3 md:col-span-2">
                <x-admin.field label="Starting price prefix" name="price_prefix" :value="$service->price_prefix" help="e.g. From" />
                <x-admin.field label="Starting price (LKR)" name="starting_price" type="number" min="0" :value="$service->starting_price" />
                <x-admin.field label="Starting price unit" name="starting_unit" :value="$service->starting_unit" help="e.g. per 1-minute video" />
            </div>
            <x-admin.field label="Label when there is no starting price" name="starting_label" :value="$service->starting_label" help="e.g. On request" />
            <div></div>
            <x-admin.field label="Inclusions" name="includes_text" type="textarea" rows="8" :value="\App\Support\ListText::groupsToText($service->includes)" help="One item per line. A line ending in “:” starts a titled group." />
            <x-admin.field label="Notes" name="notes_text" type="textarea" rows="8" :value="\App\Support\ListText::itemsToText($service->notes)" help="One note per line (exclusions, extra charges)." />
            <x-admin.checkbox name="is_published" label="Show this service on the website" :checked="$service->is_published" />
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
            <h2 class="font-display text-lg font-semibold">Rate rows</h2>
            <p class="mt-1 text-sm text-ink/60">Rows with the same table heading are grouped. Leave the price empty for “on request”. Fill a blank row to add it; tick “Delete” to remove one.</p>
            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="text-ink/60">
                        <tr>
                            <th class="pb-2 pr-2 font-semibold">Table heading</th>
                            <th class="pb-2 pr-2 font-semibold">Item</th>
                            <th class="pb-2 pr-2 font-semibold">Detail</th>
                            <th class="pb-2 pr-2 font-semibold">Prefix</th>
                            <th class="pb-2 pr-2 font-semibold">Price (LKR)</th>
                            <th class="pb-2 pr-2 font-semibold">Previous price</th>
                            <th class="pb-2 pr-2 font-semibold">Unit</th>
                            <th class="pb-2 font-semibold">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rates as $i => $rate)
                            <tr class="align-top">
                                <td class="py-1 pr-2"><input type="hidden" name="rates[{{ $i }}][id]" value="{{ $rate['id'] ?? '' }}"><input aria-label="Table heading" name="rates[{{ $i }}][group_label]" value="{{ $rate['group_label'] ?? '' }}" class="w-full rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Item" name="rates[{{ $i }}][label]" value="{{ $rate['label'] ?? '' }}" class="w-full rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Detail" name="rates[{{ $i }}][detail]" value="{{ $rate['detail'] ?? '' }}" class="w-full rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Price prefix" name="rates[{{ $i }}][price_prefix]" value="{{ $rate['price_prefix'] ?? '' }}" class="w-20 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Price" type="number" min="0" name="rates[{{ $i }}][price]" value="{{ $rate['price'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Previous price" type="number" min="0" name="rates[{{ $i }}][previous_price]" value="{{ $rate['previous_price'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Unit" name="rates[{{ $i }}][unit]" value="{{ $rate['unit'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 text-center">@if ($rate['id'] ?? null)<input type="checkbox" aria-label="Delete row" name="rates[{{ $i }}][delete]" value="1" class="mt-2 size-4 accent-rose-700">@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <button class="rounded-full bg-berry px-8 py-3 font-semibold text-paper hover:bg-plum">Save service</button>
    </form>
</x-layouts.admin>
