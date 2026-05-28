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
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Daftar Pengguna</h2>
        <p class="text-gray-400 font-medium mt-1 text-lg">Daftar pengguna yang memiliki akses ke dalam sistem.</p>
    </div>

    @if($isEditing)
        <!-- Centered Modal for User Input -->
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar relative">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                        <p class="text-gray-400 font-medium mt-0.5 text-xs">Silakan lengkapi detail pengguna di bawah ini.</p>
                    </div>
                    <button type="button" wire:click="$set('isEditing', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Nama -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="text" wire:model="name" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan nama...">
                        </div>
                        @error('name') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="email" wire:model="email" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan email...">
                        </div>
                        @error('email') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Password {{ $userId ? '(Kosongkan jika tidak ingin ganti)' : '' }}</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="password" wire:model="password" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan password...">
                        </div>
                        @error('password') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model.live="role" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
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
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Unit / Cabang</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model="unit_id" class="w-full text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
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
                    <div class="pt-6 flex space-x-3">
                        <button type="button" wire:click="$set('isEditing', false)" class="flex-1 py-3 border border-blue-600 rounded-xl text-xs font-bold text-blue-600 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all">
                            Simpan
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
                <input type="text" wire:model.live="search" class="w-full bg-transparent border-none p-0 pr-8 text-sm font-medium text-gray-800 placeholder-gray-300 focus:ring-0" placeholder="Cari pengguna...">
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
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- User List (Card-based) -->
    <div class="space-y-4 sm:space-y-6">
        @forelse($users as $user)
            <div wire:click="viewDetail({{ $user->id }})" class="group cursor-pointer bg-white p-5 sm:p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Profile Image / Avatar -->
                    <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-2xl overflow-hidden shrink-0">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-base sm:text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col">
                        <h4 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">{{ $user->name }}</h4>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">
                            {{ $user->unit->name ?? ($user->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pl-16 sm:pl-0">
                    <div class="h-1.5 w-1.5 rounded-full {{ $user->is_online ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    <span class="text-sm font-bold {{ $user->is_online ? 'text-blue-600' : 'text-gray-400' }} tracking-tight">
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

    @if($selectedUser)
        <div class="fixed inset-0 z-[60] overflow-hidden pointer-events-none bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="min-h-screen flex items-center justify-center p-4 pointer-events-none">
                <div class="pointer-events-auto bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="p-8 text-left">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 rounded-2xl overflow-hidden shrink-0">
                                    @if($selectedUser->profile_photo_path)
                                        <img src="{{ Storage::url($selectedUser->profile_photo_path) }}" alt="{{ $selectedUser->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-blue-600 text-white flex items-center justify-center font-black text-xl">
                                            {{ substr($selectedUser->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900 tracking-tight">{{ $selectedUser->name }}</h2>
                                    <p class="text-gray-400 font-bold mt-1 text-[10px] uppercase tracking-widest">
                                        {{ $selectedUser->unit->name ?? ($selectedUser->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 transition-all hover:scale-110 active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Status Badge -->
                        <div class="mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="h-2 w-2 rounded-full {{ $selectedUser->is_online ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                                <span class="text-sm font-black {{ $selectedUser->is_online ? 'text-blue-600' : 'text-gray-400' }} uppercase tracking-widest">
                                    {{ $selectedUser->is_online ? 'Sedang Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</span>
                                <span class="text-sm font-black text-gray-900">{{ $selectedUser->email }}</span>
                            </div>
                            <div class="border-t border-gray-50"></div>
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</span>
                                <span class="text-sm font-black text-gray-900 capitalize">{{ $selectedUser->role === 'admin' ? 'Administrator' : 'User / Operator' }}</span>
                            </div>
                            <div class="border-t border-gray-50"></div>
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit / Cabang</span>
                                <span class="text-sm font-black text-gray-900">
                                    {{ $selectedUser->unit->name ?? ($selectedUser->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                                </span>
                            </div>
                            <div class="border-t border-gray-50"></div>
                            <div class="flex items-center justify-between px-1">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bergabung</span>
                                <span class="text-sm font-black text-gray-900">{{ $selectedUser->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-6 border-t border-gray-50 flex space-x-3">
                            <button wire:click="edit({{ $selectedUser->id }})" class="flex-1 flex items-center justify-center space-x-2 py-4 bg-blue-600 text-white rounded-2xl text-xs font-black shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.2em]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            @if($selectedUser->id !== auth()->id())
                            <button onclick="confirm('Hapus pengguna ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $selectedUser->id }})" class="flex-1 flex items-center justify-center space-x-2 py-4 bg-rose-50 text-rose-600 rounded-2xl text-xs font-black hover:bg-rose-100 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.2em]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
