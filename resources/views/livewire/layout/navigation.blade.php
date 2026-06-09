<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

{{-- Single root element required by Livewire --}}
<div class="shrink-0 lg:w-64 xl:w-[303px]">

    {{-- Backdrop (mobile only) --}}
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

    {{-- Sidebar: mobile = slide-in drawer, desktop = fixed to viewport --}}
    <aside :class="openSidebar ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-72 bg-white border border-gray-100 shadow-sm flex flex-col z-50
                  transform transition-transform duration-300 ease-in-out
                  rounded-r-[32px] overflow-hidden
                  lg:translate-x-0 lg:w-64 lg:inset-y-8 lg:left-8 lg:rounded-[32px] lg:z-30
                  xl:w-[303px]">

        {{-- Mobile close button --}}
        <div class="lg:hidden absolute right-4 top-5">
            <button @click="openSidebar = false"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-50 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Logo & Title --}}
        <div class="mx-4 xl:mx-6 mt-5 xl:mt-6 flex items-center gap-2.5 xl:gap-3 shrink-0">
            <div class="w-11 h-11 xl:w-14 xl:h-14 shrink-0">
                <img src="{{ asset('images/logo.svg') }}" alt="Fundlink Logo"
                     class="w-full h-full object-contain rounded-2xl shadow-lg shadow-blue-100">
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-base xl:text-xl font-semibold text-[#272727] leading-none tracking-normal truncate">
                    {{ auth()->user()->isAdmin() ? 'Administration' : 'User Panel' }}
                </h1>
                <p class="text-[9px] xl:text-[10px] font-light text-[#929292] leading-none tracking-normal mt-1">
                    Sistem Manajemen Keuangan Yayasan
                </p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 mx-4 xl:mx-6 mt-6 xl:mt-8 flex flex-col justify-between overflow-y-auto no-scrollbar pb-4">
            <div class="flex flex-col gap-2 xl:gap-3">
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
            </div>

            {{-- User info & logout --}}
            <div class="flex flex-col gap-2 xl:gap-3 mt-6">
                <div class="flex items-center gap-2.5 xl:gap-3 px-2 py-1">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}"
                             alt="{{ auth()->user()->name }}"
                             class="h-9 w-9 xl:h-10 xl:w-10 rounded-full object-cover shadow-sm shrink-0">
                    @else
                        <div class="h-9 w-9 xl:h-10 xl:w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black shadow-sm shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-gray-500 font-medium uppercase tracking-widest truncate">
                            {{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}
                        </p>
                    </div>
                </div>

                <button wire:click="logout"
                        class="w-full group flex items-center gap-3 xl:gap-4 h-11 xl:h-12 px-3 xl:px-4 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 font-bold shrink-0">
                    <img src="{{ asset('images/logout.svg') }}"
                         class="h-5 w-5 xl:h-6 xl:w-6 object-contain opacity-70 group-hover:opacity-100 transition-opacity shrink-0"
                         alt="Keluar icon">
                    <span class="font-['Inter'] font-medium text-base xl:text-xl text-[#929292] group-hover:text-red-600 leading-none tracking-[-0.03em]">
                        Keluar
                    </span>
                </button>
            </div>
        </nav>
    </aside>
</div>
