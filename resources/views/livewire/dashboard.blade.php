<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:px-8 sm:py-6">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Laporan Harian</p>
            <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">Dashboard operasional proyek</h2>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl shadow-slate-950/10">
                <p class="text-sm uppercase tracking-[0.25em] text-slate-300">Proyek Aktif</p>
                <p class="mt-4 text-4xl font-semibold">{{ $activeProjectCount }}</p>
                <p class="mt-3 text-sm text-slate-300">Master proyek yang masih bisa dipakai untuk input laporan.</p>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Laporan Hari Ini</p>
                <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $reportCountToday }}</p>
                <p class="mt-3 text-sm text-slate-500">Jumlah laporan dengan tanggal {{ now()->translatedFormat('d F Y') }}.</p>
            </div>

            <div class="rounded-3xl bg-amber-100 p-6 shadow-sm ring-1 ring-amber-200">
                <p class="text-sm uppercase tracking-[0.25em] text-amber-700">Akses Cepat</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('projects.create') }}" wire:navigate class="rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white">Tambah Proyek</a>
                    <a href="{{ route('reports.create') }}" wire:navigate class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-900 ring-1 ring-amber-300">Buat Laporan</a>
                </div>
            </div>
        </div>

        <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Laporan terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">Ringkasan laporan terakhir yang masuk.</p>
                </div>
                <a href="{{ route('reports.index') }}" wire:navigate class="text-sm font-medium text-amber-700">Lihat semua</a>
            </div>

            <div class="mobile-card-list mt-4">
                @forelse ($latestReports as $latestReport)
                    <a href="{{ route('reports.show', $latestReport) }}" wire:navigate class="mobile-card block">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $latestReport->project->nama_proyek }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $latestReport->mandor_pelapor }}</p>
                            </div>
                            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">{{ count($latestReport->photoUrls()) }} foto</span>
                        </div>
                        <p class="mt-3 text-xs uppercase tracking-[0.25em] text-slate-400">{{ $latestReport->tanggal->format('d M Y') }}</p>
                    </a>
                @empty
                    <div class="mobile-card text-center text-slate-500">Belum ada laporan yang tersimpan.</div>
                @endforelse
            </div>

            <div class="mt-6 hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="py-3 pe-4 font-medium">Tanggal</th>
                            <th class="py-3 pe-4 font-medium">Proyek</th>
                            <th class="py-3 pe-4 font-medium">Mandor/Pelapor</th>
                            <th class="py-3 pe-4 font-medium">Foto</th>
                            <th class="py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestReports as $latestReport)
                            <tr class="text-slate-700">
                                <td class="py-4 pe-4">{{ $latestReport->tanggal->format('d M Y') }}</td>
                                <td class="py-4 pe-4">{{ $latestReport->project->nama_proyek }}</td>
                                <td class="py-4 pe-4">{{ $latestReport->mandor_pelapor }}</td>
                                <td class="py-4 pe-4">{{ count($latestReport->photoUrls()) }}</td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('reports.show', $latestReport) }}" wire:navigate class="font-medium text-slate-900">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">Belum ada laporan yang tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
