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
    <div class="mb-10">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Pengguna</h2>
        <p class="text-gray-400 font-medium mt-1 text-sm">Daftar pengguna yang memiliki akses ke dalam sistem.</p>
    </div>

    @if($isEditing)
        <!-- User Input Design - Compact Version -->
        <div class="fixed inset-0 bg-white z-50 overflow-y-auto animate-in fade-in duration-300">
            <div class="max-w-2xl mx-auto px-6 py-12">
                <!-- Header -->
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                    <p class="text-gray-400 font-medium mt-1 text-sm">Silakan lengkapi detail pengguna di bawah ini.</p>
                </div>

                <form wire:submit.prevent="save" class="space-y-10">
                    <!-- Nama -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="text" wire:model="name" class="w-full text-lg font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan nama...">
                        </div>
                        @error('name') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="email" wire:model="email" class="w-full text-lg font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan email...">
                        </div>
                        @error('email') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Password {{ $userId ? '(Kosongkan jika tidak ingin ganti)' : '' }}</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="password" wire:model="password" class="w-full text-lg font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan password...">
                        </div>
                        @error('password') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Role</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model.live="role" class="w-full text-lg font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                                <option value="user">User / Operator</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($role === 'user')
                    <!-- Cabang -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Unit / Cabang</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model="unit_id" class="w-full text-lg font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                                <option value="">Pilih unit tugas</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('unit_id') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <!-- Footer Buttons -->
                    <div class="pt-10 flex space-x-4">
                        <button type="button" wire:click="$set('isEditing', false)" class="flex-1 py-4 border border-blue-600 rounded-xl text-sm font-bold text-blue-600 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Search and Actions Bar -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-8">
        <div class="relative w-full md:w-96 group">
            <input type="text" wire:model.live="search" class="w-full bg-transparent border-b border-gray-100 focus:border-blue-600 transition-colors py-2 pl-0 pr-10 text-lg font-medium text-gray-800 placeholder-gray-300 focus:ring-0" placeholder="Cari pengguna...">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-blue-600 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <button wire:click="create" class="flex items-center space-x-2 px-8 py-4 bg-blue-600 text-white rounded-2xl text-sm font-bold shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- User List (Card-based) -->
    <div class="space-y-6">
        @forelse($users as $user)
            <div class="group bg-white p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <!-- Icon Indicator -->
                    <div class="h-16 w-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        {{ substr($user->name, 0, 1) }}
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col">
                        <div class="flex items-center space-x-3">
                            <h4 class="text-lg font-black text-gray-900 tracking-tight">{{ $user->name }}</h4>
                            <!-- Subtle Actions on Hover -->
                            <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $user->id }})" class="text-blue-500 hover:text-blue-700 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                <button onclick="confirm('Hapus pengguna ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">
                            {{ $user->unit->name ?? ($user->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div class="h-2 w-2 rounded-full {{ $user->is_online ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    <span class="text-lg font-bold {{ $user->is_online ? 'text-blue-600' : 'text-gray-400' }} tracking-tight">
                        {{ $user->is_online ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                <p class="text-gray-400 font-bold">Tidak ada pengguna ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $users->links() }}
    </div>
</div>
