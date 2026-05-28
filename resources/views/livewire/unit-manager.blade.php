<div>
    @if (session()->has('message'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3 text-emerald-600 animate-in fade-in slide-in-from-top-4 duration-300">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-xs font-black uppercase tracking-wider">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Unit</h2>
        <p class="text-gray-400 font-medium mt-1 text-lg">Daftar unit institusi yang dinaungi yayasan.</p>
    </div>

    @if($isEditing)
        <!-- Centered Modal for Unit Input -->
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar relative">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $unitId ? 'Edit Detail Unit' : 'Tambahkan Unit' }}</h2>
                        <p class="text-gray-400 font-medium mt-0.5 text-xs">Daftarkan unit baru ke dalam sistem.</p>
                    </div>
                    <button type="button" wire:click="$set('isEditing', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Nama Unit -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Unit</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="text" wire:model="name" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan nama...">
                        </div>
                        @error('name') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jumlah Anggota -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jumlah Anggota</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="number" wire:model="member_count_input" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan jumlah anggota...">
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lokasi</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model="address" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                                <option value="">Pilih lokasi</option>
                                <option value="Bogor">Bogor</option>
                                <option value="Depok">Depok</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bekasi">Bekasi</option>
                                <option value="Tangerang">Tangerang</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('address') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Dana Awal -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dana Awal</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="number" wire:model="initial_balance" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan dana awal...">
                        </div>
                        @error('initial_balance') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-6 flex space-x-3">
                        <button type="button" wire:click="$set('isEditing', false)" class="flex-1 py-3 border border-blue-600 rounded-xl text-xs font-bold text-blue-600 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all">
                            {{ $unitId ? 'Simpan' : 'Buat' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Sticky Search and Actions Bar -->
    <div class="sticky top-14 lg:top-0 z-20 bg-gray-50/80 backdrop-blur-md pt-4 pb-4 -mt-4 -mx-4 px-4 sm:-mx-8 sm:px-8 mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="relative w-full md:flex-1 bg-white border border-gray-200 rounded-xl px-4 py-2.5 shadow-sm group">
                <input type="text" wire:model.live="search" class="w-full bg-transparent border-none p-0 pr-8 text-sm font-medium text-gray-800 placeholder-gray-300 focus:ring-0" placeholder="Cari cabang...">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <button wire:click="create" class="w-full md:w-auto flex items-center justify-center space-x-2 px-8 py-4 bg-blue-600 text-white rounded-2xl text-sm font-bold shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Unit</span>
            </button>
        </div>
    </div>

    <!-- Unit List (Card-based) -->
    <div class="space-y-4 sm:space-y-6">
        @forelse($units as $unit)
            <div wire:click="viewDetail({{ $unit->id }})" class="group cursor-pointer bg-white p-5 sm:p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Icon Indicator -->
                    <div class="p-3 sm:p-4 rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col">
                        <h4 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">{{ $unit->name }}</h4>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">{{ $unit->users_count }} Anggota • {{ $unit->address ?? 'Pusat' }}</p>
                    </div>
                </div>

                <div class="text-left sm:text-right pl-14 sm:pl-0">
                    <p class="text-xl sm:text-2xl font-black text-gray-700 tracking-tight">
                        Rp {{ number_format($unit->balance, 0, ',', '.') }},00
                    </p>
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                <p class="text-gray-400 font-bold">Tidak ada unit ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $units->links() }}
    </div>

    @if($selectedUnit)
        <div class="fixed inset-0 z-[60] overflow-hidden pointer-events-none bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="min-h-screen flex items-center justify-center p-4 pointer-events-none">
                <div class="pointer-events-auto bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="p-8 text-left">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 rounded-2xl bg-blue-50 text-blue-600 shadow-inner shrink-0">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900 tracking-tight">{{ $selectedUnit->name }}</h2>
                                    <p class="text-gray-400 font-bold mt-1 text-[10px] uppercase tracking-widest">{{ $selectedUnit->address ?? 'Pusat' }}</p>
                                </div>
                            </div>
                            <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 transition-all hover:scale-110 active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Balance -->
                        <div class="mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Saldo Unit</span>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight break-all">
                                Rp {{ number_format($selectedUnit->balance, 0, ',', '.') }},00
                            </h1>
                        </div>

                        <!-- Details -->
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah Anggota</span>
                                <span class="text-sm font-black text-gray-900">{{ $selectedUnit->users_count }} Orang</span>
                            </div>
                            <div class="border-t border-gray-50"></div>
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lokasi</span>
                                <span class="text-sm font-black text-gray-900">{{ $selectedUnit->address ?? 'Pusat' }}</span>
                            </div>
                            @if($selectedUnit->google_maps_url)
                            <div class="border-t border-gray-50"></div>
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Google Maps</span>
                                <a href="{{ $selectedUnit->google_maps_url }}" target="_blank" class="text-sm font-black text-blue-600 hover:text-blue-800 transition-colors">Lihat Peta →</a>
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="pt-6 border-t border-gray-50 flex space-x-3">
                            <button wire:click="edit({{ $selectedUnit->id }})" class="flex-1 flex items-center justify-center space-x-2 py-4 bg-blue-600 text-white rounded-2xl text-xs font-black shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.2em]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button onclick="confirm('Hapus unit ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $selectedUnit->id }})" class="flex-1 flex items-center justify-center space-x-2 py-4 bg-rose-50 text-rose-600 rounded-2xl text-xs font-black hover:bg-rose-100 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.2em]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
