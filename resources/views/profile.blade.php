<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
        <p class="text-gray-400 font-medium mt-1 text-sm">Kelola informasi akun dan keamanan Anda.</p>
    </x-slot>

    <div class="space-y-12 w-full">
        <div class="p-6 sm:p-10 bg-white rounded-3xl sm:rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="p-6 sm:p-10 bg-white rounded-3xl sm:rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="p-6 sm:p-10 bg-rose-50/30 rounded-3xl sm:rounded-[2.5rem] border border-rose-100/50 shadow-sm">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
