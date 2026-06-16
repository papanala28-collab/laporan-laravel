<div class="space-y-3">
    <x-input-label :for="$field.'_0'" :value="$label" />

    <div class="space-y-2">
        @foreach ($this->{$field} as $index => $value)
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">{{ $index + 1 }}</span>
                <x-text-input
                    :id="$field.'_'.$index"
                    type="text"
                    class="block w-full"
                    wire:model="{{ $field }}.{{ $index }}"
                    :placeholder="$placeholder"
                />
                <button type="button" wire:click="removeLine('{{ $field }}', {{ $index }})" class="shrink-0 rounded-full border border-rose-200 p-2.5 text-rose-600 transition hover:bg-rose-50" title="Hapus">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>

    <div class="flex justify-end mt-2">
        <button type="button" wire:click="addLine('{{ $field }}')" class="rounded-full border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-50">
            + Tambah baris
        </button>
    </div>

    <x-input-error :messages="$errors->get($field)" class="mt-2" />
    <x-input-error :messages="collect($errors->get($field.'.*'))->flatten()->all()" class="mt-2" />
</div>
