<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <!-- Section Header -->
    <div class="flex items-center gap-[12px] mb-[28px]">
        <div class="p-3 rounded-2xl bg-blue-50 shadow-inner shrink-0">
            <img src="{{ asset('images/lock.svg') }}" class="h-6 w-6 object-contain" alt="Password">
        </div>
        <div>
            <h2 class="font-['Inter'] font-bold text-[24px] text-gray-900 leading-none tracking-[-0.03em]">Ubah Password</h2>
            <p class="font-['Inter'] font-light text-[14px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Gunakan password yang panjang dan acak agar akun tetap aman.</p>
        </div>
    </div>

    <form wire:submit="updatePassword" class="space-y-[24px]">
        <!-- Password Saat Ini -->
        <div class="space-y-[6px]">
            <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Password Saat Ini</label>
            <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                <input type="password" wire:model="current_password" id="update_password_current_password"
                    autocomplete="current-password"
                    class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300"
                    placeholder="••••••••">
            </div>
            @error('current_password')
                <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password Baru -->
        <div class="space-y-[6px]">
            <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Password Baru</label>
            <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                <input type="password" wire:model="password" id="update_password_password"
                    autocomplete="new-password"
                    class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300"
                    placeholder="••••••••">
            </div>
            @error('password')
                <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
            @enderror
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="space-y-[6px]">
            <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Konfirmasi Password Baru</label>
            <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                <input type="password" wire:model="password_confirmation" id="update_password_password_confirmation"
                    autocomplete="new-password"
                    class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300"
                    placeholder="••••••••">
            </div>
            @error('password_confirmation')
                <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-[12px] pt-[8px]">
            <button type="submit"
                class="flex items-center gap-[8px] px-[24px] h-[44px] rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200 font-['Inter'] font-medium text-[14px] uppercase tracking-widest hover:bg-blue-700 transition-all duration-200">
                Perbarui Password
            </button>

            <div x-data="{ show: false }"
                x-on:password-updated.window="show = true; setTimeout(() => show = false, 3000)"
                x-show="show" x-transition
                class="flex items-center gap-[6px] px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-['Inter'] font-bold text-[11px] uppercase tracking-widest">Tersimpan</span>
            </div>
        </div>
    </form>
</section>
