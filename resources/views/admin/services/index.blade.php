<x-layouts.admin title="Services & rates">
    <x-admin.header title="Services & rates" subtitle="Each service has its rate rows, inclusions and notes. Amounts are stored in whole LKR." />
    <ul class="grid gap-4 md:grid-cols-2">
        @foreach ($services as $service)
            <li>
                <a href="{{ route('admin.services.edit', $service) }}" class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 transition hover:ring-berry/40">
                    <p class="flex items-center justify-between gap-3">
                        <span class="font-display text-xl font-semibold">{{ $service->name }}</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $service->is_published ? 'bg-emerald-100 text-emerald-900' : 'bg-ink/10 text-ink/70' }}">{{ $service->is_published ? 'Shown' : 'Hidden' }}</span>
                    </p>
                    <p class="mt-1 text-sm text-ink/60">{{ $service->rates_count }} rate rows · {{ $service->starting_price !== null ? $service->price_prefix.' LKR '.lkr($service->starting_price) : $service->starting_label }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</x-layouts.admin>
