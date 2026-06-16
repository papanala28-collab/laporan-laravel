<div class="mx-auto max-w-4xl bg-white p-8 print:max-w-none print:p-0">
    <div class="border-b border-slate-300 pb-6">
        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-700">Laporan Harian Proyek</p>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-slate-900">{{ $report->project->nama_proyek }}</h1>
                <p class="mt-2 text-sm text-slate-600">Tanggal laporan: {{ $report->tanggal->translatedFormat('d F Y') }}</p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white print:hidden">Print sekarang</button>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Nama Proyek</p>
            <p class="mt-2 text-sm font-medium text-slate-800">{{ $report->project->nama_proyek }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Kode Proyek</p>
            <p class="mt-2 text-sm font-medium text-slate-800">{{ $report->project->kode_proyek }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Mandor/Pelapor</p>
            <p class="mt-2 text-sm font-medium text-slate-800">{{ $report->mandor_pelapor }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Cuaca</p>
            <p class="mt-2 text-sm font-medium text-slate-800">{{ $report->cuaca }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Foto</p>
            <p class="mt-2 text-sm font-medium text-slate-800">{{ count($report->photoUrls()) }} lampiran</p>
        </div>
    </div>

    <div class="mt-8 space-y-5">
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

        <section class="rounded-[1.75rem] border border-slate-200 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Catatan</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->catatan ?: '-' }}</p>
        </section>

        @if ($report->photoUrls() !== [])
            <section class="rounded-[1.75rem] border border-slate-200 p-6">
                <h2 class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-500">Foto Laporan</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach ($report->photoUrls() as $photo)
                        <img src="{{ Storage::url($photo) }}" alt="Foto laporan" class="h-56 w-full rounded-2xl object-cover">
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
