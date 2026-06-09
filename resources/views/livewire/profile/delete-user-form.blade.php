<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-[24px]">
    <!-- Section Header -->
    <div class="flex items-center gap-[12px]">
        <div class="p-3 rounded-2xl bg-rose-50 shadow-inner shrink-0">
            <svg class="h-6 w-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h2 class="font-['Inter'] font-bold text-[24px] text-gray-900 leading-none tracking-[-0.03em]">Hapus Akun</h2>
            <p class="font-['Inter'] font-light text-[14px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
    </div>

    <p class="font-['Inter'] font-light text-[15px] text-[#545454] leading-relaxed">
        Setelah akun dihapus, semua data dan informasi terkait akan dihapus secara permanen. Pastikan Anda telah menyimpan data yang diperlukan sebelum melanjutkan.
    </p>

    <!-- Trigger Button -->
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="flex items-center gap-[8px] px-[24px] h-[44px] rounded-xl bg-white border border-rose-200 text-rose-600 shadow-sm font-['Inter'] font-medium text-[14px] uppercase tracking-widest hover:bg-rose-600 hover:text-white hover:border-rose-600 hover:shadow-lg hover:shadow-rose-100 transition-all duration-200">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus Akun
    </button>

    <!-- Confirmation Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-8 space-y-[24px]">
            <!-- Modal Header -->
            <div class="flex items-center gap-[12px]">
                <div class="p-3 rounded-2xl bg-rose-50 shadow-inner shrink-0">
                    <svg class="h-6 w-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-['Inter'] font-bold text-[22px] text-gray-900 leading-none tracking-[-0.03em]">Konfirmasi Hapus Akun</h2>
                    <p class="font-['Inter'] font-light text-[13px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <p class="font-['Inter'] font-light text-[14px] text-[#545454] leading-relaxed">
                Setelah akun dihapus, semua data akan hilang secara permanen. Masukkan password Anda untuk mengkonfirmasi penghapusan.
            </p>

            <!-- Password Input -->
            <div class="space-y-[6px]">
                <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Password</label>
                <div class="border-b border-gray-100 focus-within:border-rose-500 transition-colors pb-2">
                    <input type="password" wire:model="password" id="password"
                        autocomplete="current-password" placeholder="••••••••"
                        class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300">
                </div>
                @error('password')
                    <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
                @enderror
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-[12px] pt-[8px]">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-[20px] h-[44px] rounded-xl border border-gray-200 bg-white font-['Inter'] font-medium text-[14px] text-[#545454] hover:bg-gray-50 transition-all uppercase tracking-widest">
                    Batal
                </button>
                <button type="submit"
                    class="px-[20px] h-[44px] rounded-xl bg-rose-600 text-white shadow-lg shadow-rose-100 font-['Inter'] font-medium text-[14px] hover:bg-rose-700 transition-all uppercase tracking-widest">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
