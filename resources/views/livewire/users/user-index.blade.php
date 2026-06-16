<div class="mobile-shell">
    <div class="mobile-stack mx-auto flex max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="mobile-panel bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:px-8 sm:py-6">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">User</p>
            <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">Daftar user dan role</h2>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <x-input-label for="search" value="Cari user" />
            <x-text-input id="search" type="text" class="mt-2 block w-full" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email" />
        </div>

        <div class="mobile-panel overflow-hidden bg-white shadow-sm ring-1 ring-slate-200">
            <div class="mobile-card-list p-4">
                @forelse ($users as $user)
                    <div class="mobile-card">
                        <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                        <select wire:change="updateRole({{ $user->id }}, $event.target.value)" class="mt-4 block w-full rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="pic" @selected($user->hasRole('pic'))>PIC</option>
                            <option value="admin" @selected($user->hasRole('admin'))>Admin</option>
                        </select>
                    </div>
                @empty
                    <div class="mobile-card text-center text-slate-500">Belum ada user.</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Nama</th>
                            <th class="px-6 py-4 font-medium">Email</th>
                            <th class="px-6 py-4 font-medium">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="text-slate-700">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <select wire:change="updateRole({{ $user->id }}, $event.target.value)" class="rounded-2xl border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                                        <option value="pic" @selected($user->hasRole('pic'))>PIC</option>
                                        <option value="admin" @selected($user->hasRole('admin'))>Admin</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-500">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
