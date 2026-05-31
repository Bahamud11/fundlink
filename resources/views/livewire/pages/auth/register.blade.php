<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        event(new Registered($user));

        Auth::login($user);

        // Tandai session bahwa user ini perlu verifikasi OTP
        // OTP akan di-generate & dikirim otomatis di OtpVerification::mount()
        session(['otp_pending' => true]);

        $this->redirect(route('otp.verify', absolute: false), navigate: true);
    }
}; ?>

<div class="text-center">
    <!-- Logo -->
    <div class="flex justify-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Fundlink Logo" class="h-16 w-auto object-contain">
    </div>

    <h2 class="text-3xl font-bold tracking-[-0.03em] text-gray-900 mb-1">Daftar Akun</h2>
    <p class="text-gray-400 font-light text-base tracking-[-0.03em] mb-6">Lengkapi Informasi Berikut</p>

    <form wire:submit="register" class="space-y-4 text-left">
        <!-- Name Field -->
        <div class="space-y-1">
            <label for="name" class="text-xl font-light text-gray-900 tracking-[-0.03em] ml-1">Nama</label>
            <div class="relative group">
                <div class="absolute left-[10px] top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <img src="{{ asset('images/profile.svg') }}" alt="Profile Icon" class="h-5 w-5">
                </div>
                <input wire:model="name" id="name" type="text" name="name" required autofocus
                    class="w-full h-[48px] pl-[40px] pr-[10px] py-[12px] bg-white border-[#545454] border-[0.3px] rounded-[8px] focus:ring-0 focus:border-[#545454] text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm"
                    placeholder="Masukkan Nama">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Field -->
        <div class="space-y-1">
            <label for="email" class="text-xl font-light text-gray-900 tracking-[-0.03em] ml-1">Email</label>
            <div class="relative group">
                <div class="absolute left-[10px] top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <img src="{{ asset('images/mail.svg') }}" alt="Profile Icon" class="h-5 w-5">
                </div>
                <input wire:model="email" id="email" type="email" name="email" required
                    class="w-full h-[48px] pl-[40px] pr-[10px] py-[12px] bg-white border-[#545454] border-[0.3px] rounded-[8px] focus:ring-0 focus:border-[#545454] text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm"
                    placeholder="Masukkan Email">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password Field -->
        <div class="space-y-1">
            <label for="password" class="text-xl font-light text-gray-900 tracking-[-0.03em] ml-1">Kata Kunci</label>
            <div class="relative group">
                <div class="absolute left-[10px] top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <img src="{{ asset('images/lock.svg') }}" alt="Lock Icon" class="h-5 w-5">
                </div>
                <input wire:model="password" id="password" type="password" name="password" required
                    class="w-full h-[48px] pl-[40px] pr-[10px] py-[12px] bg-white border-[#545454] border-[0.3px] rounded-[8px] focus:ring-0 focus:border-[#545454] text-gray-900 placeholder:text-gray-300 transition-all duration-200 text-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full h-[42px] flex items-center justify-center gap-[4px] rounded-[4px] px-[24px] bg-blue-600 text-white shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all duration-200
                    font-medium text-[16px] leading-[100%] tracking-[-0.03em]">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-4">
            <p class="font-light text-[16px] leading-[100%] tracking-[-0.03em] text-gray-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline ml-1" wire:navigate>Masuk</a>
            </p>
        </div>
    </form>
</div>
