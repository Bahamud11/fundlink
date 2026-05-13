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
        <!-- Compact Transaction Input Design -->
        <div class="fixed inset-0 bg-white z-50 overflow-y-auto animate-in fade-in duration-300">
            <div class="max-w-3xl mx-auto px-6 py-12">
                <!-- Header -->
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Input Transaksi</h2>
                    <p class="text-gray-400 font-medium mt-1 text-sm">Catat pemasukan/pengeluaran.</p>
                </div>

                <!-- Type Tabs -->
                <div class="flex mb-10 rounded-2xl bg-gray-50 p-1 h-14">
                    <button type="button" wire:click="$set('type', 'pemasukan')" 
                        class="flex-1 text-sm font-black transition-all duration-300 rounded-xl {{ $type === 'pemasukan' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-400 hover:text-gray-500' }}">
                        Pemasukan
                    </button>
                    <button type="button" wire:click="$set('type', 'pengeluaran')" 
                        class="flex-1 text-sm font-black transition-all duration-300 rounded-xl {{ $type === 'pengeluaran' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-400 hover:text-gray-500' }}">
                        Pengeluaran
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-10">
                    <!-- Nominal -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nominal</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-4">
                            <input type="number" wire:model="amount" class="w-full text-2xl font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Rp 000.000,00">
                        </div>
                        @error('amount') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-4">
                            <select wire:model="category" class="w-full text-base font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
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

                    <!-- Cabang -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Cabang</label>
                        <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-4">
                            <select wire:model="unit_id" class="w-full text-base font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 !appearance-none !bg-none cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; background-image: none !important;">
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

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Keterangan</label>
                        <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-4">
                            <input type="text" wire:model="description" class="w-full text-base font-bold text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Tulis disini...">
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="flex justify-center pt-6">
                        <label class="cursor-pointer group flex flex-col items-center space-y-3">
                            <input type="file" wire:model="attachment" class="hidden">
                            <div class="p-3 rounded-full bg-gray-50 group-hover:bg-blue-50 transition-colors">
                                <svg class="h-6 w-6 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-gray-400 group-hover:text-blue-500 uppercase tracking-widest">Tambahkan Gambar</span>
                            @if($attachment)
                                <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[8px] font-black uppercase tracking-widest">
                                    File Terpilih
                                </div>
                            @endif
                        </label>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-12 flex space-x-3">
                        <button type="button" wire:click="$set('isCreating', false)" class="flex-1 py-4 border border-gray-200 rounded-xl text-sm font-black text-blue-600 hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-xl text-sm font-black shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">
                            Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($selectedTransaction)
        <!-- Balanced Transaction Detail View Design -->
        <div class="fixed inset-0 z-[60] overflow-y-auto bg-black/20 backdrop-blur-sm animate-in fade-in duration-500">
            <div class="min-h-screen flex items-center justify-center p-4 md:p-12">
                <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl relative border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-16">
                        <!-- Header: Icon & Category -->
                        <div class="flex items-center justify-between mb-12">
                            <div class="flex items-center space-x-6">
                                <div class="p-5 rounded-[1.5rem] {{ $selectedTransaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} shadow-inner">
                                    @if($selectedTransaction->type === 'pemasukan')
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    @else
                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $selectedTransaction->category }}</h2>
                                    <p class="text-gray-400 font-bold mt-1 text-xs md:text-sm uppercase tracking-[0.2em]">
                                        {{ $selectedTransaction->unit->name }} • {{ $selectedTransaction->transaction_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeDetail" class="p-4 rounded-2xl hover:bg-gray-50 text-gray-400 transition-all hover:scale-110 active:scale-95">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Balanced Amount -->
                        <div class="mb-16">
                            <h1 class="text-6xl md:text-7xl font-black text-gray-900 tracking-tighter leading-none">
                                Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }},00
                            </h1>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                            <!-- Proof Photo -->
                            <div>
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Foto bukti</h3>
                                <div class="aspect-video rounded-[2.5rem] bg-gray-50 border border-gray-100 overflow-hidden group relative shadow-inner">
                                    @if($selectedTransaction->attachment_path)
                                        <img src="{{ Storage::url($selectedTransaction->attachment_path) }}" alt="Bukti Transaksi" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out">
                                        <a href="{{ Storage::url($selectedTransaction->attachment_path) }}" target="_blank" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm duration-500">
                                            <div class="px-8 py-4 bg-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                                Lihat Gambar Penuh
                                            </div>
                                        </a>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-4">
                                            <div class="p-5 bg-white rounded-full shadow-sm">
                                                <svg class="h-12 w-12 text-gray-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">Tidak ada lampiran</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="flex flex-col">
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Keterangan</h3>
                                <div class="flex-1 p-10 rounded-[2.5rem] bg-gray-50/50 border border-gray-100 italic font-medium text-gray-600 text-lg md:text-xl leading-relaxed">
                                    "{{ $selectedTransaction->description ?: 'Tidak ada keterangan tambahan untuk transaksi ini.' }}"
                                </div>
                                <div class="mt-8 flex items-center space-x-5 px-2">
                                    <div class="h-12 w-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-lg shadow-xl shadow-blue-100">
                                        {{ substr($selectedTransaction->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dicatat Oleh</span>
                                        <span class="text-base font-black text-gray-900 tracking-tight">{{ $selectedTransaction->user->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Action -->
                        <div class="pt-10 border-t border-gray-50">
                            <button wire:click="closeDetail" class="w-full py-6 bg-blue-600 text-white rounded-[1.5rem] text-xs font-black shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all duration-300 uppercase tracking-[0.3em]">
                                Kembali ke Daftar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Transaction List -->
    <div class="space-y-6">
        @forelse($transactions as $transaction)
            <div wire:click="viewDetail({{ $transaction->id }})" class="group cursor-pointer bg-white p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <!-- Icon Indicator -->
                    <div class="p-4 rounded-2xl {{ $transaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }}">
                        @if($transaction->type === 'pemasukan')
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        @else
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                            </svg>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col">
                        <h4 class="text-lg font-black text-gray-900">{{ $transaction->category }}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-xs font-bold text-gray-400">{{ $transaction->unit->name }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs font-bold text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Amount -->
                <div class="text-right">
                    <p class="text-2xl font-black text-gray-700 tracking-tight">
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
