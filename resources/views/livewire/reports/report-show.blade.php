<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-5xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel flex flex-col gap-4 bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:flex-row sm:items-end sm:justify-between sm:px-8 sm:py-6">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Laporan Harian</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">{{ $report->project->nama_proyek }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $report->tanggal->translatedFormat('d F Y') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="{{ route('reports.edit', $report) }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 sm:w-auto">Edit</a>
                <a href="{{ route('reports.pdf', ['report' => $report, 'preview' => now()->timestamp]) }}" class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto" target="_blank" rel="noopener">PDF</a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Mandor/Pelapor</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $report->mandor_pelapor }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Cuaca</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $report->cuaca }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Jam Kerja</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $report->start_time && $report->end_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($report->end_time)->format('H:i') : '-' }}</p>
            </div>
            <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Foto</p>
                <p class="mt-3 text-sm font-semibold">{{ count($report->photoUrls()) }} lampiran</p>
            </div>
        </div>

        <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Kode Proyek</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $report->project->kode_proyek }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Lokasi</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $report->project->lokasi }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">PIC</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $report->project->picNames() ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Klien</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $report->project->klien }}</p>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                @include('livewire.reports.partials.show-table', [
                    'title' => 'Uraian Pekerjaan',
                    'lines' => $report->lineItems('uraian_pekerjaan'),
                ])

                @if ($report->workerAttendances->isNotEmpty())
                    @include('livewire.reports.partials.attendance-table', [
                        'attendances' => $report->workerAttendances,
                    ])
                @else
                    @include('livewire.reports.partials.show-table', [
                        'title' => 'Tenaga Kerja',
                        'lines' => $report->lineItems('tenaga_kerja'),
                    ])
                @endif

                @include('livewire.reports.partials.material-show-table', [
                    'items' => $report->materialItems(),
                ])

                @include('livewire.reports.partials.show-table', [
                    'title' => 'Daftar Alat Kerja',
                    'lines' => $report->lineItems('kendala'),
                ])

                <section>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Catatan</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->catatan ?: '-' }}</p>
                </section>

                @if ($report->photoUrls() !== [])
                    <section x-data="{ lightboxOpen: false, lightboxUrl: '' }">
                        <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Foto Laporan</h3>
                        <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200">
                            <div class="grid grid-cols-3 gap-[1px] bg-slate-200 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                                @foreach ($report->photoUrls() as $photo)
                                    <button type="button" @click="lightboxOpen = true; lightboxUrl = '{{ Storage::url($photo) }}'" class="group relative block aspect-square w-full overflow-hidden bg-slate-100 focus:outline-none focus:ring-inset focus:ring-4 focus:ring-amber-500/50">
                                        <img src="{{ Storage::url($photo) }}" alt="Foto laporan" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Lightbox Modal -->
                        <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4 sm:p-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <button type="button" @click="lightboxOpen = false" class="absolute right-4 top-4 z-[110] rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>

                            <div class="relative flex h-full w-full flex-col items-center justify-center" @click.self="lightboxOpen = false">
                                <img :src="lightboxUrl" class="max-h-full max-w-full rounded-lg object-contain shadow-2xl" @click.outside="lightboxOpen = false">
                            </div>

                            <div class="absolute bottom-6 left-1/2 flex w-[calc(100%-3rem)] -translate-x-1/2 items-center justify-center rounded-3xl bg-slate-900/80 px-6 py-4 shadow-xl backdrop-blur-md sm:w-auto sm:min-w-80">
                                <div>
                                    <p class="text-xs text-center uppercase tracking-wider text-slate-400">Tanggal Laporan</p>
                                    <p class="mt-0.5 text-center text-base font-semibold text-white">{{ $report->tanggal->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
