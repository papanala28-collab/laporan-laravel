<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-6xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:px-8 sm:py-6">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Laporan Harian</p>
            <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">{{ $report ? 'Edit laporan' : 'Buat laporan baru' }}</h2>
        </div>

        <form wire:submit="save" class="mobile-panel space-y-5 bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:space-y-6 sm:p-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-2">
                <div>
                    <x-input-label for="tanggal" value="Tanggal" />
                    <x-text-input id="tanggal" type="date" class="mt-2 block w-full" wire:model="tanggal" />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Jam kerja (Mulai - Selesai)" />
                    <div class="mt-2 flex items-center gap-3">
                        <div class="w-full">
                            <x-text-input id="start_time" type="time" class="block w-full" wire:model="start_time" />
                        </div>
                        <span class="text-sm text-slate-500">s/d</span>
                        <div class="w-full">
                            <x-text-input id="end_time" type="time" class="block w-full" wire:model="end_time" />
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="project_id" value="Nama proyek" />
                    <select id="project_id" wire:model.live="project_id" class="mt-2 block w-full rounded-2xl border-slate-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Pilih proyek</option>
                        @foreach ($projects as $projectOption)
                            <option value="{{ $projectOption->id }}">{{ $projectOption->nama_proyek }} - {{ $projectOption->kode_proyek }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Mandor/Pelapor" />
                    <div class="mt-2 flex min-h-12 items-center rounded-2xl border border-slate-300 bg-slate-50 px-4 text-sm font-medium text-slate-700">
                        {{ $reporterName }}
                    </div>
                </div>

                <div>
                    <x-input-label for="cuaca" value="Cuaca" />
                    <x-text-input id="cuaca" type="text" class="mt-2 block w-full" wire:model="cuaca" placeholder="Contoh: Cerah" />
                    <x-input-error :messages="$errors->get('cuaca')" class="mt-2" />
                </div>
            </div>

            <div class="space-y-6">
                @include('livewire.reports.partials.line-table', [
                    'field' => 'uraian_pekerjaan_lines',
                    'label' => 'Uraian pekerjaan',
                    'placeholder' => 'Baris uraian pekerjaan',
                ])

                <div class="space-y-3">
                    <x-input-label value="Absensi tenaga kerja" />

                    @if (blank($project_id))
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                            Pilih proyek dulu, lalu daftar pekerja akan muncul otomatis.
                        </div>
                    @elseif ($attendanceWorkers->isEmpty())
                        <div class="rounded-3xl border border-amber-200 bg-amber-50 px-4 py-5 text-sm text-amber-800">
                            Proyek ini belum punya data tenaga kerja. Tambahkan pekerja di menu edit proyek.
                        </div>
                    @else
                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-fixed divide-y divide-slate-200 text-sm">
                                    <colgroup>
                                        <col class="w-14">
                                        <col>
                                        <col class="w-32">
                                    </colgroup>
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">No</th>
                                            <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Nama pekerja</th>
                                            <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Absen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($attendanceWorkers as $index => $worker)
                                            <tr>
                                                <td class="px-3 py-3 align-top text-center sm:px-4">
                                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                                                </td>
                                                <td class="px-3 py-3 sm:px-4">
                                                    <p class="font-medium text-slate-800">{{ $worker->name }}</p>
                                                    @if ($worker->job_title)
                                                        <p class="mt-1 text-xs text-slate-500">{{ $worker->job_title }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 sm:px-4">
                                                    <select wire:model="worker_attendance.{{ $worker->id }}" class="block w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                                                        @foreach ($attendanceStatuses as $statusValue => $statusLabel)
                                                            <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get('worker_attendance')" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('worker_attendance.*'))->flatten()->all()" class="mt-2" />
                </div>

                @include('livewire.reports.partials.material-table')

                @include('livewire.reports.partials.line-table', [
                    'field' => 'kendala_lines',
                    'label' => 'Daftar alat kerja',
                    'placeholder' => 'Baris alat kerja',
                ])
            </div>

            <div class="space-y-4">
                <div class="space-y-3">
                    <x-input-label value="Foto laporan" />
                    <label class="relative mt-2 inline-flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-2xl bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">
                        Tambah foto
                        <input
                            id="gallery_photos"
                            type="file"
                            accept="image/*"
                            multiple
                            wire:model="galleryPhotos"
                            class="absolute inset-0 cursor-pointer opacity-0"
                        >
                    </label>
                    <p class="mt-2 text-xs text-slate-500">Pilih dari galeri atau kamera HP. Maksimal 10 MB per foto.</p>
                    <div wire:loading wire:target="galleryPhotos" class="mt-2 text-xs font-medium text-amber-700">Memproses foto...</div>
                    <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('photos.*'))->flatten()->all()" class="mt-2" />
                    <x-input-error :messages="collect($errors->get('galleryPhotos.*'))->flatten()->all()" class="mt-2" />
                </div>

                @if ($existingPhotos !== [] || $photos !== [])
                    <div>
                        <p class="text-sm font-medium text-slate-800">Preview foto sementara</p>
                        <p class="mt-1 text-xs text-slate-500">Foto di bawah ini belum final sampai kamu menekan tombol simpan laporan. Kalau ada yang salah, hapus dulu dari sini.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($existingPhotos as $index => $photo)
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                <img src="{{ Storage::url($photo) }}" alt="Foto laporan {{ $index + 1 }}" class="h-44 w-full object-cover">
                                <div class="flex items-center justify-between px-4 py-3">
                                    <p class="text-sm font-medium text-slate-700">Foto tersimpan</p>
                                    <button type="button" wire:click="removeExistingPhoto({{ $index }})" class="text-xs font-medium text-rose-600">Hapus</button>
                                </div>
                            </div>
                        @endforeach

                        @foreach ($photos as $index => $photo)
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto {{ $index + 1 }}" class="h-44 w-full object-cover">
                                <div class="flex items-center justify-between px-4 py-3">
                                    <p class="text-sm font-medium text-slate-700">Foto sementara</p>
                                    <button type="button" wire:click="removeNewPhoto({{ $index }})" class="text-xs font-medium text-rose-600">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                        Belum ada foto yang diambil. Setelah ambil foto, thumbnail akan muncul di sini dan bisa dihapus kalau salah.
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="catatan" value="Catatan" />
                <textarea id="catatan" wire:model="catatan" rows="4" class="mt-2 block w-full rounded-3xl border-slate-300 focus:border-amber-500 focus:ring-amber-500"></textarea>
                <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
            </div>

            <div class="sticky bottom-3 flex flex-col-reverse gap-3 rounded-[1.5rem] border-t border-slate-200 bg-white/95 pt-5 backdrop-blur sm:static sm:flex-row sm:justify-end sm:bg-transparent">
                <a href="{{ route('reports.index') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 sm:w-auto">Batal</a>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-medium text-white sm:w-auto">Simpan laporan</button>
            </div>
        </form>
    </div>
</div>
