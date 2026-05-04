<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Unit Yayasan</h2>
                <p class="text-gray-400 font-medium mt-1">Kelola unit institusi di seluruh organisasi.</p>
            </div>
            <button wire:click="create" class="px-8 py-3 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-200 hover:scale-105 transition-all duration-300">
                Tambah Unit Baru
            </button>
        </div>
    </x-slot>

    @if($isEditing)
        <!-- Unit Form -->
        <div class="max-w-3xl mx-auto bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 mb-10">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center space-x-3">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <span>{{ $unitId ? 'Edit Detail Unit' : 'Daftar Unit Baru' }}</span>
            </h3>

            <form wire:submit.prevent="save" class="space-y-8">
                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Nama Unit</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="e.g. SD Al-Hikmah">
                    @error('name') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Alamat</label>
                    <textarea wire:model="address" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900 h-24" placeholder="Detail alamat lengkap..."></textarea>
                    @error('address') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Google Maps URL</label>
                    <input type="url" wire:model="google_maps_url" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="https://maps.google.com/...">
                    @error('google_maps_url') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" wire:click="$set('isEditing', false)" class="px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-12 py-4 bg-gray-900 text-white rounded-2xl text-sm font-black shadow-xl shadow-gray-200 hover:bg-blue-600 transition-all duration-300">
                        Simpan Unit
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($units as $unit)
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col h-full relative group">
                <!-- Top Section: Logo & Name -->
                <div class="flex items-center space-x-5 mb-8">
                    <div class="shrink-0 p-4 bg-gray-50 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm shadow-blue-50">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-black text-gray-900 tracking-tight leading-tight">{{ $unit->name }}</h4>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mt-1">Institusi Aktif</span>
                    </div>
                </div>

                <!-- Middle Section: Address -->
                <div class="flex-1 space-y-4 mb-8">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 text-gray-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-xs font-bold text-gray-500 leading-relaxed">{{ $unit->address ?? 'Alamat belum diisi' }}</p>
                    </div>
                </div>

                <!-- Bottom Section: Actions & Info -->
                <div class="pt-6 border-t border-gray-50 flex items-center justify-between mt-auto">
                    <!-- Action Buttons: Bottom Left -->
                    <div class="flex space-x-2">
                        <button wire:click="edit({{ $unit->id }})" class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-300 group/btn shadow-sm shadow-blue-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button onclick="confirm('Yakin ingin menghapus unit ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $unit->id }})" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm shadow-red-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex -space-x-2">
                            @foreach($unit->users->take(3) as $user)
                                <div class="h-8 w-8 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[10px] font-black text-blue-600 shadow-sm" title="{{ $user->name }}">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        @if($unit->google_maps_url)
                            <a href="{{ $unit->google_maps_url }}" target="_blank" class="p-2 bg-gray-50 text-gray-400 rounded-xl hover:text-blue-600 transition-colors duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10">
        {{ $units->links() }}
    </div>
</div>
