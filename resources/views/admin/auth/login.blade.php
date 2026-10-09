<x-layouts.admin title="Sign in">
    <div class="flex min-h-screen items-center justify-center bg-plum px-5 py-12">
        <div class="w-full max-w-md rounded-3xl bg-paper p-8 shadow-2xl sm:p-10">
            <img src="{{ asset('images/brand/logo-dark.png') }}" alt="Sparklenz — Beyond the Frame" width="812" height="196" class="mb-8 w-44">
            <h1 class="font-display text-2xl font-semibold tracking-tight">Studio admin</h1>
            <p class="mt-1 text-ink/65">Sign in to manage work, rates and enquiries.</p>

            @if (session('status'))
                <p role="status" class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5">
                @csrf
                <x-admin.field label="Email" name="email" type="email" required autocomplete="username" />
                <x-admin.field label="Password" name="password" type="password" required autocomplete="current-password" />
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" class="size-4 accent-berry"> Keep me signed in on this device</label>
                <button class="w-full rounded-full bg-berry px-6 py-3.5 font-semibold text-paper transition hover:bg-plum focus:outline-none focus:ring-4 focus:ring-berry/30">Sign in</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
