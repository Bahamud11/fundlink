<x-app-layout>
    <div>
        <!-- Header -->
        <div class="w-full flex items-center justify-between mb-5 lg:mb-8 shrink-0">
            <div class="flex flex-col justify-center">
                <h2 class="font-['Inter'] font-bold text-2xl sm:text-3xl lg:text-[40px] text-black leading-none tracking-[-0.03em]">Profil Saya</h2>
                <p class="font-['Inter'] font-light text-base lg:text-[24px] text-[#545454] mt-1 leading-none tracking-[-0.03em]">Kelola informasi akun dan keamanan Anda.</p>
            </div>
            <!-- Notification Bell -->
            <a href="{{ route('notifications') }}" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors duration-200 shrink-0">
                @php
                    $hasUnreadNotif = auth()->user()->notifications()->where('is_read', false)->exists();
                @endphp
                <img src="{{ asset($hasUnreadNotif ? 'images/notifred.svg' : 'images/notif.svg') }}" class="h-7 w-7 lg:h-9 lg:w-9 object-contain" alt="Notifikasi">
            </a>
        </div>

        <div class="w-full space-y-3 lg:space-y-6">
            <!-- Update Profile Information -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-5 lg:p-8">
                <livewire:profile.update-profile-information-form />
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-5 lg:p-8">
                <livewire:profile.update-password-form />
            </div>

            <!-- Delete Account -->
            <div class="bg-rose-50/40 rounded-[32px] border border-rose-100/60 shadow-sm p-5 lg:p-8">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
