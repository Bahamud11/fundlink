<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-col">
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Notifikasi</h2>
                <p class="text-gray-400 font-medium mt-1 text-sm">Pusat informasi dan peringatan sistem Anda.</p>
            </div>
            <button wire:click="markAllAsRead" class="w-full sm:w-auto px-6 py-3 bg-gray-100 text-gray-600 rounded-xl text-xs font-black hover:bg-blue-600 hover:text-white transition-all duration-300">
                Tandai Semua Dibaca
            </button>
        </div>
    </x-slot>

    <div class="w-full space-y-4">
        @forelse($notifications as $notification)
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start gap-4 sm:gap-6 relative group transition-all duration-200 {{ $notification->is_read ? 'opacity-60' : 'border-l-4 border-l-blue-600' }}">
                <div class="p-3 sm:p-4 rounded-2xl {{ $notification->is_read ? 'bg-gray-50 text-gray-400' : 'bg-blue-50 text-blue-600' }} shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                
                <div class="flex-1 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                        <h4 class="text-base sm:text-lg font-black text-gray-900 pr-8">{{ $notification->title }}</h4>
                        <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-gray-500 leading-relaxed">{{ $notification->message }}</p>
                </div>

                @if(!$notification->is_read)
                    <button wire:click="markAsRead({{ $notification->id }})" class="absolute top-4 right-4 sm:top-6 sm:right-6 p-2 bg-blue-50 text-blue-600 rounded-lg lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                @endif
            </div>
        @empty
            <div class="py-20 text-center">
                <div class="p-6 bg-gray-50 rounded-full inline-block mb-6">
                    <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Tidak Ada Notifikasi</h3>
                <p class="text-gray-400 font-medium mt-2">Semua pesan sistem telah dibaca atau kosong.</p>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
