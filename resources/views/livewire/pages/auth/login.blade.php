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
        <img src="{{ asset('images/logo.png') }}" alt="Fundlink Logo" class="h-16 w-auto object-contain">
    </div>

    <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1">Fundlink Account</h2>
    <p class="text-gray-400 font-medium text-xs mb-6 uppercase tracking-widest">Sistem Manajemen Keuangan</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4 text-left">
        <!-- Name Field -->
        <div class="space-y-1">
            <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email / Identitas</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus 
                    class="w-full pl-12 pr-4 py-3 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm" 
                    placeholder="Masukkan Email">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- Password Field -->
        <div class="space-y-1">
            <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Kunci</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password" type="password" name="password" required 
                    class="w-full pl-12 pr-4 py-3 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm" 
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-2xl text-xs font-black shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all duration-200 uppercase tracking-widest">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="text-xs text-gray-500">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline ml-1">Daftar</a>
            </p>
        </div>
    </form>
</div>
