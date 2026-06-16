<div class="space-y-3">
    <x-input-label value="Material" />

    <div class="space-y-3">
        @foreach ($material_rows as $index => $row)
            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:bg-transparent sm:border-0 sm:p-0">
                <span class="hidden sm:inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                <div class="flex-1">
                    <x-text-input type="text" class="block w-full bg-white sm:bg-transparent" wire:model="material_rows.{{ $index }}.name" placeholder="Nama material" />
                </div>
                <div class="flex-1">
                    <x-text-input type="text" class="block w-full bg-white sm:bg-transparent" wire:model="material_rows.{{ $index }}.qty" placeholder="Contoh: 10 sak" />
                </div>
                <div class="flex justify-end sm:block">
                    <button type="button" wire:click="removeMaterialRow({{ $index }})" class="shrink-0 flex items-center justify-center gap-2 rounded-full border border-rose-200 px-4 py-2 sm:p-2.5 text-sm sm:text-base text-rose-600 transition hover:bg-rose-50" title="Hapus">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="sm:hidden font-medium">Hapus</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex justify-end mt-2">
        <button type="button" wire:click="addMaterialRow" class="rounded-full border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-50">
            + Tambah material
        </button>
    </div>

    <x-input-error :messages="$errors->get('material_rows')" class="mt-2" />
    <x-input-error :messages="collect($errors->get('material_rows.*.name'))->flatten()->all()" class="mt-2" />
    <x-input-error :messages="collect($errors->get('material_rows.*.qty'))->flatten()->all()" class="mt-2" />
</div>
