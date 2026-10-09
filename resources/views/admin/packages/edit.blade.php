@php
    $tiers = array_values(old('tiers', $package->tiers->map(fn ($tier) => $tier->only(['id', 'label', 'inclusions', 'price', 'previous_price', 'alt_price']))->all()));
    for ($i = 0; $i < 2; $i++) {
        $tiers[] = ['id' => null, 'label' => null, 'inclusions' => null, 'price' => null, 'previous_price' => null, 'alt_price' => null];
    }
@endphp
<x-layouts.admin :title="'Edit '.$package->name">
    <x-admin.header :title="$package->name" subtitle="The lowest current tier price becomes the “From” price on the package card.">
        <a href="{{ route('admin.packages.index') }}" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">All packages</a>
    </x-admin.header>

    <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="space-y-8">
        @csrf @method('PUT')
        <section class="grid gap-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 md:grid-cols-2">
            <x-admin.field label="Name" name="name" :value="$package->name" required />
            <x-admin.field label="Tagline" name="tagline" :value="$package->tagline" />
            <x-admin.field label="Type label" name="type_label" :value="$package->type_label" required help="e.g. Editing + social media" />
            <x-admin.field label="Billing unit" name="billing_unit" :value="$package->billing_unit" required help="Shown as “/ month”." />
            <x-admin.field class="md:col-span-2" label="Description" name="description" type="textarea" rows="2" :value="$package->description" />
            <x-admin.field label="First column heading" name="tier_column_label" :value="$package->tier_column_label" required />
            <x-admin.field label="Price column heading" name="price_column_label" :value="$package->price_column_label" required />
            <x-admin.field label="Second price column heading (optional)" name="alt_price_column_label" :value="$package->alt_price_column_label" help="e.g. Without presenting. Leave empty for a single price column." />
            <div class="grid grid-cols-2 gap-4">
                <x-admin.field label="Additional item price" name="additional_item_price" type="number" min="0" :value="$package->additional_item_price" />
                <x-admin.field label="Additional item unit" name="additional_item_unit" :value="$package->additional_item_unit" />
            </div>
            <x-admin.field label="Inclusions" name="includes_text" type="textarea" rows="5" :value="\App\Support\ListText::itemsToText($package->includes)" help="One per line." />
            <x-admin.field label="Notes" name="notes_text" type="textarea" rows="5" :value="\App\Support\ListText::itemsToText($package->notes)" help="One per line." />
            <x-admin.checkbox name="is_featured" label="Highlight this package" :checked="$package->is_featured" />
            <x-admin.checkbox name="is_published" label="Show on the website" :checked="$package->is_published" />
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
            <h2 class="font-display text-lg font-semibold">Tiers</h2>
            <p class="mt-1 text-sm text-ink/60">“Previous price” is shown struck through — use it only where the rate card shows a reduced price.</p>
            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead class="text-ink/60"><tr>
                        <th class="pb-2 pr-2 font-semibold">Tier</th><th class="pb-2 pr-2 font-semibold">Inclusions</th><th class="pb-2 pr-2 font-semibold">Price (LKR)</th><th class="pb-2 pr-2 font-semibold">Previous price</th><th class="pb-2 pr-2 font-semibold">Second column price</th><th class="pb-2 font-semibold">Delete</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($tiers as $i => $tier)
                            <tr class="align-top">
                                <td class="py-1 pr-2"><input type="hidden" name="tiers[{{ $i }}][id]" value="{{ $tier['id'] ?? '' }}"><input aria-label="Tier" name="tiers[{{ $i }}][label]" value="{{ $tier['label'] ?? '' }}" class="w-full rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Inclusions" name="tiers[{{ $i }}][inclusions]" value="{{ $tier['inclusions'] ?? '' }}" class="w-full rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Price" type="number" min="0" name="tiers[{{ $i }}][price]" value="{{ $tier['price'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Previous price" type="number" min="0" name="tiers[{{ $i }}][previous_price]" value="{{ $tier['previous_price'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 pr-2"><input aria-label="Second column price" type="number" min="0" name="tiers[{{ $i }}][alt_price]" value="{{ $tier['alt_price'] ?? '' }}" class="w-28 rounded-md border border-ink/20 px-2 py-1.5"></td>
                                <td class="py-1 text-center">@if ($tier['id'] ?? null)<input type="checkbox" aria-label="Delete tier" name="tiers[{{ $i }}][delete]" value="1" class="mt-2 size-4 accent-rose-700">@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        <button class="rounded-full bg-berry px-8 py-3 font-semibold text-paper hover:bg-plum">Save package</button>
    </form>
</x-layouts.admin>
