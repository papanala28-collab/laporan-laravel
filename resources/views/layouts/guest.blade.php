<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laporan Harian') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        
        <!-- PWA Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#f59e0b">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;instrument-sans:500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="relative min-h-screen overflow-hidden bg-[linear-gradient(160deg,_#fff7ed_0%,_#f8fafc_42%,_#e0f2fe_100%)]">
            <div class="absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.3),_transparent_55%)]"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col justify-center px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div class="grid items-center gap-6 lg:grid-cols-[1fr_28rem] lg:gap-10">
                    <section class="hidden lg:block">
                        <div class="max-w-xl">
                            <div class="inline-flex rounded-full border border-amber-200 bg-white/80 px-4 py-2 text-sm font-medium text-amber-700 shadow-sm backdrop-blur">
                                Aplikasi Laporan Harian Proyek
                            </div>
                            <h1 class="mt-7 font-['Instrument_Sans'] text-5xl font-semibold leading-tight text-slate-950">
                                Input laporan lapangan yang tetap nyaman dari layar ponsel.
                            </h1>
                            <p class="mt-6 text-lg leading-8 text-slate-600">
                                Login, register, verifikasi email, dan kelola laporan harian dalam satu alur yang ringan, cepat, dan siap dipakai di lapangan.
                            </p>
                        </div>
                    </section>

                    <div class="mx-auto w-full max-w-md">
                        <a href="/" wire:navigate class="mb-5 flex items-center justify-center gap-3 lg:justify-start">
                            <x-application-logo class="h-14 w-14 text-amber-600" />
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium uppercase tracking-[0.3em] text-amber-700">Pladen 62</p>
                                <p class="truncate text-lg font-semibold text-slate-900">Laporan Harian</p>
                            </div>
                        </a>

                        <div class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/92 px-5 py-6 shadow-2xl shadow-slate-900/10 backdrop-blur sm:px-8 sm:py-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PWA Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </body>
</html>
