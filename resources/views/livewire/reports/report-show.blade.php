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
                    <section>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Foto Laporan</h3>
                        <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($report->photoUrls() as $photo)
                                <a href="{{ Storage::url($photo) }}" target="_blank" rel="noopener" class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                    <img src="{{ Storage::url($photo) }}" alt="Foto laporan" class="h-52 w-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
