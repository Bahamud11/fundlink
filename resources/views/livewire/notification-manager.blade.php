<div>
    <!-- Header -->
    <div class="w-full flex items-center justify-between mb-5 lg:mb-8 shrink-0">
        <div class="flex flex-col justify-center">
            <h2 class="font-['Inter'] font-bold text-2xl sm:text-3xl lg:text-[40px] text-black leading-none tracking-[-0.03em]">Notifikasi</h2>
            <p class="font-['Inter'] font-light text-base lg:text-[24px] text-[#545454] mt-1 leading-none tracking-[-0.03em]">Pusat informasi dan peringatan sistem.</p>
        </div>
        <button wire:click="markAllAsRead"
            class="flex items-center gap-2 px-4 lg:px-5 h-11 lg:h-12 rounded-2xl border border-gray-200 bg-white shadow-sm font-['Inter'] font-medium text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:border-blue-200 hover:text-blue-600 transition-all duration-200 shrink-0">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="hidden sm:inline">Tandai Semua Dibaca</span>
            <span class="sm:hidden">Tandai Semua</span>
        </button>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="flex flex-col gap-3 lg:gap-4">
        @forelse($notifications as $notification)
        <div class="group bg-white w-full px-4 lg:px-6 py-4 lg:py-5 rounded-3xl border transition-all duration-300 flex items-start gap-3 lg:gap-6 relative shrink-0
            {{ $notification->is_read
                ? 'border-transparent opacity-60 hover:opacity-80 hover:border-gray-100 hover:shadow-md'
                : 'border-l-[4px] border-l-blue-600 border-t-transparent border-r-transparent border-b-transparent hover:shadow-xl hover:shadow-gray-100/50' }}">

            <!-- Icon -->
            <div class="p-2 lg:p-3 rounded-2xl shrink-0 {{ $notification->is_read ? 'bg-gray-50' : 'bg-blue-50 group-hover:bg-blue-600 group-hover:scale-110' }} transition-all duration-300">
                <img src="{{ asset($notification->is_read ? 'images/notif.svg' : 'images/notifred.svg') }}"
                    class="h-7 w-7 lg:h-9 lg:w-9 object-contain {{ $notification->is_read ? 'opacity-50' : 'group-hover:brightness-0 group-hover:invert' }} transition-all duration-300"
                    alt="Notifikasi">
            </div>

            <!-- Konten -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-['Inter'] font-normal text-base lg:text-[24px] text-[#7D7D7D] leading-none tracking-[-0.03em]">{{ $notification->title }}</p>
                        <p class="font-['Inter'] font-light text-sm lg:text-[16px] text-[#929292] mt-2 leading-relaxed tracking-[-0.03em]">{{ $notification->message }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 mt-1">
                        <span class="font-['Inter'] font-light text-[12px] text-[#929292] leading-none tracking-[-0.03em] hidden sm:block">{{ $notification->created_at->diffForHumans() }}</span>
                        @if(!$notification->is_read)
                        <button wire:click="markAsRead({{ $notification->id }})"
                            class="p-2 bg-blue-50 text-blue-600 rounded-xl lg:opacity-0 lg:group-hover:opacity-100 transition-all duration-200 hover:bg-blue-600 hover:text-white hover:scale-110 active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        @else
                        <div class="w-8 h-8"></div>
                        @endif
                    </div>
                </div>
                <span class="font-['Inter'] font-light text-[11px] text-[#929292] leading-none tracking-[-0.03em] mt-1 block sm:hidden">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @empty
        <div class="w-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100 shrink-0">
            <div class="flex flex-col items-center gap-3 lg:gap-4">
                <div class="p-6 bg-gray-50 rounded-full shadow-sm">
                    <img src="{{ asset('images/notif.svg') }}" class="h-12 w-12 object-contain opacity-20" alt="Notifikasi">
                </div>
                <p class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#929292] leading-none tracking-[-0.03em]">Tidak ada notifikasi</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-5 lg:mt-8 w-full">
        {{ $notifications->links() }}
    </div>
</div>
