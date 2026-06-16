<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div>
        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-600">Masuk</p>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Akses laporan harian</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Masuk untuk membuka dashboard proyek dan input laporan dari perangkat apa pun.</p>
    </div>

    <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="mt-2 block w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-amber-700 hover:text-amber-800" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input wire:model="form.password" id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <label for="remember" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-slate-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
            <span class="text-sm text-slate-600">{{ __('Remember me') }}</span>
        </label>

        <div class="flex flex-col gap-3 pt-2">
            <x-primary-button class="w-full justify-center rounded-full px-5 py-3">
                {{ __('Log in') }}
            </x-primary-button>

            @if (Route::has('register'))
                <a class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700" href="{{ route('register') }}" wire:navigate>
                    {{ __('Create account') }}
                </a>
            @endif
        </div>
    </form>
</div>
