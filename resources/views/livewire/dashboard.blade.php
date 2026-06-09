<div>
    <!-- Header -->
    <div class="w-full flex items-center justify-between mb-6 lg:mb-8">
        <div class="flex flex-col justify-center">
            <h2 class="font-['Inter'] font-bold text-2xl sm:text-3xl lg:text-[40px] text-black leading-none tracking-[-0.03em]">Selamat Datang, {{ auth()->user()->name }} <span class="inline-block animate-[wave_1.5s_ease-in-out_infinite]">👋</span></h2>
            <p class="font-['Inter'] font-light text-sm sm:text-base lg:text-[24px] text-[#545454] mt-1 leading-none tracking-[-0.03em]">Berikut ringkasan keuangan yayasan.</p>
        </div>

        <!-- Notification Bell -->
        <a href="{{ route('notifications') }}" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors duration-200 shrink-0">
            <img src="{{ asset($hasUnreadNotif ? 'images/notifred.svg' : 'images/notif.svg') }}" class="h-7 w-7 sm:h-8 sm:w-8 lg:h-9 lg:w-9 object-contain" alt="Notifikasi">
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="w-full grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Saldo Card -->
        <div class="bg-blue-600 p-5 lg:p-6 rounded-3xl shadow-2xl shadow-blue-200 text-white relative overflow-hidden group flex flex-col justify-between min-h-[120px] lg:min-h-[153px]">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg">
                    <img src="{{ asset('images/saldo.svg') }}" class="h-5 w-5 object-contain brightness-0 invert" alt="Saldo">
                </div>
                <span class="font-light text-base lg:text-[24px] text-[#EBEFFC] leading-none tracking-[-0.03em]">Saldo</span>
            </div>
            <p class="text-2xl sm:text-3xl lg:text-[40px] font-bold leading-none tracking-[-0.03em] mt-3 lg:mb-2 break-all">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-gradient-to-b from-white to-[#EBEFFC] p-5 lg:p-6 rounded-3xl border border-gray-100 shadow-sm group hover:border-blue-100 transition-all duration-300 flex flex-col justify-between min-h-[120px] lg:min-h-[153px]">
            <div class="flex items-center gap-3 text-blue-600">
                <div class="p-2 bg-blue-50 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/pemasukan.svg') }}" class="h-5 w-5 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="Pemasukan">
                </div>
                <span class="font-light text-base lg:text-[24px] text-[#545454] group-hover:text-blue-600 leading-none tracking-[-0.03em]">Pemasukan</span>
            </div>
            <p class="text-2xl sm:text-3xl lg:text-[40px] font-bold text-[#545454] leading-none tracking-[-0.03em] mt-3 lg:mb-2 break-all">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-gradient-to-b from-white to-[#FFE6E6] p-5 lg:p-6 rounded-3xl border border-gray-100 shadow-sm group hover:border-rose-100 transition-all duration-300 flex flex-col justify-between min-h-[120px] lg:min-h-[153px]">
            <div class="flex items-center gap-3 text-rose-500">
                <div class="p-2 bg-rose-50 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/Pengeluaran.svg') }}" class="h-5 w-5 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="Pengeluaran">
                </div>
                <span class="font-light text-base lg:text-[24px] text-[#545454] group-hover:text-rose-500 leading-none tracking-[-0.03em]">Pengeluaran</span>
            </div>
            <p class="text-2xl sm:text-3xl lg:text-[40px] font-bold text-[#545454] leading-none tracking-[-0.03em] mt-3 lg:mb-2 break-all">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="w-full flex flex-col lg:flex-row gap-4 lg:gap-4 mb-6 lg:mb-8">
        <!-- Bar Chart Card -->
        <div class="flex-1 min-w-0 bg-white rounded-[32px] p-5 lg:p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-5 lg:mb-6">
                <div class="p-2 rounded-lg text-gray-400">
                    <img src="{{ asset('images/trendmingguan.svg') }}" class="h-6 w-6 lg:h-8 lg:w-8 object-contain" alt="Trend Mingguan">
                </div>
                <h3 class="text-base lg:text-[20px] font-light text-[#545454] leading-none tracking-[-0.03em]">{{ $chartTitle }}</h3>
            </div>

            <div wire:ignore class="relative h-[238px] mb-3">
                <!-- Scrollable chart wrapper -->
                <div id="trendChartWrapper"
                     class="w-full h-full overflow-x-auto overflow-y-hidden">
                    <div id="trendChartInner" style="height: 226.28px; margin: 0 auto;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-center items-center gap-8 lg:gap-12">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 lg:w-3.5 lg:h-3.5 rounded-full bg-blue-600 shrink-0"></div>
                    <span class="text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em]">Pemasukan</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 lg:w-3.5 lg:h-3.5 rounded-full bg-blue-200 shrink-0"></div>
                    <span class="text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em]">Pengeluaran</span>
                </div>
            </div>
        </div>

        <!-- Donut Chart Card -->
        <div class="w-full lg:w-[450px] lg:shrink-0 bg-white rounded-[32px] p-5 lg:p-6 border border-gray-100 shadow-sm flex flex-col justify-start gap-6 lg:gap-[30px]">
            <!-- Filters -->
            <div class="flex flex-col gap-4">
                <div class="flex flex-col space-y-1.5">
                    <label class="text-base lg:text-[20px] font-light text-[#000000] leading-none tracking-[-0.03em]">Rentang</label>
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <button @click="open = !open" type="button" class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm text-sm lg:text-[24px] font-light text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                            <span>{{ $filterKategori }}</span>
                            <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                        </button>
                        <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-40 overflow-y-auto focus:outline-none" style="display: none;">
                            <button @click="$wire.set('filterKategori', 'Mingguan'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                Mingguan
                            </button>
                            <button @click="$wire.set('filterKategori', 'Bulanan'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                Bulanan
                            </button>
                            <button @click="$wire.set('filterKategori', 'Tahunan'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                Tahunan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid @if(auth()->user()->isAdmin()) grid-cols-2 @else grid-cols-1 @endif gap-3">
                    <!-- Waktu -->
                    <div class="flex flex-col space-y-1.5">
                        <label class="text-base lg:text-[20px] font-light text-[#000000] leading-none tracking-[-0.03em]">Waktu</label>
                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button @click="open = !open" type="button" class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm text-sm lg:text-[24px] font-light text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                                <span class="truncate">{{ $filterWaktu }}</span>
                                <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                            </button>
                            <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-40 overflow-y-auto focus:outline-none" style="display: none;">
                                @if($filterKategori === 'Mingguan')
                                    @foreach(['Minggu ke-1','Minggu ke-2','Minggu ke-3','Minggu ke-4'] as $opt)
                                        <button @click="$wire.set('filterWaktu', '{{ $opt }}'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                            {{ $opt }}
                                        </button>
                                    @endforeach
                                @elseif($filterKategori === 'Bulanan')
                                    @php $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                    @foreach($namaBulan as $bulan)
                                        <button @click="$wire.set('filterWaktu', '{{ $bulan }}'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                            {{ $bulan }}
                                        </button>
                                    @endforeach
                                @elseif($filterKategori === 'Tahunan')
                                    @foreach(range(now()->year, now()->year - 4) as $opt)
                                        <button @click="$wire.set('filterWaktu', '{{ $opt }}'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                            {{ $opt }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Cabang (admin only) -->
                    @if(auth()->user()->isAdmin())
                    <div class="flex flex-col space-y-1.5">
                        <label class="text-base lg:text-[20px] font-light text-[#000000] leading-none tracking-[-0.03em]">Cabang</label>
                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button @click="open = !open" type="button" class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm text-sm lg:text-[24px] font-light text-[#545454] leading-[1.2] tracking-[-0.03em] focus:outline-none cursor-pointer">
                                <span class="truncate">{{ $filterCabang === 'Semua' ? 'Semua' : ($units->firstWhere('id', $filterCabang)->name ?? 'Semua') }}</span>
                                <img src="{{ asset('images/dropdown.svg') }}" class="h-3 w-3 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" alt="">
                            </button>
                            <div x-show="open" class="absolute left-0 z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl py-1 max-h-40 overflow-y-auto focus:outline-none" style="display: none;">
                                <button @click="$wire.set('filterCabang', 'Semua'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">Semua</button>
                                @foreach($units as $unit)
                                    <button @click="$wire.set('filterCabang', '{{ $unit->id }}'); open = false;" type="button" class="w-full text-left px-4 py-2 text-sm lg:text-[24px] font-light text-[#545454] leading-none tracking-[-0.03em] hover:bg-gray-50 transition-colors">
                                        {{ $unit->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Donut Chart & Legend -->
            <div class="flex items-center justify-center gap-5 lg:gap-6 pt-5 border-t border-gray-50 shrink-0">
                <!-- Legend -->
                <div class="space-y-3 lg:space-y-4">
                    <div class="flex items-center gap-2 lg:gap-[10px]">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 shrink-0"></div>
                        <span class="text-xs lg:text-[16px] font-normal text-[#929292] leading-none tracking-normal">Pemasukan</span>
                        <span class="px-1.5 py-0.5 bg-blue-800 text-white text-xs lg:text-[13px] font-bold rounded-lg leading-none">{{ $incomePercentage }}%</span>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-[10px]">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-200 shrink-0"></div>
                        <span class="text-xs lg:text-[16px] font-normal text-[#929292] leading-none tracking-normal">Pengeluaran</span>
                        <span class="px-1.5 py-0.5 bg-blue-800 text-white text-xs lg:text-[13px] font-bold rounded-lg leading-none">{{ $expensePercentage }}%</span>
                    </div>
                </div>

                <!-- Donut Chart -->
                <div wire:ignore class="h-32 w-32 relative flex-shrink-0">
                    <canvas id="categoryChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mb-10 lg:mb-12">
        <div class="w-full flex items-center justify-between mb-4 lg:mb-6">
            <h3 class="text-base lg:text-[24px] font-medium text-[#545454] leading-none tracking-[-0.03em]">Transaksi Terbaru</h3>
        </div>

        <div class="flex flex-col gap-3 lg:gap-4">
            @foreach($recentTransactions as $transaction)
            <div wire:click="viewDetail({{ $transaction->id }})" class="group cursor-pointer bg-white w-full px-4 lg:px-6 py-4 lg:py-0 lg:h-[96px] rounded-2xl lg:rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between">
                <div class="flex items-center gap-3 lg:gap-6 min-w-0">
                    <div class="p-2 lg:p-3 rounded-xl lg:rounded-2xl {{ $transaction->type === 'pemasukan' ? 'bg-blue-50 group-hover:bg-blue-600' : 'bg-rose-50 group-hover:bg-rose-500' }} group-hover:scale-110 transition-all duration-300 shrink-0">
                        <img src="{{ asset($transaction->type === 'pemasukan' ? 'images/pemasukan.svg' : 'images/Pengeluaran.svg') }}" class="h-6 w-6 lg:h-9 lg:w-9 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="{{ $transaction->type }}">
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm lg:text-[24px] font-normal text-[#7D7D7D] leading-none tracking-[-0.03em] truncate">{{ $transaction->category }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if(auth()->user()->isAdmin())
                            <span class="text-[11px] lg:text-[12px] font-light text-[#929292] leading-none tracking-[-0.03em] truncate">{{ $transaction->unit->name }}</span>
                            <span class="text-gray-300">•</span>
                            @endif
                            <span class="text-[11px] lg:text-[12px] font-light text-[#929292] leading-none tracking-[-0.03em] shrink-0">{{ $transaction->transaction_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <p class="text-base sm:text-xl lg:text-[32px] font-bold leading-none tracking-[-0.03em] text-[#7D7D7D]">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($selectedTransaction)
        <!-- Transaction Detail Modal -->
        <div class="fixed inset-0 z-[60] overflow-hidden bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="absolute inset-0" wire:click="closeDetail"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4 pointer-events-none">
                <div class="pointer-events-auto bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="p-6 sm:p-8 text-left">
                        <!-- Header: Icon & Category -->
                        <div class="flex items-center justify-between mb-6 sm:mb-8">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 rounded-2xl {{ $selectedTransaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} shadow-inner shrink-0">
                                    <img src="{{ asset($selectedTransaction->type === 'pemasukan' ? 'images/pemasukan.svg' : 'images/Pengeluaran.svg') }}" class="h-6 w-6 object-contain" alt="{{ $selectedTransaction->type }}">
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

                        <!-- Amount -->
                        <div class="mb-6 sm:mb-8 text-center bg-gray-50/50 py-6 rounded-3xl border border-gray-100">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nominal Transaksi</span>
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight break-all">
                                Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }},00
                            </h1>
                        </div>

                        <!-- Details Stack -->
                        <div class="space-y-6 mb-6 sm:mb-8">
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
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-2 py-8">
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
                                Kembali ke Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        const initDashboardCharts = () => {
            let trendChart, categoryChart;

            const initCharts = (weeklyData, categoryData) => {
                const trendCanvas = document.getElementById('trendChart');
                const categoryCanvas = document.getElementById('categoryChart');

                if (!trendCanvas || !categoryCanvas) return;

                // Build labels dynamically
                const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const labels = weeklyData.map(d => {
                    if (d.label) return d.label; // Bulanan (Minggu 1-4) or Tahunan (Jan-Des)
                    const date = new Date(d.date);
                    return dayNames[date.getDay()];
                });

                // Trend Chart
                if (window.trendChartInstance) window.trendChartInstance.destroy();

                const barWidth   = 61.4;
                const innerGap   = 9.21;
                const groupGap   = 24;
                const groupW     = barWidth * 2 + innerGap + groupGap;
                const padX       = 80;
                const neededW    = weeklyData.length * groupW + padX;
                const canvasW    = neededW;
                const canvasH    = 226.28;

                trendCanvas.parentElement.style.width = canvasW + 'px';
                trendCanvas.style.width  = canvasW + 'px';
                trendCanvas.style.height = canvasH + 'px';

                window.trendChartInstance = new Chart(trendCanvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: weeklyData.map(d => d.income),
                                backgroundColor: '#2563eb',
                                borderRadius: {
                                    topLeft: 12,
                                    topRight: 12,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                },
                                borderSkipped: false,
                                categoryPercentage: 141.22 / 156.01,
                                barPercentage: 61.4 / 70.61,
                            },
                            {
                                label: 'Pengeluaran',
                                data: weeklyData.map(d => d.expense),
                                backgroundColor: '#bfdbfe',
                                borderRadius: {
                                    topLeft: 12,
                                    topRight: 12,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                },
                                borderSkipped: false,
                                categoryPercentage: 141.22 / 156.01,
                                barPercentage: 61.4 / 70.61,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        devicePixelRatio: window.devicePixelRatio || 2,
                        animation: { duration: 400 },
                        plugins: { legend: { display: false } },
                        layout: {
                            padding: { left: 40, right: 40, top: 10, bottom: 10 },
                            autoPadding: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { display: false },
                                border: { display: false },
                                ticks: { display: false }
                            },
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    font: { family: 'Inter, sans-serif', weight: 300, size: 24, lineHeight: 1 },
                                    color: '#545454',
                                    textAlign: 'center',
                                    maxRotation: 0
                                }
                            }
                        }
                    }
                });

                // Category Chart (Donut)
                const categoryCtx = categoryCanvas.getContext('2d');
                if (window.categoryChartInstance) window.categoryChartInstance.destroy();
                window.categoryChartInstance = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.map(d => d.category),
                        datasets: [{
                            data: categoryData.map(d => d.total),
                            backgroundColor: ['#2563eb', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
                            borderWidth: 0,
                            cutout: '80%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            };

            // Initial load data
            const initialWeeklyData = @json($weeklyData);
            const initialCategoryData = @json($categoryData);
            initCharts(initialWeeklyData, initialCategoryData);

            // Listen for Livewire updates
            Livewire.on('chartUpdated', (eventData) => {
                const data = eventData[0];
                initCharts(data.weeklyData, data.categoryData);
            });
        };

        // Run on initial load and every navigation
        document.addEventListener('livewire:navigated', initDashboardCharts);
        document.addEventListener('livewire:initialized', initDashboardCharts);
    </script>
    @endpush
</div>
