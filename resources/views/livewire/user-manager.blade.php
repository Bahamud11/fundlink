<div>
    <!-- Header -->
    <div class="w-full flex items-center mb-5 lg:mb-8 shrink-0">
        <div class="flex flex-col justify-center">
            <h2 class="font-['Inter'] font-bold text-2xl sm:text-3xl lg:text-[40px] text-black leading-none tracking-[-0.03em]">Daftar Pengguna</h2>
            <p class="font-['Inter'] font-light text-base lg:text-[24px] text-[#545454] mt-1 leading-none tracking-[-0.03em]">Daftar pengguna yang menggunakan aplikasi.</p>
        </div>
    </div>

    <!-- Search Bar + Tombol Tambah (sejajar) -->
    <div class="w-full flex items-center gap-3 mb-5 lg:mb-8 shrink-0">
        <div class="relative flex-1 bg-white border border-gray-200 rounded-2xl px-5 py-3.5 shadow-sm">
            <input type="text" wire:model.live="search"
                class="w-full bg-transparent border-none p-0 pr-8 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] placeholder-gray-300 focus:ring-0"
                placeholder="Cari pengguna...">
            <div class="absolute right-5 top-1/2 -translate-y-1/2">
                <img src="{{ asset('images/search.svg') }}" class="h-6 w-6 lg:h-8 lg:w-8 object-contain opacity-30" alt="Cari">
            </div>
        </div>
        <button wire:click="create"
            class="flex items-center gap-2 px-4 lg:px-5 h-12 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all duration-200 shrink-0">
            <img src="{{ asset('images/input.svg') }}" class="h-6 w-6 lg:h-7 lg:w-7 object-contain" alt="Input">
            <span class="font-['Inter'] font-medium text-sm lg:text-[20px] leading-none tracking-[-0.03em]">Input Pengguna</span>
        </button>
    </div>

    <!-- ─── Modal: Tambah / Edit Pengguna ──────────────────────────────────── -->
    @if($isEditing)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="mb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-blue-50 shadow-inner shrink-0">
                        <img src="{{ asset('images/profile.svg') }}" class="h-6 w-6 object-contain" alt="Pengguna">
                    </div>
                    <div>
                        <h2 class="font-['Inter'] font-bold text-[28px] text-gray-900 leading-none tracking-[-0.03em]">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                        <p class="font-['Inter'] font-light text-[16px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Lengkapi detail pengguna di bawah ini.</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('isEditing', false)" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 hover:text-gray-600 hover:scale-110 active:scale-95 transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save" class="space-y-5">
                <!-- Nama -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Nama Lengkap</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="text" wire:model="name" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Masukkan nama...">
                    </div>
                    @error('name') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <!-- Email -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Email</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="email" wire:model="email" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="email@domain.com">
                    </div>
                    @error('email') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <!-- Password -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">
                        Password{{ $userId ? ' (Kosongkan jika tidak ingin mengubah)' : '' }}
                    </label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="password" wire:model="password" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="••••••••">
                    </div>
                    @error('password') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <!-- Role -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Role</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model.live="role" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="user">User / Operator</option>
                            <option value="admin">Administrator</option>
                        </select>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                            <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 opacity-60" alt="">
                        </div>
                    </div>
                </div>
                @if($role === 'user')
                <!-- Unit / Cabang -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Unit / Cabang</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model="unit_id" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="">Pilih unit tugas</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                            <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 opacity-60" alt="">
                        </div>
                    </div>
                    @error('unit_id') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                @endif
                <!-- Footer -->
                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="$set('isEditing', false)"
                        class="flex-1 py-3 border border-gray-200 rounded-xl font-['Inter'] font-medium text-[14px] text-blue-600 hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-['Inter'] font-medium text-[14px] shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ─── Modal: Detail Pengguna ─────────────────────────────────────────── -->
    @if($selectedUser)
    <div class="fixed inset-0 z-[60] overflow-hidden bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="absolute inset-0" wire:click="closeDetail"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                <div class="p-8">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-2xl overflow-hidden shrink-0">
                                @if($selectedUser->profile_photo_path)
                                    <img src="{{ Storage::url($selectedUser->profile_photo_path) }}" alt="{{ $selectedUser->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-blue-600 text-white flex items-center justify-center font-['Inter'] font-bold text-[20px]">
                                        {{ substr($selectedUser->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h2 class="font-['Inter'] font-bold text-[20px] text-gray-900 leading-none tracking-[-0.03em]">{{ $selectedUser->name }}</h2>
                                <p class="font-['Inter'] font-light text-[12px] text-[#929292] mt-1 uppercase tracking-widest">
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
                    <!-- Status Online -->
                    <div class="mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                        <div class="flex items-center justify-center gap-2">
                            <div class="h-2.5 w-2.5 rounded-full {{ $selectedUser->is_online ? 'bg-blue-600 animate-pulse' : 'bg-gray-300' }}"></div>
                            <span class="font-['Inter'] font-medium text-[16px] {{ $selectedUser->is_online ? 'text-blue-600' : 'text-[#929292]' }} leading-none tracking-[-0.03em]">
                                {{ $selectedUser->is_online ? 'Sedang Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>
                    <!-- Detail -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center justify-between px-1">
                            <span class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Email</span>
                            <span class="font-['Inter'] font-bold text-[14px] text-gray-900 truncate ml-4">{{ $selectedUser->email }}</span>
                        </div>
                        <div class="border-t border-gray-50"></div>
                        <div class="flex items-center justify-between px-1">
                            <span class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Role</span>
                            <span class="px-3 py-1 rounded-lg font-['Inter'] font-bold text-[12px] {{ $selectedUser->role === 'admin' ? 'bg-blue-800 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $selectedUser->role === 'admin' ? 'Administrator' : 'User / Operator' }}
                            </span>
                        </div>
                        <div class="border-t border-gray-50"></div>
                        <div class="flex items-center justify-between px-1">
                            <span class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Unit / Cabang</span>
                            <span class="font-['Inter'] font-bold text-[14px] text-gray-900">
                                {{ $selectedUser->unit->name ?? ($selectedUser->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                            </span>
                        </div>
                        <div class="border-t border-gray-50"></div>
                        <div class="flex items-center justify-between px-1">
                            <span class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Bergabung</span>
                            <span class="font-['Inter'] font-bold text-[14px] text-gray-900">{{ $selectedUser->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-50 flex gap-3">
                        <button wire:click="edit({{ $selectedUser->id }})"
                            class="flex-1 flex items-center justify-center gap-2 py-4 bg-blue-600 text-white rounded-2xl font-['Inter'] font-medium text-[12px] shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        @if($selectedUser->id !== auth()->id())
                        <button
                            @click="Swal.fire({ title: 'Hapus Pengguna?', text: 'Akun pengguna akan dihapus permanen dari sistem.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#2563eb', cancelButtonColor: '#e5e7eb', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', customClass: { cancelButton: '!text-gray-700', popup: '!rounded-3xl !shadow-2xl', title: '!font-black !text-gray-900 !text-xl', htmlContainer: '!text-gray-400 !text-sm', confirmButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3', cancelButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3' }}).then(r => r.isConfirmed && $wire.delete({{ $selectedUser->id }}))"
                            class="flex-1 flex items-center justify-center gap-2 py-4 bg-rose-50 text-rose-600 rounded-2xl font-['Inter'] font-medium text-[12px] hover:bg-rose-100 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ─── Daftar Pengguna ─────────────────────────────────────────────────── -->
    <div class="flex flex-col gap-3 lg:gap-4">
        @forelse($users as $user)
        <div wire:click="viewDetail({{ $user->id }})"
            class="group cursor-pointer bg-white w-full py-4 lg:py-0 lg:h-[96px] px-4 lg:px-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3 lg:gap-6 min-w-0">
                <!-- Avatar -->
                <div class="h-11 w-11 lg:h-12 lg:w-12 rounded-2xl overflow-hidden shrink-0 group-hover:scale-110 transition-transform duration-300">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-blue-50 text-blue-600 flex items-center justify-center font-['Inter'] font-bold text-[18px] group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <!-- Info -->
                <div class="min-w-0">
                    <p class="font-['Inter'] font-normal text-base lg:text-[24px] text-[#7D7D7D] leading-none tracking-[-0.03em] truncate">{{ $user->name }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="font-['Inter'] font-light text-[12px] text-[#929292] leading-none tracking-[-0.03em]">
                            {{ $user->unit->name ?? ($user->role === 'admin' ? 'Administrator' : 'Tidak ada cabang') }}
                        </span>
                        <span class="text-gray-300 hidden sm:inline">•</span>
                        <span class="font-['Inter'] font-light text-[12px] text-[#929292] leading-none tracking-[-0.03em] hidden sm:inline truncate">{{ $user->email }}</span>
                    </div>
                </div>
            </div>
            <!-- Status Online -->
            <div class="flex items-center gap-2 shrink-0 ml-3">
                <div class="h-2 w-2 rounded-full shrink-0 {{ $user->is_online ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                <span class="font-['Inter'] font-normal text-sm sm:text-xl lg:text-[32px] leading-none tracking-[-0.03em] {{ $user->is_online ? 'text-blue-600' : 'text-[#929292]' }}">
                    {{ $user->is_online ? 'Online' : 'Offline' }}
                </span>
            </div>
        </div>
        @empty
        <div class="w-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100 shrink-0">
            <div class="flex flex-col items-center gap-3 lg:gap-4">
                <div class="p-6 bg-gray-50 rounded-full shadow-sm">
                    <img src="{{ asset('images/profil.svg') }}" class="h-12 w-12 object-contain opacity-20" alt="Pengguna">
                </div>
                <p class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#929292] leading-none tracking-[-0.03em]">Tidak ada pengguna ditemukan</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-5 lg:mt-8 w-full">
        {{ $users->links('livewire.pagination') }}
    </div>
</div>
