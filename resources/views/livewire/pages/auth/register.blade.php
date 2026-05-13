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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="text-center">
    <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-1">Daftar Akun</h2>
    <p class="text-gray-400 font-medium text-sm mb-10 uppercase tracking-widest">Sistem Manajemen Keuangan</p>

    <form wire:submit="register" class="space-y-6 text-left">
        <!-- Email Field -->
        <div class="space-y-2">
            <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input wire:model="email" id="email" type="email" name="email" required autofocus 
                    class="w-full pl-12 pr-4 py-4 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="Masukkan Email">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Name Field -->
        <div class="space-y-2">
            <label for="name" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="name" id="name" type="text" name="name" required 
                    class="w-full pl-12 pr-4 py-4 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="Masukkan Nama">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                <input wire:model="password" id="password" type="password" name="password" required 
                    class="w-full pl-12 pr-12 py-4 bg-white border-gray-100 border rounded-2xl shadow-sm focus:ring-0 focus:border-blue-600 text-gray-900 placeholder:text-gray-300 transition-all duration-200" 
                    placeholder="Masukkan Password">
                <!-- Right icon from mockup (dice/dots) -->
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-width="1.5" />
                        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" stroke="none" />
                        <circle cx="15.5" cy="8.5" r="1.5" fill="currentColor" stroke="none" />
                        <circle cx="8.5" cy="15.5" r="1.5" fill="currentColor" stroke="none" />
                        <circle cx="15.5" cy="15.5" r="1.5" fill="currentColor" stroke="none" />
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all duration-200 uppercase tracking-widest">
                Konfirmasi
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline ml-1">Masuk</a>
            </p>
        </div>
    </form>
</div>
