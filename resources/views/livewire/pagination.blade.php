@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between w-full">

        {{-- Bagian Kiri: Info Halaman --}}
        <div class="hidden sm:block">
            <p class="font-['Inter'] font-light text-[16px] text-[#929292] leading-none tracking-[-0.03em]">
                Menampilkan <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span> hingga <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span> dari <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> pengguna
            </p>
        </div>

        {{-- Bagian Kanan: Tombol Navigasi --}}
        <div class="flex items-center gap-[8px]">

            {{-- Tombol "Sebelumnya" --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center justify-center px-[20px] h-[48px] rounded-2xl bg-gray-50 border border-gray-100 text-gray-300 font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em] cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="flex items-center justify-center px-[20px] h-[48px] rounded-2xl bg-white border border-gray-200 text-[#545454] font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em] hover:bg-gray-50 hover:shadow-sm transition-all duration-200 active:scale-95">
                    Sebelumnya
                </button>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Pemisah Tiga Titik "..." --}}
                @if (is_string($element))
                    <span class="flex items-center justify-center min-w-[48px] h-[48px] rounded-2xl bg-transparent text-[#929292] font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em]">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Halaman Aktif --}}
                            <span class="flex items-center justify-center min-w-[48px] h-[48px] rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-200 font-['Inter'] font-bold text-[16px] leading-none tracking-[-0.03em]">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Halaman Tidak Aktif --}}
                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="flex items-center justify-center min-w-[48px] h-[48px] rounded-2xl bg-white border border-gray-200 text-[#545454] font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em] hover:bg-gray-50 hover:shadow-sm transition-all duration-200 active:scale-95">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol "Selanjutnya" --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="flex items-center justify-center px-[20px] h-[48px] rounded-2xl bg-white border border-gray-200 text-[#545454] font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em] hover:bg-gray-50 hover:shadow-sm transition-all duration-200 active:scale-95">
                    Selanjutnya
                </button>
            @else
                <span class="flex items-center justify-center px-[20px] h-[48px] rounded-2xl bg-gray-50 border border-gray-100 text-gray-300 font-['Inter'] font-medium text-[16px] leading-none tracking-[-0.03em] cursor-not-allowed">
                    Selanjutnya
                </span>
            @endif

        </div>
    </nav>
@endif
