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
        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Riwayat Transaksi</h2>
        <p class="text-gray-400 font-medium mt-1 text-sm">Catatan pemasukan/pengeluaran.</p>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row items-end md:items-center justify-between mb-12 gap-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 w-full md:w-auto flex-1">
            <div class="flex flex-col space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rentang</label>
                <div class="relative">
                    <select wire:model.live="filterRange" class="w-full !appearance-none !bg-none bg-transparent border-none p-0 pr-8 text-sm font-bold text-gray-900 focus:ring-0 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                        <option value="Mingguan">Mingguan</option>
                        <option value="Bulanan">Bulanan</option>
                        <option value="Harian">Harian</option>
                    </select>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Frekuensi</label>
                <div class="relative">
                    <select wire:model.live="filterFrequency" class="w-full !appearance-none !bg-none bg-transparent border-none p-0 pr-8 text-sm font-bold text-gray-900 focus:ring-0 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                        <option value="Minggu ke-1">Minggu ke-1</option>
                        <option value="Minggu ke-2">Minggu ke-2</option>
                        <option value="Minggu ke-3">Minggu ke-3</option>
                        <option value="Minggu ke-4">Minggu ke-4</option>
                    </select>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="flex flex-col space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cabang</label>
                <div class="relative">
                    <select wire:model.live="filterUnit" class="w-full !appearance-none !bg-none bg-transparent border-none p-0 pr-8 text-sm font-bold text-gray-900 focus:ring-0 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                        <option value="">Pilih Cabang</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
            <button wire:click="exportPdf" wire:loading.attr="disabled" class="flex items-center space-x-2 px-4 py-2 text-gray-400 hover:text-gray-600 transition-colors disabled:opacity-50">
                <svg wire:loading.remove wire:target="exportPdf" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportPdf" class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-bold">{{ __('Ekspor') }}</span>
            </button>
            <button wire:click="$toggle('isCreating')" class="flex items-center space-x-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all duration-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs font-black">{{ $isCreating ? 'Batal' : 'Input' }}</span>
            </button>
        </div>
    </div>

    @if($isCreating)
        <!-- Centered Modal for Transaction Input -->
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar relative">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Input Transaksi</h2>
                        <p class="text-gray-400 font-medium mt-0.5 text-xs">Catat pemasukan/pengeluaran.</p>
                    </div>
                    <button type="button" wire:click="$set('isCreating', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if(!auth()->user()->isAdmin() && !auth()->user()->unit_id)
                <div class="p-6 bg-rose-50 border border-rose-100 rounded-3xl text-center space-y-4">
                    <div class="mx-auto w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm text-rose-500 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900">Akses Dibatasi</h3>
                    <p class="text-gray-500 font-medium text-xs">Akun Anda belum ditautkan ke cabang mana pun. Silakan hubungi Admin untuk penempatan cabang sebelum dapat menginput transaksi.</p>
                    <div class="pt-4">
                        <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-black shadow-lg shadow-rose-200 hover:bg-rose-700 transition-all uppercase tracking-widest">
                            Tutup
                        </button>
                    </div>
                </div>
                @else
                <!-- Type Tabs -->
                <div class="flex mb-6 rounded-2xl bg-gray-50 p-1 h-12">
                    <button type="button" wire:click="$set('type', 'pemasukan')" 
                        class="flex-1 text-xs font-black transition-all duration-300 rounded-xl {{ $type === 'pemasukan' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-400 hover:text-gray-500' }}">
                        Pemasukan
                    </button>
                    <button type="button" wire:click="$set('type', 'pengeluaran')" 
                        class="flex-1 text-xs font-black transition-all duration-300 rounded-xl {{ $type === 'pengeluaran' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-400 hover:text-gray-500' }}">
                        Pengeluaran
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-5">
                    <!-- Nominal -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nominal</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="number" wire:model="amount" class="w-full text-xl font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Rp 000.000,00">
                        </div>
                        @error('amount') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model="category" class="w-full text-sm font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                                <option value="">Pilih kategori transaksi</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Dana BOS">Dana BOS</option>
                                <option value="Donasi">Donasi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('category') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    @if(auth()->user()->isAdmin())
                    <!-- Cabang -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cabang</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <select wire:model="unit_id" class="w-full text-sm font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
                                <option value="">Pilih Cabang</option>
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

                    <!-- Keterangan -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keterangan</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                            <input type="text" wire:model="description" class="w-full text-sm font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Tulis disini...">
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="flex flex-col items-center justify-center pt-4 space-y-2">
                        <label class="cursor-pointer group flex flex-col items-center space-y-2 relative">
                            <input type="file" wire:model="attachment" accept="image/*" class="hidden">
                            
                            <!-- Loading State -->
                            <div wire:loading wire:target="attachment" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-2xl z-10 backdrop-blur-sm">
                                <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>

                            @if($attachment)
                                <!-- Preview Image -->
                                <div class="relative w-24 h-24 rounded-2xl overflow-hidden shadow-lg border-2 border-emerald-100 group-hover:border-emerald-300 transition-all">
                                    <img src="{{ $attachment->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="text-white text-[9px] font-black uppercase tracking-widest">Ganti</span>
                                    </div>
                                </div>
                                <div class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[8px] font-black uppercase tracking-widest flex items-center space-x-1 mt-1">
                                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Gambar Siap</span>
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="p-3 rounded-full bg-gray-50 group-hover:bg-blue-50 transition-colors border-2 border-dashed border-gray-200 group-hover:border-blue-200">
                                    <svg class="h-6 w-6 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-[9px] font-black text-gray-400 group-hover:text-blue-500 uppercase tracking-widest transition-colors">Tambahkan Bukti (Opsional)</span>
                            @endif
                        </label>
                        @error('attachment') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-4 flex space-x-3">
                        <button type="button" wire:click="$set('isCreating', false)" class="flex-1 py-3 border border-gray-200 rounded-xl text-xs font-black text-blue-600 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-xs font-black shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">
                            Buat
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    @endif

    @if($selectedTransaction)
        <!-- Balanced Transaction Detail View Design -->
        <div class="fixed inset-0 z-[60] overflow-y-auto no-scrollbar bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="min-h-screen flex items-center justify-center p-4">
                <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="p-8">
                        <!-- Header: Icon & Category -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 rounded-2xl {{ $selectedTransaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} shadow-inner shrink-0">
                                    <img src="{{ asset($selectedTransaction->type === 'pemasukan' ? 'images/pemasukan.png' : 'images/Pengeluaran.png') }}" class="h-6 w-6 object-contain" alt="{{ $selectedTransaction->type }}">
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900 tracking-tight">{{ $selectedTransaction->category }}</h2>
                                    <p class="text-gray-400 font-bold mt-1 text-[10px] uppercase tracking-widest truncate max-w-[200px]">
                                        {{ $selectedTransaction->unit->name }} • {{ $selectedTransaction->transaction_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeDetail" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 transition-all hover:scale-110 active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Balanced Amount -->
                        <div class="mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nominal Transaksi</span>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight break-all">
                                Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }},00
                            </h1>
                        </div>

                        <!-- Details Stack -->
                        <div class="space-y-6 mb-8">
                            <!-- Proof Photo -->
                            <div>
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Foto bukti</h3>
                                <div class="aspect-video rounded-3xl bg-gray-50 border border-gray-100 overflow-hidden group relative shadow-inner">
                                    @if($selectedTransaction->attachment_path)
                                        <img src="{{ Storage::url($selectedTransaction->attachment_path) }}" alt="Bukti Transaksi" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out">
                                        <a href="{{ Storage::url($selectedTransaction->attachment_path) }}" target="_blank" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm duration-500">
                                            <div class="px-6 py-3 bg-white rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                                Lihat Gambar Penuh
                                            </div>
                                        </a>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-2">
                                            <div class="p-3 bg-white rounded-full shadow-sm">
                                                <svg class="h-6 w-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Tidak ada lampiran</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Keterangan</h3>
                                <div class="p-5 rounded-3xl bg-gray-50/50 border border-gray-100 italic text-gray-600 text-sm leading-relaxed">
                                    "{{ $selectedTransaction->description ?: 'Tidak ada keterangan tambahan untuk transaksi ini.' }}"
                                </div>
                            </div>

                            <!-- Recorded By -->
                            <div class="flex items-center space-x-4 px-2">
                                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-base shadow-lg shadow-blue-100 shrink-0">
                                    {{ substr($selectedTransaction->user->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dicatat Oleh</span>
                                    <span class="text-sm font-black text-gray-900 tracking-tight">{{ $selectedTransaction->user->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Action -->
                        <div class="pt-6 border-t border-gray-50">
                            <button wire:click="closeDetail" class="w-full py-4 bg-blue-600 text-white rounded-2xl text-xs font-black shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.2em]">
                                Kembali ke Daftar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Transaction List -->
    <div class="space-y-4 sm:space-y-6">
        @forelse($transactions as $transaction)
            <div wire:click="viewDetail({{ $transaction->id }})" class="group cursor-pointer bg-white p-5 sm:p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Icon Indicator -->
                    <div class="p-3 sm:p-4 rounded-2xl {{ $transaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }}">
                        <img src="{{ asset($transaction->type === 'pemasukan' ? 'images/pemasukan.png' : 'images/Pengeluaran.png') }}" class="h-6 w-6 sm:h-8 sm:w-8 object-contain" alt="{{ $transaction->type }}">
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col">
                        <h4 class="text-base sm:text-lg font-black text-gray-900">{{ $transaction->category }}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            @if(auth()->user()->isAdmin())
                            <span class="text-xs font-bold text-gray-400">{{ $transaction->unit->name }}</span>
                            <span class="text-gray-300">•</span>
                            @endif
                            <span class="text-xs font-bold text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Amount -->
                <div class="text-left sm:text-right pl-14 sm:pl-0">
                    <p class="text-xl sm:text-2xl font-black text-gray-700 tracking-tight">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }},00
                    </p>
                    @if($transaction->attachment_path)
                        <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" class="inline-flex items-center mt-2 text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 transition-colors">
                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Lihat Bukti
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                <div class="flex flex-col items-center">
                    <div class="p-6 bg-white rounded-full shadow-sm mb-4">
                        <svg class="h-12 w-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-400 font-black text-sm uppercase tracking-widest">Belum ada transaksi</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $transactions->links() }}
    </div>
</div>
