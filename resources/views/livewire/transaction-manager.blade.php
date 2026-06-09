<div>
    <!-- Header -->
    <div class="w-full flex items-center justify-between mb-5 lg:mb-8 shrink-0">
        <div class="flex flex-col justify-center">
            <h2 class="font-['Inter'] font-bold text-2xl sm:text-3xl lg:text-[40px] text-black leading-none tracking-[-0.03em]">Riwayat Transaksi</h2>
            <p class="font-['Inter'] font-light text-base lg:text-[24px] text-[#545454] mt-1 leading-none tracking-[-0.03em]">Catatan pemasukan &amp; pengeluaran yayasan.</p>
        </div>
    </div>

    <!-- Filter Bar + Actions -->
    <div class="w-full flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-5 lg:mb-8 shrink-0">
        <!-- Kiri: Dropdown Filters -->
        <div class="flex flex-wrap items-end gap-3 flex-1 w-full sm:w-auto">
            <!-- Rentang -->
            <div class="flex flex-col gap-2 flex-1 min-w-[100px]">
                <label class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#000000] leading-none tracking-[-0.03em]">Rentang</label>
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                        <span>{{ $filterRange }}</span>
                        <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                    </button>
                    <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-40 overflow-y-auto" style="display: none;">
                        @foreach(['Mingguan','Bulanan','Tahunan'] as $opt)
                            <button @click="$wire.set('filterRange', '{{ $opt }}'); open = false;" type="button"
                                class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                {{ $opt }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Frekuensi -->
            <div class="flex flex-col gap-2 flex-1 min-w-[100px]">
                <label class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#000000] leading-none tracking-[-0.03em]">Frekuensi</label>
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                        <span class="truncate">{{ $filterFrequency }}</span>
                        <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                    </button>
                    <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-48 overflow-y-auto" style="display: none;">
                        @if($filterRange === 'Mingguan')
                            @foreach(['Minggu ke-1','Minggu ke-2','Minggu ke-3','Minggu ke-4'] as $opt)
                                <button @click="$wire.set('filterFrequency', '{{ $opt }}'); open = false;" type="button"
                                    class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">{{ $opt }}</button>
                            @endforeach
                        @elseif($filterRange === 'Bulanan')
                            @php $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                            @foreach($namaBulan as $bulan)
                                <button @click="$wire.set('filterFrequency', '{{ $bulan }}'); open = false;" type="button"
                                    class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">{{ $bulan }}</button>
                            @endforeach
                        @elseif($filterRange === 'Tahunan')
                            @foreach(range(now()->year, now()->year - 4) as $opt)
                                <button @click="$wire.set('filterFrequency', '{{ $opt }}'); open = false;" type="button"
                                    class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">{{ $opt }}</button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cabang (admin only) -->
            @if(auth()->user()->isAdmin())
            <div class="flex flex-col gap-2 flex-1 min-w-[100px]">
                <label class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#000000] leading-none tracking-[-0.03em]">Cabang</label>
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                        <span class="truncate">{{ $units->firstWhere('id', $filterUnit)->name ?? 'Semua Cabang' }}</span>
                        <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                    </button>
                    <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-48 overflow-y-auto" style="display: none;">
                        <button @click="$wire.set('filterUnit', ''); open = false;" type="button"
                            class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">Semua Cabang</button>
                        @foreach($units as $unit)
                            <button @click="$wire.set('filterUnit', '{{ $unit->id }}'); open = false;" type="button"
                                class="w-full text-left px-4 py-2 font-['Inter'] font-light text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">{{ $unit->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Kanan: Tombol Aksi -->
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <!-- Export PDF -->
            <button wire:click="exportPdf" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 h-11 rounded-2xl border border-gray-200 bg-white shadow-sm font-['Inter'] font-medium text-sm lg:text-[20px] text-[#545454] leading-none tracking-[-0.03em] hover:border-blue-200 hover:text-blue-600 transition-all duration-200 disabled:opacity-50 shrink-0">
                <svg wire:loading.remove wire:target="exportPdf" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportPdf" class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Ekspor PDF</span>
            </button>
            <!-- Input Transaksi -->
            <button wire:click="$toggle('isCreating')"
                class="flex items-center gap-2 px-4 h-11 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-200 font-['Inter'] font-medium text-sm lg:text-[20px] leading-none tracking-[-0.03em] hover:bg-blue-700 transition-all duration-200 shrink-0">
                <img src="{{ asset('images/input.svg') }}" class="h-6 w-6 object-contain" alt="Input">
                <span>{{ $isCreating ? 'Batal' : 'Input Transaksi' }}</span>
            </button>
        </div>
    </div>

    <!-- ─── Modal: Input Transaksi ─────────────────────────────────────────── -->
    @if($isCreating)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="mb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-blue-50 shadow-inner shrink-0">
                        <img src="{{ asset('images/transaksi.svg') }}" class="h-6 w-6 object-contain" alt="Transaksi">
                    </div>
                    <div>
                        <h2 class="font-['Inter'] font-bold text-[28px] text-gray-900 leading-none tracking-[-0.03em]">Input Transaksi</h2>
                        <p class="font-['Inter'] font-light text-[16px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Catat pemasukan atau pengeluaran.</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('isCreating', false)" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 hover:text-gray-600 hover:scale-110 active:scale-95 transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if(!auth()->user()->isAdmin() && !auth()->user()->unit_id)
            <div class="p-6 bg-rose-50 border border-rose-100 rounded-3xl text-center space-y-4">
                <div class="mx-auto w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm text-rose-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-['Inter'] font-bold text-[20px] text-gray-900 leading-none">Akses Dibatasi</h3>
                <p class="font-['Inter'] font-light text-[14px] text-[#929292] leading-relaxed">Akun Anda belum ditautkan ke cabang. Hubungi Admin untuk penempatan cabang.</p>
                <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-2.5 bg-rose-600 text-white rounded-xl font-['Inter'] font-medium text-[14px] tracking-widest hover:bg-rose-700 transition-all uppercase">Tutup</button>
            </div>
            @else
            <!-- Type Tabs -->
            <div class="flex mb-6 rounded-2xl bg-gray-50 p-1 h-12">
                <button type="button" wire:click="$set('type', 'pemasukan')"
                    class="flex-1 font-['Inter'] font-medium text-[14px] tracking-[-0.03em] transition-all duration-300 rounded-xl {{ $type === 'pemasukan' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-[#929292] hover:text-[#545454]' }}">
                    Pemasukan
                </button>
                <button type="button" wire:click="$set('type', 'pengeluaran')"
                    class="flex-1 font-['Inter'] font-medium text-[14px] tracking-[-0.03em] transition-all duration-300 rounded-xl {{ $type === 'pengeluaran' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-[#929292] hover:text-[#545454]' }}">
                    Pengeluaran
                </button>
            </div>
            <form wire:submit.prevent="save" class="space-y-5">
                <!-- Nominal -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Nominal</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="number" wire:model="amount" class="w-full font-['Inter'] font-bold text-[20px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Rp 0">
                    </div>
                    @error('amount') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <!-- Kategori -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Kategori</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model="category" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="">Pilih kategori</option>
                            @if($type === 'pemasukan')
                                @foreach($kategoriPemasukan as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            @else
                                @foreach($kategoriPengeluaran as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                            <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 opacity-60" alt="">
                        </div>
                    </div>
                    @error('category') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                @if(auth()->user()->isAdmin())
                <!-- Cabang -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Cabang</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model="unit_id" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="">Pilih Cabang</option>
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
                <!-- Keterangan -->
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Keterangan</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="text" wire:model="description" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Tulis disini...">
                    </div>
                </div>
                <!-- Upload Bukti -->
                <div class="flex flex-col items-center justify-center pt-4 space-y-2">
                    <label class="cursor-pointer group flex flex-col items-center space-y-2 relative">
                        <input type="file" wire:model="attachment" accept="image/*" class="hidden">
                        <div wire:loading wire:target="attachment" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-2xl z-10 backdrop-blur-sm">
                            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        @if($attachment)
                            <div class="relative w-24 h-24 rounded-2xl overflow-hidden shadow-lg border-2 border-emerald-100 group-hover:border-emerald-300 transition-all">
                                <img src="{{ $attachment->temporaryUrl() }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <span class="text-white text-[9px] font-black uppercase tracking-widest">Ganti</span>
                                </div>
                            </div>
                            <div class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Gambar Siap
                            </div>
                        @else
                            <div class="p-3 rounded-full bg-gray-50 group-hover:bg-blue-50 transition-colors border-2 border-dashed border-gray-200 group-hover:border-blue-200">
                                <img src="{{ asset('images/picture.svg') }}" class="h-6 w-6 object-contain opacity-40 group-hover:opacity-80 transition-opacity" alt="Tambah Bukti">
                            </div>
                            <span class="font-['Inter'] font-light text-[12px] text-[#929292] group-hover:text-blue-500 uppercase tracking-widest transition-colors">Tambahkan Bukti (Opsional)</span>
                        @endif
                    </label>
                    @error('attachment') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <!-- Footer -->
                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="$set('isCreating', false)"
                        class="flex-1 py-3 border border-gray-200 rounded-xl font-['Inter'] font-medium text-[14px] text-blue-600 hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-['Inter'] font-medium text-[14px] shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">Buat</button>
                </div>
            </form>
            @endif
        </div>
    </div>
    @endif

    <!-- ─── Modal: Detail Transaksi ────────────────────────────────────────── -->
    @if($selectedTransaction)
    <div class="fixed inset-0 z-[60] overflow-hidden bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="absolute inset-0" wire:click="closeDetail"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                <div class="p-8">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-2xl {{ $selectedTransaction->type === 'pemasukan' ? 'bg-blue-50' : 'bg-rose-50' }} shadow-inner shrink-0">
                                <img src="{{ asset($selectedTransaction->type === 'pemasukan' ? 'images/pemasukan.svg' : 'images/Pengeluaran.svg') }}" class="h-6 w-6 object-contain" alt="{{ $selectedTransaction->type }}">
                            </div>
                            <div>
                                <h2 class="font-['Inter'] font-bold text-[20px] text-gray-900 leading-none tracking-[-0.03em]">{{ $selectedTransaction->category }}</h2>
                                <p class="font-['Inter'] font-light text-[12px] text-[#929292] mt-1 uppercase tracking-widest">
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
                    <!-- Nominal -->
                    <div class="mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                        <span class="font-['Inter'] font-medium text-[10px] text-[#929292] uppercase tracking-widest block mb-1">Nominal Transaksi</span>
                        <h1 class="font-['Inter'] font-bold text-[32px] text-gray-900 leading-none tracking-[-0.03em] break-all">
                            Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }},00
                        </h1>
                    </div>
                    <!-- Detail Stack -->
                    <div class="space-y-6 mb-8">
                        <div>
                            <h3 class="font-['Inter'] font-medium text-[10px] text-[#929292] uppercase tracking-[0.2em] mb-3">Foto Bukti</h3>
                            <div class="aspect-video rounded-3xl bg-gray-50 border border-gray-100 overflow-hidden group relative shadow-inner">
                                @if($selectedTransaction->attachment_path)
                                    <img src="{{ Storage::url($selectedTransaction->attachment_path) }}" alt="Bukti" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out">
                                    <a href="{{ Storage::url($selectedTransaction->attachment_path) }}" target="_blank" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center backdrop-blur-sm transition-opacity duration-500">
                                        <div class="px-6 py-3 bg-white rounded-2xl font-['Inter'] font-medium text-[11px] uppercase tracking-[0.2em] shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                            Lihat Gambar Penuh
                                        </div>
                                    </a>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2 py-8">
                                        <div class="p-3 bg-white rounded-full shadow-sm">
                                            <img src="{{ asset('images/picture.svg') }}" class="h-6 w-6 object-contain opacity-30" alt="Tidak ada lampiran">
                                        </div>
                                        <span class="font-['Inter'] font-medium text-[10px] text-gray-300 uppercase tracking-widest">Tidak ada lampiran</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="font-['Inter'] font-medium text-[10px] text-[#929292] uppercase tracking-[0.2em] mb-3">Keterangan</h3>
                            <div class="p-5 rounded-3xl bg-gray-50/50 border border-gray-100 italic font-['Inter'] font-light text-[14px] text-gray-600 leading-relaxed">
                                "{{ $selectedTransaction->description ?: 'Tidak ada keterangan tambahan.' }}"
                            </div>
                        </div>
                        <div class="flex items-center gap-4 px-2">
                            <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-['Inter'] font-bold text-[16px] shadow-lg shadow-blue-100 shrink-0">
                                {{ substr($selectedTransaction->user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-['Inter'] font-medium text-[10px] text-[#929292] uppercase tracking-widest">Dicatat Oleh</span>
                                <span class="font-['Inter'] font-bold text-[14px] text-gray-900 tracking-tight">{{ $selectedTransaction->user->name }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="pt-6 border-t border-gray-50 flex gap-3">
                        @if(auth()->user()->isAdmin())
                        <button wire:click="editTransaction({{ $selectedTransaction->id }})"
                            class="flex-1 flex items-center justify-center gap-2 py-4 bg-blue-600 text-white rounded-2xl font-['Inter'] font-medium text-[12px] shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button
                            @click="Swal.fire({ title: 'Hapus Transaksi?', text: 'Data tidak bisa dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#2563eb', cancelButtonColor: '#e5e7eb', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', customClass: { cancelButton: '!text-gray-700', popup: '!rounded-3xl !shadow-2xl', title: '!font-black !text-gray-900 !text-xl', htmlContainer: '!text-gray-400 !text-sm', confirmButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3', cancelButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3' }}).then(r => r.isConfirmed && $wire.deleteTransaction({{ $selectedTransaction->id }}))"
                            class="flex-1 flex items-center justify-center gap-2 py-4 bg-rose-50 text-rose-600 rounded-2xl font-['Inter'] font-medium text-[12px] hover:bg-rose-100 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                        @else
                        <button wire:click="closeDetail"
                            class="w-full py-4 bg-blue-600 text-white rounded-2xl font-['Inter'] font-medium text-[12px] shadow-2xl shadow-blue-100 hover:bg-blue-700 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em]">
                            Kembali ke Daftar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ─── Modal: Edit Transaksi (Admin) ──────────────────────────────────── -->
    @if($isEditing)
    <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto no-scrollbar">
            <div class="mb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-blue-50 shadow-inner shrink-0">
                        <img src="{{ asset('images/transaksi.svg') }}" class="h-6 w-6 object-contain" alt="Transaksi">
                    </div>
                    <div>
                        <h2 class="font-['Inter'] font-bold text-[28px] text-gray-900 leading-none tracking-[-0.03em]">Edit Transaksi</h2>
                        <p class="font-['Inter'] font-light text-[16px] text-[#929292] mt-1 leading-none tracking-[-0.03em]">Perbarui data transaksi.</p>
                    </div>
                </div>
                <button type="button" wire:click="resetEditState" class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 hover:text-gray-600 hover:scale-110 active:scale-95 transition-all">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Type Tabs -->
            <div class="flex mb-6 rounded-2xl bg-gray-50 p-1 h-12">
                <button type="button" wire:click="$set('type', 'pemasukan')"
                    class="flex-1 font-['Inter'] font-medium text-[14px] tracking-[-0.03em] transition-all duration-300 rounded-xl {{ $type === 'pemasukan' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-[#929292] hover:text-[#545454]' }}">
                    Pemasukan
                </button>
                <button type="button" wire:click="$set('type', 'pengeluaran')"
                    class="flex-1 font-['Inter'] font-medium text-[14px] tracking-[-0.03em] transition-all duration-300 rounded-xl {{ $type === 'pengeluaran' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-[#929292] hover:text-[#545454]' }}">
                    Pengeluaran
                </button>
            </div>
            <form wire:submit.prevent="update" class="space-y-5">
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Nominal</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="number" wire:model="amount" class="w-full font-['Inter'] font-bold text-[20px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Rp 0">
                    </div>
                    @error('amount') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Kategori</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model="category" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="">Pilih kategori</option>
                            @if($type === 'pemasukan')
                                @foreach($kategoriPemasukan as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            @else
                                @foreach($kategoriPengeluaran as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                            <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 opacity-60" alt="">
                        </div>
                    </div>
                    @error('category') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Cabang</label>
                    <div class="relative border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <select wire:model="unit_id" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 cursor-pointer appearance-none" style="background-image:none">
                            <option value="">Pilih Cabang</option>
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
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Tanggal</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="date" wire:model="transaction_date" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0">
                    </div>
                    @error('transaction_date') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="font-['Inter'] font-medium text-[12px] text-[#929292] uppercase tracking-widest">Keterangan</label>
                    <div class="border-b border-gray-100 focus-within:border-blue-600 transition-colors pb-2">
                        <input type="text" wire:model="description" class="w-full font-['Inter'] font-medium text-[16px] text-gray-900 bg-transparent border-none focus:ring-0 p-0 placeholder-gray-300" placeholder="Tulis disini...">
                    </div>
                </div>
                <!-- Ganti Lampiran -->
                <div class="flex flex-col items-center justify-center pt-2 space-y-2">
                    <label class="cursor-pointer group flex flex-col items-center space-y-2 relative">
                        <input type="file" wire:model="attachment" accept="image/*" class="hidden">
                        <div wire:loading wire:target="attachment" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-2xl z-10 backdrop-blur-sm">
                            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        @if($attachment)
                            <div class="relative w-24 h-24 rounded-2xl overflow-hidden shadow-lg border-2 border-emerald-100">
                                <img src="{{ $attachment->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                            <div class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Gambar Baru Siap
                            </div>
                        @else
                            <div class="p-3 rounded-full bg-gray-50 group-hover:bg-blue-50 transition-colors border-2 border-dashed border-gray-200 group-hover:border-blue-200">
                                <svg class="h-6 w-6 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="font-['Inter'] font-light text-[12px] text-[#929292] group-hover:text-blue-500 uppercase tracking-widest transition-colors">Ganti Bukti (Opsional)</span>
                        @endif
                    </label>
                    @error('attachment') <span class="font-['Inter'] font-medium text-[11px] text-red-500 uppercase tracking-wider">{{ $message }}</span> @enderror
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="resetEditState"
                        class="flex-1 py-3 border border-gray-200 rounded-xl font-['Inter'] font-medium text-[14px] text-blue-600 hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-['Inter'] font-medium text-[14px] shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ─── Daftar Transaksi ────────────────────────────────────────────────── -->
    <div class="flex flex-col gap-3 lg:gap-4">
        @forelse($transactions as $transaction)
        <div wire:click="viewDetail({{ $transaction->id }})"
            class="group cursor-pointer bg-white w-full py-4 lg:py-0 lg:h-[96px] px-4 lg:px-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3 lg:gap-6 min-w-0">
                <div class="p-2 lg:p-3 rounded-2xl {{ $transaction->type === 'pemasukan' ? 'bg-blue-50 group-hover:bg-blue-600' : 'bg-rose-50 group-hover:bg-rose-500' }} group-hover:scale-110 transition-all duration-300 shrink-0">
                    <img src="{{ asset($transaction->type === 'pemasukan' ? 'images/pemasukan.svg' : 'images/Pengeluaran.svg') }}"
                        class="h-7 w-7 lg:h-9 lg:w-9 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="{{ $transaction->type }}">
                </div>
                <div class="min-w-0">
                    <p class="font-['Inter'] font-normal text-base lg:text-[24px] text-[#7D7D7D] leading-none tracking-[-0.03em] truncate">{{ $transaction->category }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if(auth()->user()->isAdmin())
                        <span class="font-['Inter'] font-light text-[12px] text-[#929292] leading-none tracking-[-0.03em] truncate">{{ $transaction->unit->name }}</span>
                        <span class="text-gray-300">•</span>
                        @endif
                        <span class="font-['Inter'] font-light text-[12px] text-[#929292] leading-none tracking-[-0.03em] shrink-0">{{ $transaction->transaction_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-right shrink-0 ml-3">
                <p class="font-['Inter'] font-bold text-base sm:text-xl lg:text-[32px] leading-none tracking-[-0.03em] text-[#7D7D7D]">
                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </p>
            </div>
        </div>
        @empty
        <div class="w-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100 shrink-0">
            <div class="flex flex-col items-center gap-3 lg:gap-4">
                <div class="p-6 bg-gray-50 rounded-full shadow-sm">
                    <svg class="h-12 w-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="font-['Inter'] font-light text-sm lg:text-[20px] text-[#929292] leading-none tracking-[-0.03em]">Belum ada transaksi pada periode ini</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-5 lg:mt-8 w-full">
        {{ $transactions->links('livewire.pagination') }}
    </div>
</div>
