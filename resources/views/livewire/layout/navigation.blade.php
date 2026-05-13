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

<aside class="w-72 bg-white border-r border-gray-100 flex flex-col h-full sticky top-0 z-20">
    <!-- Header: Logo & Title -->
    <div class="p-8">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Fundlink Logo" class="w-full h-full object-contain rounded-2xl shadow-lg shadow-blue-100">
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-800 leading-tight">
                    {{ auth()->user()->isAdmin() ? 'Administration' : 'User Panel' }}
                </h1>
                <p class="text-[10px] text-gray-400 font-medium leading-tight uppercase tracking-widest">Sistem Manajemen Keuangan</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-6 py-4 space-y-1 overflow-y-auto">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link :href="route('transactions')" :active="request()->routeIs('transactions')" icon="receipt">
            Transaksi
        </x-sidebar-link>

        @if(auth()->user()->isAdmin())
            <x-sidebar-link :href="route('units')" :active="request()->routeIs('units')" icon="office">
                Unit
            </x-sidebar-link>

            <x-sidebar-link :href="route('users')" :active="request()->routeIs('users')" icon="users">
                Pengguna
            </x-sidebar-link>
        @endif
    </nav>

    <!-- Logout Area -->
    <div class="p-6">
        <button wire:click="logout" class="w-full group flex items-center space-x-4 p-4 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 font-bold">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="text-sm">Keluar</span>
        </button>
    </div>
</aside>
