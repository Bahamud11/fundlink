<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<aside class="w-72 bg-white border-r border-gray-100 flex flex-col h-full sticky top-0 shadow-sm z-20">
    <!-- Header: Logo & Role -->
    <div class="p-8 border-b border-gray-50">
        <div class="flex items-center space-x-4 mb-6">
            <div class="p-3 bg-blue-600 rounded-2xl shadow-xl shadow-blue-100">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Fundlink</h1>
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-600/70 mt-1 block">{{ auth()->user()->role === 'admin' ? 'Administrasi Pusat' : 'Pengelola Unit' }}</span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-4 flex items-center space-x-3">
            <div class="h-10 w-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600 font-bold border border-gray-100">
                {{ strtoupper(substr(auth()->user()->role, 0, 1)) }}
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Masuk sebagai</p>
                <p class="text-sm font-bold text-gray-800">{{ auth()->user()->role === 'admin' ? 'Admin Pusat' : 'PIC Unit' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Beranda
        </x-sidebar-link>

        <x-sidebar-link :href="route('transactions')" :active="request()->routeIs('transactions')" icon="receipt">
            Transaksi
        </x-sidebar-link>

        @if(auth()->user()->isAdmin())
            <x-sidebar-link :href="route('units')" :active="request()->routeIs('units')" icon="office">
                Unit Kerja
            </x-sidebar-link>

            <x-sidebar-link :href="route('users')" :active="request()->routeIs('users')" icon="users">
                Pengguna
            </x-sidebar-link>
        @endif
    </nav>

    <!-- Logout Area -->
    <div class="p-6 border-t border-gray-50">
        <button wire:click="logout" class="w-full group flex items-center space-x-4 p-4 rounded-2xl hover:bg-red-50 transition-all duration-200">
            <div class="p-2 rounded-xl bg-gray-50 group-hover:bg-red-100 transition-colors duration-200 text-gray-400 group-hover:text-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-500 group-hover:text-red-600 transition-colors duration-200">Log Out</span>
        </button>
    </div>
</aside>
