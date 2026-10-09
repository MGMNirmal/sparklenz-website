<x-layouts.admin title="Enquiries">
    <x-admin.header title="Enquiries" subtitle="Messages sent through the contact form. Opening a new enquiry marks it as read." />
    <nav aria-label="Filter enquiries" class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.enquiries.index') }}" @class(['rounded-full px-4 py-2 text-sm font-semibold', 'bg-ink text-paper' => ! $status, 'border border-ink/20 hover:bg-white' => $status])>All</a>
        @foreach (\App\Enums\EnquiryStatus::cases() as $case)
            <a href="{{ route('admin.enquiries.index', ['status' => $case->value]) }}" @class(['rounded-full px-4 py-2 text-sm font-semibold', 'bg-ink text-paper' => $status === $case, 'border border-ink/20 hover:bg-white' => $status !== $case])>{{ $case->label() }} ({{ $counts[$case->value] ?? 0 }})</a>
        @endforeach
    </nav>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-ink/5">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-ink/10 text-ink/60"><tr>
                <th scope="col" class="px-4 py-3 font-semibold">From</th><th scope="col" class="px-4 py-3 font-semibold">Interested in</th><th scope="col" class="px-4 py-3 font-semibold">Message</th><th scope="col" class="px-4 py-3 font-semibold">Received</th><th scope="col" class="px-4 py-3 font-semibold">Status</th>
            </tr></thead>
            <tbody>
                @forelse ($enquiries as $enquiry)
                    <tr class="border-b border-ink/5 last:border-0 {{ $enquiry->status->value === 'new' ? 'font-semibold' : '' }}">
                        <td class="px-4 py-3"><a href="{{ route('admin.enquiries.show', $enquiry) }}" class="hover:text-berry">{{ $enquiry->name }}</a><span class="block text-xs font-normal text-ink/60">{{ $enquiry->email }}</span></td>
                        <td class="px-4 py-3 font-normal">{{ $enquiry->service_interest ?? '—' }}</td>
                        <td class="px-4 py-3 font-normal text-ink/70">{{ \Illuminate\Support\Str::limit($enquiry->message, 80) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-normal">{{ $enquiry->created_at->timezone('Asia/Colombo')->format('j M Y, g:i a') }}</td>
                        <td class="px-4 py-3"><x-admin.status :status="$enquiry->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-ink/60">No enquiries here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $enquiries->links() }}</div>
</x-layouts.admin>
