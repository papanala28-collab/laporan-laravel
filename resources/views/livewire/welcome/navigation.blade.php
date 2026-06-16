<nav class="-mx-2 flex flex-1 flex-wrap justify-start gap-2 sm:justify-end">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="inline-flex items-center rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 backdrop-blur transition hover:text-slate-900"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="inline-flex items-center rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 backdrop-blur transition hover:text-slate-900"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
