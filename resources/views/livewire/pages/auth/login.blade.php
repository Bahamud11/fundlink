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

<div class="text-center">
    <!-- Logo -->
    <div class="flex justify-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Fundlink Logo" class="h-16 w-auto object-contain">
    </div>

    <h2 class="text-3xl font-bold tracking-[-0.03em] text-gray-900 mb-0">Administration</h2>
    <p class="text-gray-400 font-light text-base tracking-[-0.03em] mb-6">Sistem Manajemen Keuangan Yayasan</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4 text-left">
        <!-- Name Field -->
        <div class="space-y-1">
           <label for="email" class="text-xl font-light text-gray-900 tracking-[-0.03em] ml-1">Nama / Email</label>
            <div class="relative group">
                <div class="absolute left-[10px] top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <img src="{{ asset('images/profile.svg') }}" alt="Profile Icon" class="h-5 w-5">
                </div>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                    class="w-full h-[48px] pl-[40px] pr-[10px] py-[12px] bg-white border-[#545454] border-[0.3px] rounded-[8px] focus:ring-0 focus:border-[#545454] text-gray-900 placeholder:text-gray-400 transition-all duration-200 text-[16px]"
                    placeholder="Masukkan Nama">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password Field -->
        <div class="space-y-1">
            <label for="password" class="text-xl font-light text-gray-900 tracking-[-0.03em] ml-1">Kata Kunci</label>
            <div class="relative group">
                <div class="absolute left-[10px] top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <img src="{{ asset('images/lock.svg') }}" alt="Lock Icon" class="h-5 w-5">
                </div>
                <input wire:model="form.password" id="password" type="password" name="password" required
                    class="w-full h-[48px] pl-[40px] pr-[10px] py-[12px] bg-white border-[#545454] border-[0.3px] rounded-[8px] focus:ring-0 focus:border-[#545454] text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-[436px] max-w-full h-[42px] opacity-100 flex items-center justify-center gap-[4px] rounded-[4px] px-[24px] bg-blue-600 text-white shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all duration-200
                    font-medium text-[16px] leading-[100%] tracking-[-0.03em]">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="font-light text-[16px] leading-[100%] tracking-[-0.03em] text-gray-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:underline ml-1">Daftar</a>
            </p>
        </div>
    </form>
</div>
