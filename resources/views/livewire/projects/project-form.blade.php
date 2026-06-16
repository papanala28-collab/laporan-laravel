<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-5xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:px-8 sm:py-6">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Master Proyek</p>
            <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">{{ $project ? 'Edit proyek' : 'Tambah proyek' }}</h2>
        </div>

        <form wire:submit="save" class="mobile-panel space-y-5 bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:space-y-6 sm:p-8">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="kode_proyek" value="Kode proyek" />
                    <x-text-input id="kode_proyek" type="text" class="mt-2 block w-full" wire:model="kode_proyek" />
                    <x-input-error :messages="$errors->get('kode_proyek')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nama_proyek" value="Nama proyek" />
                    <x-text-input id="nama_proyek" type="text" class="mt-2 block w-full" wire:model="nama_proyek" />
                    <x-input-error :messages="$errors->get('nama_proyek')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="lokasi" value="Lokasi" />
                    <x-text-input id="lokasi" type="text" class="mt-2 block w-full" wire:model="lokasi" />
                    <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label value="PIC proyek" />
                    <div class="mt-2 grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                        @forelse ($picUsers as $picUser)
                            <label class="flex items-center gap-3 rounded-xl bg-white px-3 py-3 text-sm text-slate-700 ring-1 ring-slate-200">
                                <input type="checkbox" wire:model="pic_user_ids" value="{{ $picUser->id }}" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                <span>
                                    <span class="block font-medium text-slate-900">{{ $picUser->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $picUser->email }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="rounded-xl bg-white px-4 py-3 text-sm text-slate-500 ring-1 ring-slate-200">
                                Belum ada user role PIC. Atur role user di menu User.
                            </div>
                        @endforelse
                    </div>
                    <x-input-error :messages="$errors->get('pic_user_ids')" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('pic_user_ids.*'))->flatten()->all()" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <x-input-label value="Tenaga kerja proyek" />
                            <p class="mt-1 text-xs text-slate-500">Nama pekerja di sini otomatis muncul sebagai daftar absen saat membuat laporan.</p>
                        </div>
                    </div>

                    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed divide-y divide-slate-200 text-sm">
                                <colgroup>
                                    <col class="w-14">
                                    <col>
                                    <col class="w-40">
                                    <col class="w-20 sm:w-24">
                                </colgroup>
                                <thead class="bg-slate-50 text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">No</th>
                                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Nama pekerja</th>
                                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Jabatan</th>
                                        <th class="px-3 py-3 text-right text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($workers as $index => $worker)
                                        <tr>
                                            <td class="px-3 py-3 align-top text-center sm:px-4">
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="px-3 py-3 sm:px-4">
                                                <x-text-input type="text" class="block w-full" wire:model="workers.{{ $index }}.name" placeholder="Nama pekerja" />
                                            </td>
                                            <td class="px-3 py-3 sm:px-4">
                                                <x-text-input type="text" class="block w-full" wire:model="workers.{{ $index }}.job_title" placeholder="Contoh: Tukang" />
                                            </td>
                                            <td class="px-3 py-3 text-right align-middle sm:px-4">
                                                <button type="button" wire:click="removeWorker({{ $index }})" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600 transition hover:bg-rose-50">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 flex justify-end">
                        <button type="button" wire:click="addWorker" class="rounded-full border border-amber-300 px-3 py-2 text-xs font-medium text-amber-700">
                            Tambah pekerja
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('workers')" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('workers.*.name'))->flatten()->all()" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('workers.*.job_title'))->flatten()->all()" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="klien" value="Klien" />
                    <x-text-input id="klien" type="text" class="mt-2 block w-full" wire:model="klien" />
                    <x-input-error :messages="$errors->get('klien')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status_aktif" value="Status proyek" />
                    <select id="status_aktif" wire:model="status_aktif" class="mt-2 block w-full rounded-2xl border-slate-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status_aktif')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="keterangan" value="Keterangan" />
                <textarea id="keterangan" wire:model="keterangan" rows="5" class="mt-2 block w-full rounded-3xl border-slate-300 focus:border-amber-500 focus:ring-amber-500"></textarea>
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>

            <div class="sticky bottom-3 flex flex-col-reverse gap-3 rounded-[1.5rem] border-t border-slate-200 bg-white/95 pt-5 backdrop-blur sm:static sm:flex-row sm:justify-end sm:bg-transparent">
                <a href="{{ route('projects.index') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 sm:w-auto">Batal</a>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Simpan proyek</button>
            </div>
        </form>
    </div>
</div>
