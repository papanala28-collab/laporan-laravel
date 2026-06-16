<div class="space-y-3">
    <x-input-label value="Material" />

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed divide-y divide-slate-200 text-sm">
                <colgroup>
                    <col class="w-14">
                    <col>
                    <col class="w-32 sm:w-44">
                    <col class="w-20 sm:w-24">
                </colgroup>
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">No</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Material</th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Qty</th>
                        <th class="px-3 py-3 text-right text-[11px] font-semibold uppercase tracking-[0.16em] sm:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($material_rows as $index => $row)
                        <tr>
                            <td class="px-3 py-3 align-top text-center sm:px-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <x-text-input type="text" class="block w-full" wire:model="material_rows.{{ $index }}.name" placeholder="Nama material" />
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <x-text-input type="text" class="block w-full" wire:model="material_rows.{{ $index }}.qty" placeholder="Contoh: 10 sak" />
                            </td>
                            <td class="px-3 py-3 text-right align-middle sm:px-4">
                                <button type="button" wire:click="removeMaterialRow({{ $index }})" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600 transition hover:bg-rose-50">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button" wire:click="addMaterialRow" class="rounded-full border border-amber-300 px-3 py-2 text-xs font-medium text-amber-700 sm:px-3 sm:py-1">
            Tambah material
        </button>
    </div>

    <x-input-error :messages="$errors->get('material_rows')" class="mt-2" />
    <x-input-error :messages="collect($errors->get('material_rows.*.name'))->flatten()->all()" class="mt-2" />
    <x-input-error :messages="collect($errors->get('material_rows.*.qty'))->flatten()->all()" class="mt-2" />
</div>
