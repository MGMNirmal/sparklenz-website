<x-layouts.admin :title="'Enquiry from '.$enquiry->name">
    <x-admin.header :title="$enquiry->name" :subtitle="'Received '.$enquiry->created_at->timezone('Asia/Colombo')->format('l j F Y, g:i a').' (Sri Lanka time)'">
        <a href="{{ route('admin.enquiries.index') }}" class="rounded-full border border-ink/20 px-5 py-2.5 font-semibold hover:bg-white">All enquiries</a>
    </x-admin.header>
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
            <dl class="grid gap-4 sm:grid-cols-3">
                <div><dt class="text-sm text-ink/60">Email</dt><dd><a class="font-semibold text-berry hover:underline" href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: your enquiry to Sparklenz') }}">{{ $enquiry->email }}</a></dd></div>
                <div><dt class="text-sm text-ink/60">Phone</dt><dd>@if ($enquiry->phone)<a class="font-semibold text-berry hover:underline" href="tel:{{ preg_replace('/[^0-9+]/', '', $enquiry->phone) }}">{{ $enquiry->phone }}</a>@else — @endif</dd></div>
                <div><dt class="text-sm text-ink/60">Interested in</dt><dd class="font-semibold">{{ $enquiry->service_interest ?? '—' }}</dd></div>
            </dl>
            <h2 class="mt-8 text-sm text-ink/60">Message</h2>
            <p class="mt-2 whitespace-pre-line leading-relaxed">{{ $enquiry->message }}</p>
        </article>
        <aside class="space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
            <p class="flex items-center gap-2 text-sm">Status: <x-admin.status :status="$enquiry->status" /></p>
            <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="space-y-3">
                @csrf @method('PATCH')
                <x-admin.field label="Mark as" name="status" type="select" :value="$enquiry->status->value" :options="collect(\App\Enums\EnquiryStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])" />
                <button class="w-full rounded-full bg-berry px-6 py-2.5 font-semibold text-paper hover:bg-plum">Update status</button>
            </form>
            <p class="text-xs text-ink/50">Email notification: {{ $enquiry->notified_at ? 'sent '.$enquiry->notified_at->diffForHumans() : 'not sent' }}</p>
        </aside>
    </div>
</x-layouts.admin>
