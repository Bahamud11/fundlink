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
    <!-- Icon from the mockup -->
    <div class="inline-flex items-center justify-center p-6 bg-blue-600 rounded-[2rem] shadow-2xl shadow-blue-200 mb-8">
        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    </div>

    <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Administration</h2>
    <p class="text-gray-400 font-medium mb-12">Sistem Manajemen Keuangan Yayasan</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="login" class="space-y-6 text-left">
        <!-- Email Address (Label: Nama) -->
        <div class="space-y-2">
            <label for="email" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Nama</label>
            <div class="relative group">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus 
                    class="w-full pl-14 pr-6 py-4 bg-white border-gray-100 border rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-transparent font-bold text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="Masukkan Nama">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password (Label: Kata Kunci) -->
        <div class="space-y-2">
            <label for="password" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Kata Kunci</label>
            <div class="relative group">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password" type="password" name="password" required 
                    class="w-full pl-14 pr-6 py-4 bg-white border-gray-100 border rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-transparent font-bold text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-2xl text-base font-black shadow-xl shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] active:scale-95 transition-all duration-200">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-8">
            <p class="text-sm font-bold text-gray-400">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline ml-1">Daftar</a>
            </p>
        </div>
    </form>
</div>
