<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($this->photo) {
            $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <!-- Section Header -->
    <div class="flex items-center gap-[12px] mb-[28px]">
        <div class="p-3 rounded-2xl bg-blue-50 shadow-inner shrink-0">
            <img src="{{ asset('images/profile.svg') }}" class="h-6 w-6 object-contain" alt="Profil">
        </div>
        <div>
            <h2 class="font-['Inter'] font-bold text-[24px] text-gray-900 leading-none tracking-[-0.03em]">Informasi Profil</h2>
            <p class="font-['Inter'] font-light text-[14px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Perbarui nama, email, dan foto profil Anda.</p>
        </div>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-[24px]">
        <!-- Foto Profil -->
        <div class="flex items-center gap-[24px]">
            <!-- Avatar Preview -->
            <div class="shrink-0">
                @if($photo)
                    <img src="{{ $photo->temporaryUrl() }}" alt="Preview"
                        class="h-20 w-20 rounded-2xl object-cover shadow-lg border-2 border-blue-100">
                @elseif(Auth::user()->profile_photo_path)
                    <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}"
                        class="h-20 w-20 rounded-2xl object-cover shadow-lg border-2 border-blue-100">
                @else
                    <div class="h-20 w-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-['Inter'] font-bold text-[28px] shadow-lg shadow-blue-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Upload Button -->
            <div class="flex flex-col gap-[8px]">
                <label class="cursor-pointer group inline-flex items-center gap-[8px] px-[16px] h-[40px] rounded-xl bg-white border border-gray-200 shadow-sm hover:border-blue-200 hover:bg-blue-50 transition-all duration-200">
                    <img src="{{ asset('images/picture.svg') }}" class="h-4 w-4 object-contain opacity-50 group-hover:opacity-100 transition-opacity" alt="">
                    <span class="font-['Inter'] font-medium text-[13px] text-[#545454] group-hover:text-blue-600 uppercase tracking-widest transition-colors">Pilih Foto</span>
                    <input type="file" wire:model="photo" class="hidden" accept="image/*">
                </label>
                <div wire:loading wire:target="photo" class="flex items-center gap-[6px]">
                    <svg class="animate-spin h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-['Inter'] font-medium text-[11px] text-blue-600 uppercase tracking-widest">Mengunggah...</span>
                </div>
                <p class="font-['Inter'] font-light text-[11px] text-[#929292] leading-none">Format JPG, PNG, GIF. Maks. 2MB.</p>
                @error('photo')
                    <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Nama -->
        <div class="space-y-[6px]">
            <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Nama</label>
            <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                <input type="text" wire:model="name" id="name" autocomplete="name" required
                    class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300"
                    placeholder="Nama lengkap Anda">
            </div>
            @error('name')
                <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="space-y-[6px]">
            <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Email</label>
            <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2 flex items-center gap-2">
                <img src="{{ asset('images/mail.svg') }}" class="h-4 w-4 object-contain opacity-40 shrink-0" alt="">
                <input type="email" wire:model="email" id="email" autocomplete="username" required
                    class="flex-1 font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300"
                    placeholder="email@domain.com">
            </div>
            @error('email')
                <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span>
            @enderror

            @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="mt-2 p-4 bg-amber-50 border border-amber-100 rounded-2xl space-y-2">
                    <p class="font-['Inter'] font-medium text-[13px] text-amber-700 leading-relaxed">
                        Email Anda belum diverifikasi.
                        <button wire:click.prevent="sendVerification"
                            class="underline font-bold hover:text-amber-900 transition-colors">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>
                    @if(session('status') === 'verification-link-sent')
                        <p class="font-['Inter'] font-medium text-[12px] text-emerald-600 uppercase tracking-widest">
                            Link verifikasi telah dikirim.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-[12px] pt-[8px]">
            <button type="submit"
                class="flex items-center gap-[8px] px-[24px] h-[44px] rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200 font-['Inter'] font-medium text-[14px] uppercase tracking-widest hover:bg-blue-700 transition-all duration-200">
                Simpan Perubahan
            </button>

            <div x-data="{ show: false }"
                x-on:profile-updated.window="show = true; setTimeout(() => show = false, 3000)"
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
