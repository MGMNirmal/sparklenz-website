<x-layouts.admin title="Dashboard">
    <x-admin.header title="Welcome back" subtitle="A quick view of the site. Changes you make here appear on the public site straight away." />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['New enquiries', $newEnquiries, route('admin.enquiries.index', ['status' => 'new'])],
            ['Published projects', $publishedProjects, route('admin.projects.index')],
            ['Draft projects', $draftProjects, route('admin.projects.index')],
            ['Concept (demo) projects', $demoProjects, route('admin.projects.index')],
        ] as [$label, $count, $url])
            <a href="{{ $url }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5 transition hover:ring-berry/40">
                <p class="text-sm text-ink/60">{{ $label }}</p>
                <p class="mt-2 font-display text-4xl font-semibold">{{ $count }}</p>
            </a>
        @endforeach
    </div>

    @if ($demoProjects)
        <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ $demoProjects }} concept project(s) are flagged as demo content. They are hidden on the live site unless <code>SHOW_DEMO_CONTENT=true</code>. Replace them with approved work, or untick “Concept / demo” once a project is real.
        </p>
    @endif

    <section class="mt-10">
        <h2 class="font-display text-xl font-semibold">Latest enquiries</h2>
        <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-ink/5">
            @forelse ($recentEnquiries as $enquiry)
                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="flex flex-wrap items-center justify-between gap-3 border-b border-ink/5 px-5 py-4 last:border-0 hover:bg-paper">
                    <span><span class="font-semibold">{{ $enquiry->name }}</span> <span class="text-ink/60">· {{ $enquiry->service_interest ?? 'General enquiry' }}</span></span>
                    <span class="flex items-center gap-3 text-sm text-ink/60">
                        <x-admin.status :status="$enquiry->status" />
                        {{ $enquiry->created_at->timezone('Asia/Colombo')->diffForHumans() }}
                    </span>
                </a>
            @empty
                <p class="px-5 py-6 text-ink/60">No enquiries yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.admin>
