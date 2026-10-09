@php $groupLabels = ['contact' => 'Contact details', 'social' => 'Social links', 'seo' => 'Search engines']; @endphp
<x-layouts.admin title="Contact & social">
    <x-admin.header title="Contact & social" subtitle="Used across the header, footer, contact page and WhatsApp links. Empty social links are hidden." />
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-8">
        @csrf @method('PUT')
        @foreach ($groups as $group => $settings)
            <section class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-ink/5">
                <h2 class="font-display text-lg font-semibold">{{ $groupLabels[$group] ?? ucfirst($group) }}</h2>
                @foreach ($settings as $setting)
                    <x-admin.field :label="$setting->label" name="settings[{{ $setting->key }}]" :type="match ($setting->type) { 'email' => 'email', 'url' => 'url', 'phone' => 'tel', 'textarea' => 'textarea', default => 'text' }" :rows="3" :value="$setting->value" :help="$setting->help" />
                @endforeach
            </section>
        @endforeach
        <button class="rounded-full bg-berry px-8 py-3 font-semibold text-paper hover:bg-plum">Save settings</button>
    </form>
</x-layouts.admin>
