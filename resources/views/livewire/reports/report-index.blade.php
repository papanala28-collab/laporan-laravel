<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel flex flex-col gap-4 bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:flex-row sm:items-end sm:justify-between sm:px-8 sm:py-6">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Laporan Harian</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">Daftar laporan</h2>
            </div>
            <a href="{{ route('reports.create') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Buat laporan</a>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mobile-panel grid gap-4 bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6 md:grid-cols-3">
            <div>
                <x-input-label for="project" value="Proyek" />
                <select id="project" wire:model.live="project" class="mt-2 block w-full rounded-2xl border-slate-300 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">Semua proyek</option>
                    @foreach ($projects as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="start_date" value="Tanggal mulai" />
                <x-text-input id="start_date" type="date" class="mt-2 block w-full" wire:model.live="start_date" />
            </div>

            <div>
                <x-input-label for="end_date" value="Tanggal akhir" />
                <x-text-input id="end_date" type="date" class="mt-2 block w-full" wire:model.live="end_date" />
            </div>
        </div>

        <div class="mobile-panel overflow-hidden bg-white shadow-sm ring-1 ring-slate-200">
            <div class="mobile-card-list p-4">
                @forelse ($reports as $report)
                    <a href="{{ route('reports.show', $report) }}" wire:navigate class="mobile-card block">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $report->project->nama_proyek }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $report->mandor_pelapor }}</p>
                            </div>
                            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">{{ count($report->photoUrls()) }} foto</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-600">
                            <p>{{ $report->tanggal->format('d M Y') }}</p>
                            <p>{{ $report->cuaca }}</p>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="inline-flex text-sm font-medium text-amber-700">Lihat detail</span>
                            @role('admin')
                                <button type="button" wire:click.prevent="delete({{ $report->id }})" wire:confirm="Yakin ingin menghapus laporan harian ini?" class="inline-flex text-sm font-medium text-red-600 relative z-10">Hapus</button>
                            @endrole
                        </div>
                    </a>
                @empty
                    <div class="mobile-card text-center text-slate-500">Belum ada laporan yang tersimpan.</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Tanggal</th>
                            <th class="px-6 py-4 font-medium">Proyek</th>
                            <th class="px-6 py-4 font-medium">Mandor/Pelapor</th>
                            <th class="px-6 py-4 font-medium">Cuaca</th>
                            <th class="px-6 py-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr class="text-slate-700">
                                <td class="px-6 py-4">{{ $report->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $report->project->nama_proyek }}</td>
                                <td class="px-6 py-4">{{ $report->mandor_pelapor }}</td>
                                <td class="px-6 py-4">{{ $report->cuaca }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-4">
                                        <a href="{{ route('reports.show', $report) }}" wire:navigate class="font-medium text-slate-900">Detail</a>
                                        <a href="{{ route('reports.edit', $report) }}" wire:navigate class="font-medium text-amber-700">Edit</a>
                                        @role('admin')
                                            <button type="button" wire:click="delete({{ $report->id }})" wire:confirm="Yakin ingin menghapus laporan harian ini?" class="font-medium text-red-600">Hapus</button>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada laporan yang tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
