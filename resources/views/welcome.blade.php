<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laporan Harian') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600;instrument-sans:500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative min-h-screen overflow-hidden bg-[linear-gradient(160deg,_#fff7ed_0%,_#f8fafc_40%,_#e0f2fe_100%)]">
            <div class="absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.35),_transparent_55%)]"></div>

            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-8">
                <header class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <x-application-logo class="h-14 w-14 text-amber-600" />
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.35em] text-amber-700">Pladen 62</p>
                            <h1 class="mt-1 text-2xl font-semibold text-slate-900">Laporan Harian Proyek</h1>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </header>

                <main class="grid flex-1 items-center gap-10 py-12 lg:grid-cols-[1.1fr_0.9fr]">
                    <section>
                        <div class="inline-flex rounded-full border border-amber-200 bg-white/80 px-4 py-2 text-sm text-amber-700 shadow-sm backdrop-blur">
                            Ringkas, rapi, dan siap print untuk laporan lapangan
                        </div>
                        <h2 class="mt-8 max-w-3xl font-['Instrument_Sans'] text-5xl font-semibold leading-tight text-slate-950 sm:text-6xl">
                            Catat progres proyek harian tanpa ribet.
                        </h2>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                            Aplikasi ini dibuat untuk memudahkan input laporan harian proyek, memilih proyek aktif, menyimpan progres, lalu menampilkan hasilnya dalam format detail yang siap dicetak.
                        </p>

                        <div class="mt-10 flex flex-wrap gap-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-slate-900 px-6 py-3 text-sm font-medium text-white shadow-lg shadow-slate-900/10">
                                Masuk ke aplikasi
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white/80 px-6 py-3 text-sm font-medium text-slate-700 backdrop-blur">
                                    Buat akun
                                </a>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-[2rem] bg-white/85 p-6 shadow-2xl shadow-slate-900/10 ring-1 ring-white/60 backdrop-blur sm:p-8">
                        <div class="rounded-[1.75rem] bg-slate-900 p-6 text-white">
                            <p class="text-sm uppercase tracking-[0.3em] text-slate-300">Isi Form</p>
                            <div class="mt-5 space-y-3">
                                <div class="rounded-2xl bg-white/10 px-4 py-3">Tanggal dan proyek</div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">Mandor/pelapor dan cuaca</div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">Tenaga kerja dan progress</div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">Uraian pekerjaan, material, kendala</div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">Rencana besok dan catatan</div>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-3xl border border-slate-200 bg-white p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Modul Proyek</p>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Simpan daftar proyek aktif agar pencarian laporan lebih mudah.</p>
                            </div>
                            <div class="rounded-3xl border border-slate-200 bg-white p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Print View</p>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Buka detail laporan lalu cetak langsung lewat browser.</p>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
