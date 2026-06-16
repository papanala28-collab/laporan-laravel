<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel flex flex-col gap-4 bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:flex-row sm:items-end sm:justify-between sm:px-8 sm:py-6">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Detail Proyek</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">{{ $project->nama_proyek }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $project->kode_proyek }} - {{ $project->lokasi }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('projects.index') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 sm:w-auto">Kembali</a>
                <a href="{{ route('projects.attendance-pdf', ['project' => $project, 'start_date' => $start_date, 'end_date' => $end_date, 'preview' => now()->timestamp]) }}" class="inline-flex w-full items-center justify-center rounded-full border border-amber-300 bg-amber-50 px-5 py-3 text-sm font-medium text-amber-800 sm:w-auto" target="_blank" rel="noopener">PDF Absensi</a>
                @role('admin')
                    <a href="{{ route('projects.edit', $project) }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Edit project</a>
                @endrole
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Klien</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $project->klien }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">PIC</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $project->picNames() ?: '-' }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Status</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $project->status_aktif ? 'Aktif' : 'Nonaktif' }}</p>
            </div>
            <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Periode</p>
                <p class="mt-3 text-sm font-semibold">{{ $start_date }} s/d {{ $end_date }}</p>
            </div>
        </div>

        <div class="mobile-panel grid gap-4 bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6 md:grid-cols-2">
            <div>
                <x-input-label for="start_date" value="Dari tanggal" />
                <x-text-input id="start_date" type="date" class="mt-2 block w-full" wire:model.live="start_date" />
            </div>
            <div>
                <x-input-label for="end_date" value="Sampai tanggal" />
                <x-text-input id="end_date" type="date" class="mt-2 block w-full" wire:model.live="end_date" />
            </div>
        </div>

        <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Pekerjaan Dilaporkan</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">Daftar laporan project</h3>
                </div>
                <a href="{{ route('reports.create') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Buat laporan</a>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($reports as $report)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $report->tanggal->translatedFormat('d F Y') }}</p>
                                <p class="mt-1 text-xs text-slate-500">Pelapor: {{ $report->mandor_pelapor }} - Cuaca: {{ $report->cuaca }}</p>
                            </div>
                            <a href="{{ route('reports.show', $report) }}" wire:navigate class="text-sm font-medium text-amber-700">Lihat laporan</a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada laporan pada rentang tanggal ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-5 border-t border-slate-200 pt-4">
                {{ $reports->links() }}
            </div>
        </div>

        <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Rekap Absensi</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">Absensi pekerja per tanggal</h3>
                </div>
                <div class="flex flex-col gap-3 sm:items-end">
                    <div class="flex flex-wrap gap-2 text-xs font-medium">
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-800">Hadir</span>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">Setengah hari</span>
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-800">Tidak hadir</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-500">Belum ada laporan</span>
                    </div>
                    <a href="{{ route('projects.attendance-pdf', ['project' => $project, 'start_date' => $start_date, 'end_date' => $end_date, 'preview' => now()->timestamp]) }}" class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-xs font-medium text-white sm:w-auto" target="_blank" rel="noopener">Preview PDF</a>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-max divide-y divide-slate-200 text-sm">
                        <colgroup>
                            <col class="w-14">
                            <col class="w-48">
                            <col class="w-24">
                            @foreach ($attendanceDates as $date)
                                <col class="w-20">
                            @endforeach
                        </colgroup>
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="sticky left-0 z-20 bg-slate-50 px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">No</th>
                                <th class="sticky left-14 z-20 bg-slate-50 px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Pekerja</th>
                                <th class="sticky left-[15.5rem] z-20 bg-slate-50 px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Total HK</th>
                                @foreach ($attendanceDates as $date)
                                    <th class="px-2 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.08em]">
                                        <span class="block text-slate-700">{{ $date['label'] }}</span>
                                        <span class="mt-1 block text-[10px] font-medium normal-case tracking-normal text-slate-400">{{ $date['day'] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($attendanceRows as $index => $row)
                                <tr class="text-slate-700">
                                    <td class="sticky left-0 z-10 bg-white px-3 py-3 text-center sm:px-4">{{ $index + 1 }}</td>
                                    <td class="sticky left-14 z-10 bg-white px-3 py-3 sm:px-4">
                                        <p class="font-medium text-slate-900">{{ $row['worker_name'] }}</p>
                                        @if ($row['job_title'])
                                            <p class="mt-1 text-xs text-slate-500">{{ $row['job_title'] }}</p>
                                        @endif
                                    </td>
                                    <td class="sticky left-[15.5rem] z-10 bg-white px-3 py-3 text-center sm:px-4">
                                        <span class="inline-flex min-w-14 items-center justify-center rounded-2xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">
                                            {{ number_format($row['total_hk'], fmod($row['total_hk'], 1.0) === 0.0 ? 0 : 1) }}
                                        </span>
                                    </td>
                                    @foreach ($attendanceDates as $date)
                                        @php($status = $row['dates'][$date['key']] ?? null)
                                        @php($statusClass = match ($status) {
                                            'hadir' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                                            'setengah_hari' => 'bg-amber-100 text-amber-800 ring-amber-200',
                                            'tidak_hadir' => 'bg-rose-100 text-rose-800 ring-rose-200',
                                            default => 'bg-slate-50 text-slate-300 ring-slate-100',
                                        })
                                        @php($statusShort = match ($status) {
                                            'hadir' => 'H',
                                            'setengah_hari' => '1/2',
                                            'tidak_hadir' => 'TH',
                                            default => '-',
                                        })
                                        <td class="px-2 py-3 text-center">
                                            <span class="inline-flex h-9 min-w-14 items-center justify-center rounded-2xl px-3 text-xs font-semibold ring-1 {{ $statusClass }}" title="{{ $status ? $attendanceStatuses[$status] : 'Belum ada laporan' }}">
                                                {{ $statusShort }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 3 + $attendanceDates->count() }}" class="px-6 py-10 text-center text-slate-500">Belum ada data tenaga kerja pada project ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if (!empty($projectPhotos))
            <div x-data="{ lightboxOpen: false, lightboxUrl: '', lightboxDate: '', lightboxReportUrl: '' }" class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div class="mb-5">
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Galeri</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">Dokumentasi Laporan</h3>
                    <p class="mt-1 text-sm text-slate-500">Kumpulan foto dari seluruh laporan harian pada rentang tanggal terpilih.</p>
                </div>
                
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="grid grid-cols-3 gap-[1px] bg-slate-200 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                        @foreach ($projectPhotos as $photo)
                            <button type="button" @click="lightboxOpen = true; lightboxUrl = '{{ Storage::url($photo['url']) }}'; lightboxDate = '{{ $photo['date']->format('d M Y') }}'; lightboxReportUrl = '{{ route('reports.show', $photo['report_id']) }}'" class="group relative block aspect-square w-full overflow-hidden bg-slate-100 focus:outline-none focus:ring-inset focus:ring-4 focus:ring-amber-500/50">
                                <img src="{{ Storage::url($photo['url']) }}" alt="Foto laporan" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
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

                    <div class="absolute bottom-6 left-1/2 flex w-[calc(100%-3rem)] -translate-x-1/2 items-center justify-between rounded-3xl bg-slate-900/80 px-6 py-4 shadow-xl backdrop-blur-md sm:w-auto sm:min-w-80">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-slate-400">Tanggal Laporan</p>
                            <p class="mt-0.5 text-base font-semibold text-white" x-text="lightboxDate"></p>
                        </div>
                        <a :href="lightboxReportUrl" wire:navigate class="ml-6 shrink-0 rounded-full bg-amber-500 px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-amber-400">Ke laporan</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
