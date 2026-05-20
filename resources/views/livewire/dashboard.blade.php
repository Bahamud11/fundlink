<div>
    <div class="flex items-start justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
            <p class="text-gray-400 font-medium mt-1 text-sm">Berikut ringkasan keuangan yayasan.</p>
        </div>
        
        <!-- Notification Bell -->
        <a href="{{ route('notifications') }}" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors duration-200 mt-2">
            @if(auth()->user()->notifications()->where('is_read', false)->exists())
                <span class="absolute top-2 right-2 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            @endif
            <img src="{{ asset('images/lonceng.png') }}" class="h-7 w-7 object-contain" alt="Notifikasi">
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Saldo Card -->
        <div class="bg-blue-600 p-8 rounded-3xl shadow-2xl shadow-blue-200 text-white relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center space-x-3 mb-10">
                <div class="p-2 bg-white/20 rounded-lg">
                    <img src="{{ asset('images/saldo.png') }}" class="h-5 w-5 object-contain brightness-0 invert" alt="Saldo">
                </div>
                <span class="text-xs font-black uppercase tracking-widest opacity-80">Total Saldo</span>
            </div>
            <p class="text-4xl font-black tracking-tight">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm group hover:border-blue-100 transition-all duration-300">
            <div class="flex items-center space-x-3 mb-10 text-blue-600">
                <div class="p-2 bg-blue-50 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/pemasukan.png') }}" class="h-5 w-5 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="Pemasukan">
                </div>
                <span class="text-xs font-black uppercase tracking-widest text-gray-400 group-hover:text-blue-600">Pemasukan</span>
            </div>
            <p class="text-4xl font-black text-gray-900 tracking-tight">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm group hover:border-rose-100 transition-all duration-300">
            <div class="flex items-center space-x-3 mb-10 text-rose-500">
                <div class="p-2 bg-rose-50 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <img src="{{ asset('images/Pengeluaran.png') }}" class="h-5 w-5 object-contain transition-all duration-300 group-hover:brightness-0 group-hover:invert" alt="Pengeluaran">
                </div>
                <span class="text-xs font-black uppercase tracking-widest text-gray-400 group-hover:text-rose-500">Pengeluaran</span>
            </div>
            <p class="text-4xl font-black text-gray-900 tracking-tight">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm mb-12">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 lg:gap-0">
            <!-- Left Side: Bar Chart -->
            <div class="flex-1 min-w-0 w-full overflow-hidden">
                <div class="flex items-center space-x-3 mb-10">
                    <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                        <img src="{{ asset('images/trendmingguan.png') }}" class="h-5 w-5 object-contain" alt="Trend Mingguan">
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Trend Mingguan</h3>
                </div>
                
                <div wire:ignore class="h-80 relative">
                    <canvas id="trendChart"></canvas>
                </div>

                <div class="flex justify-center space-x-12 mt-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Pemasukan</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-blue-200"></div>
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Pengeluaran</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Filters & Donut -->
            <div class="lg:w-96 lg:ml-16 mt-16 lg:mt-0 flex flex-col space-y-12 min-w-0 w-full overflow-hidden">
                <!-- Filters -->
                <div class="grid grid-cols-1 gap-10">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rentang Analisis</label>
                        <div class="relative border-b border-gray-100 pb-2">
                            <select wire:model.live="filterKategori" class="w-full bg-transparent border-none p-0 text-sm font-black text-gray-900 focus:ring-0 !appearance-none cursor-pointer">
                                <option>Mingguan</option>
                                <option>Bulanan</option>
                            </select>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="grid @if(auth()->user()->isAdmin()) grid-cols-2 @else grid-cols-1 @endif gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu</label>
                            <div class="relative border-b border-gray-100 pb-2">
                                <select wire:model.live="filterWaktu" class="w-full bg-transparent border-none p-0 text-sm font-black text-gray-900 focus:ring-0 !appearance-none cursor-pointer">
                                    <option>Minggu ke-1</option>
                                    <option>Minggu ke-2</option>
                                </select>
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cabang</label>
                            <div class="relative border-b border-gray-100 pb-2">
                                <select wire:model.live="filterCabang" class="w-full bg-transparent border-none p-0 text-sm font-black text-gray-900 focus:ring-0 !appearance-none cursor-pointer">
                                    <option value="Semua">Semua Cabang</option>
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
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Donut Chart & Legend -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-50">
                    <div class="space-y-5">
                        <div class="flex items-center space-x-4">
                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pemasukan</span>
                                <span class="text-lg font-black text-gray-900">{{ $incomePercentage }}%</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-2 h-2 rounded-full bg-blue-200"></div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengeluaran</span>
                                <span class="text-lg font-black text-gray-900">{{ $expensePercentage }}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div wire:ignore class="h-40 w-40 relative">
                        <canvas id="categoryChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-black text-gray-300 uppercase">Ratio</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight">Transaksi Terbaru</h3>
            <a href="{{ route('transactions') }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">Lihat Semua</a>
        </div>
        
        <div class="space-y-4">
            @foreach($recentTransactions as $transaction)
            <div wire:click="viewDetail({{ $transaction->id }})" class="group cursor-pointer bg-white p-6 rounded-3xl border border-transparent hover:border-gray-100 hover:shadow-xl hover:shadow-gray-100/50 transition-all duration-300 flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="p-3 rounded-2xl {{ $transaction->type === 'pemasukan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset($transaction->type === 'pemasukan' ? 'images/pemasukan.png' : 'images/Pengeluaran.png') }}" class="h-6 w-6 object-contain" alt="{{ $transaction->type }}">
                    </div>
                    <div>
                        <p class="text-lg font-black text-gray-900 tracking-tight">{{ $transaction->category }}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            @if(auth()->user()->isAdmin())
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $transaction->unit->name }}</span>
                            <span class="text-gray-300">•</span>
                            @endif
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $transaction->transaction_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-gray-800 tracking-tight">
                        {{ $transaction->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($selectedTransaction)
        <!-- Balanced Transaction Detail View Design -->
        <div class="fixed inset-0 z-[60] overflow-y-auto no-scrollbar bg-black/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="min-h-screen flex items-center justify-center p-4">
                <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative border border-gray-100 overflow-hidden max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="p-8 text-left">
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

            const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            const initCharts = (weeklyData, categoryData) => {
                const trendCanvas = document.getElementById('trendChart');
                const categoryCanvas = document.getElementById('categoryChart');

                if (!trendCanvas || !categoryCanvas) return;

                // Trend Chart
                const trendCtx = trendCanvas.getContext('2d');
                if (window.trendChartInstance) window.trendChartInstance.destroy();
                window.trendChartInstance = new Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.map(d => {
                            const date = new Date(d.date);
                            return dayNames[date.getDay()];
                        }),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: weeklyData.map(d => d.income),
                                backgroundColor: '#2563eb', // blue-600
                                borderRadius: 4,
                                barThickness: 15,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8
                            },
                            {
                                label: 'Pengeluaran',
                                data: weeklyData.map(d => d.expense),
                                backgroundColor: '#bfdbfe', // blue-200
                                borderRadius: 4,
                                barThickness: 15,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false }, border: { display: false }, ticks: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: 'bold', size: 12 }, color: '#9ca3af' } }
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
