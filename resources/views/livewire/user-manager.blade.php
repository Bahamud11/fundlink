<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Personel Sistem</h2>
                <p class="text-gray-400 font-medium mt-1">Kelola administrator dan penanggung jawab unit.</p>
            </div>
            <button wire:click="create" class="px-8 py-3 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-200 hover:scale-105 transition-all duration-300">
                Tambah Pengguna Baru
            </button>
        </div>
    </x-slot>

    @if($isEditing)
        <!-- User Form -->
        <div class="max-w-3xl mx-auto bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 mb-10">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center space-x-3">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <span>{{ $userId ? 'Perbarui Profil Personel' : 'Daftar Personel Baru' }}</span>
            </h3>

            <form wire:submit.prevent="save" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="e.g. Ahmad Sujadi">
                        @error('name') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Email Address</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="ahmad@fundlink.com">
                        @error('email') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Password {{ $userId ? '(Opsional)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="********">
                        @error('password') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Peran Sistem</label>
                        <select wire:model.live="role" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900">
                            <option value="user">Pengelola Unit (User)</option>
                            <option value="admin">Admin Yayasan (Pusat)</option>
                        </select>
                        @error('role') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    @if($role === 'user')
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Unit Penempatan</label>
                            <select wire:model="unit_id" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900">
                                <option value="">Pilih Unit...</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" wire:click="$set('isEditing', false)" class="px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-12 py-4 bg-gray-900 text-white rounded-2xl text-sm font-black shadow-xl shadow-gray-200 hover:bg-blue-600 transition-all duration-300">
                        Simpan Personel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- User Table -->
    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Personel</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Peran</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Penempatan Unit</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Status</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                        <td class="py-6">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-gray-900">{{ $user->name }}</span>
                                    <span class="text-[10px] font-bold text-gray-400">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $user->isAdmin() ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-6 text-xs font-bold text-gray-500">
                            {{ $user->unit->name ?? 'Global (Pusat)' }}
                        </td>
                        <td class="py-6">
                            <div class="flex items-center space-x-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase">Online</span>
                            </div>
                        </td>
                        <td class="py-6 text-right">
                            <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button wire:click="edit({{ $user->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-10">
            {{ $users->links() }}
        </div>
    </div>
</div>
