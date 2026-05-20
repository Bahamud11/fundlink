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

<div class="shrink-0 lg:w-72">
    <!-- Backdrop Overlay (Only on Mobile when sidebar is open) -->
    <div x-show="openSidebar" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-40 lg:hidden backdrop-blur-sm"
         @click="openSidebar = false"
         style="display: none;"></div>

    <!-- Sidebar Aside -->
    <aside :class="openSidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-72 bg-white border-r border-gray-100 flex flex-col h-full z-50 lg:z-20 transform lg:transform-none lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
        
        <!-- Mobile Close Button inside aside -->
        <div class="lg:hidden absolute right-4 top-8">
            <button @click="openSidebar = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
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
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('transactions')" :active="request()->routeIs('transactions')" icon="transaksi">
                Transaksi
            </x-sidebar-link>

            @if(auth()->user()->isAdmin())
                <x-sidebar-link :href="route('units')" :active="request()->routeIs('units')" icon="cabang">
                    Unit
                </x-sidebar-link>

                <x-sidebar-link :href="route('users')" :active="request()->routeIs('users')" icon="profil">
                    Pengguna
                </x-sidebar-link>
            @endif

            @if(!auth()->user()->isAdmin())
            <x-sidebar-link :href="route('profile')" :active="request()->routeIs('profile')" icon="profil">
                Profil Saya
            </x-sidebar-link>
            @endif
        </nav>

        <!-- User Info & Logout -->
        <div class="p-6 border-t border-gray-50 flex flex-col space-y-4">
            <div class="flex items-center space-x-3 px-4">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover shadow-sm">
                @else
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-widest truncate">{{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}</p>
                </div>
            </div>
            
            <button wire:click="logout" class="w-full group flex items-center space-x-4 p-4 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 font-bold">
                <img src="{{ asset('images/logout.png') }}" class="h-5 w-5 object-contain opacity-70 group-hover:opacity-100 transition-opacity" alt="Keluar icon">
                <span class="text-sm">Keluar</span>
            </button>
        </div>
    </aside>
</div>
