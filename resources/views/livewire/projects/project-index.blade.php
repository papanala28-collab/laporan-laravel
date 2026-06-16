<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel flex flex-col gap-4 bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:flex-row sm:items-end sm:justify-between sm:px-8 sm:py-6">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Master Proyek</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">Daftar proyek</h2>
            </div>
            @role('admin')
                <a href="{{ route('projects.create') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Tambah proyek</a>
            @endrole
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mobile-panel grid gap-4 bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6 md:grid-cols-3">
            <div class="md:col-span-2">
                <x-input-label for="search" value="Cari proyek" />
                <x-text-input id="search" type="text" class="mt-2 block w-full" wire:model.live.debounce.300ms="search" placeholder="Cari kode, nama, lokasi, PIC, atau klien" />
            </div>
            <div>
                <x-input-label for="status" value="Status" />
                <select id="status" wire:model.live="status" class="mt-2 block w-full rounded-2xl border-slate-300 focus:border-amber-500 focus:ring-amber-500">
                    <option value="">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="mobile-panel overflow-hidden bg-white shadow-sm ring-1 ring-slate-200">
            <div class="mobile-card-list p-4">
                @forelse ($projects as $project)
                    <div class="mobile-card">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $project->nama_proyek }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $project->kode_proyek }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $project->status_aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                {{ $project->status_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-slate-600">
                            <p><span class="font-medium text-slate-800">Lokasi:</span> {{ $project->lokasi }}</p>
                            <p><span class="font-medium text-slate-800">PIC:</span> {{ $project->picNames() ?: '-' }}</p>
                            <p><span class="font-medium text-slate-800">Klien:</span> {{ $project->klien }}</p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-4">
                            <a href="{{ route('projects.show', $project) }}" wire:navigate class="inline-flex text-sm font-medium text-slate-900">Lihat project</a>
                            @role('admin')
                                <a href="{{ route('projects.edit', $project) }}" wire:navigate class="inline-flex text-sm font-medium text-amber-700">Edit proyek</a>
                                <button type="button" wire:click="delete({{ $project->id }})" wire:confirm="Yakin ingin menghapus proyek ini? (Semua laporan di dalamnya juga akan terhapus)" class="inline-flex text-sm font-medium text-red-600">Hapus</button>
                            @endrole
                        </div>
                    </div>
                @empty
                    <div class="mobile-card text-center text-slate-500">Belum ada proyek yang tersimpan.</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Kode</th>
                            <th class="px-6 py-4 font-medium">Nama proyek</th>
                            <th class="px-6 py-4 font-medium">Lokasi</th>
                            <th class="px-6 py-4 font-medium">PIC</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            <tr class="text-slate-700">
                                <td class="px-6 py-4 font-medium">{{ $project->kode_proyek }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900">{{ $project->nama_proyek }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $project->klien }}</p>
                                </td>
                                <td class="px-6 py-4">{{ $project->lokasi }}</td>
                                <td class="px-6 py-4">{{ $project->picNames() ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $project->status_aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $project->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" wire:navigate class="font-medium text-slate-900">Lihat</a>
                                    @role('admin')
                                        <a href="{{ route('projects.edit', $project) }}" wire:navigate class="ml-4 font-medium text-amber-700">Edit</a>
                                        <button type="button" wire:click="delete({{ $project->id }})" wire:confirm="Yakin ingin menghapus proyek ini? (Semua laporan di dalamnya juga akan terhapus)" class="ml-4 font-medium text-red-600">Hapus</button>
                                    @endrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada proyek yang tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
