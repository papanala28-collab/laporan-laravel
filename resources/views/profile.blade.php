<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Akun</p>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="mobile-shell">
        <div class="mobile-stack mx-auto flex max-w-5xl flex-col px-4 sm:px-6 lg:px-8">
            <div class="mobile-panel bg-white/80 px-5 py-5 shadow-sm ring-1 ring-white/60 backdrop-blur sm:px-8 sm:py-6">
                <p class="text-sm leading-6 text-slate-600">Kelola nama, email, password, dan pengaturan akunmu dari tampilan yang nyaman dipakai di mobile.</p>
            </div>

            <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="max-w-2xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="max-w-2xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="mobile-panel bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="max-w-2xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
