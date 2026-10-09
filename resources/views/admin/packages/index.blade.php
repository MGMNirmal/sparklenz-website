<x-layouts.admin title="Packages">
    <x-admin.header title="Monthly packages" subtitle="Tiers, prices and inclusions for each package." />
    <ul class="grid gap-4 md:grid-cols-2">
        @foreach ($packages as $package)
            <li>
                <a href="{{ route('admin.packages.edit', $package) }}" class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 transition hover:ring-berry/40">
                    <p class="flex items-center justify-between gap-3">
                        <span class="font-display text-xl font-semibold">{{ $package->name }}</span>
                        <span class="text-sm text-ink/60">{{ $package->tiers->count() }} tiers</span>
                    </p>
                    <p class="mt-1 text-sm text-ink/60">{{ $package->type_label }} · from LKR {{ lkr($package->startingPrice()) }} / {{ $package->billing_unit }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</x-layouts.admin>
