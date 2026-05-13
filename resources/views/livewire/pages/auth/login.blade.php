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
    <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-600 rounded-[1.5rem] mb-6">
        <svg class="h-12 w-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <!-- Simplified Cube -->
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
            <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
            <line x1="12" y1="22.08" x2="12" y2="12" />
            <!-- Coins placeholder - in the mockup it's quite specific, but Lucide-style package is close -->
        </svg>
    </div>

    <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-1">Fundlink Account</h2>
    <p class="text-gray-400 font-medium text-sm mb-10 uppercase tracking-widest">Sistem Manajemen Keuangan</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="login" class="space-y-6 text-left">
        <!-- Name Field -->
        <div class="space-y-2">
            <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email / Identitas</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="email" name="email" required autofocus 
                    class="w-full pl-12 pr-4 py-4 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="Masukkan Email">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password Field -->
        <div class="space-y-2">
            <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Kunci</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password" type="password" name="password" required 
                    class="w-full pl-12 pr-4 py-4 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all duration-200 uppercase tracking-widest">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline ml-1">Daftar</a>
            </p>
        </div>
    </form>
</div>
