<div>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Ringkasan Beranda</h2>
            <p class="text-gray-400 font-medium mt-1">Pantau kesehatan keuangan seluruh unit secara real-time.</p>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <!-- Saldo Card -->
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-blue-100/50 relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 h-40 w-40 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="p-3 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Saldo Terkini</span>
                </div>
                <h3 class="text-gray-400 font-bold text-sm uppercase tracking-wider mb-2">Total Saldo</h3>
                <p class="text-4xl font-black text-gray-900 tabular-nums tracking-tight">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-emerald-100/50 relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 h-40 w-40 bg-emerald-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="p-3 bg-emerald-500 rounded-2xl shadow-lg shadow-emerald-200">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">+12% Kenaikan</span>
                </div>
                <h3 class="text-gray-400 font-bold text-sm uppercase tracking-wider mb-2">Total Pemasukan</h3>
                <p class="text-4xl font-black text-gray-900 tabular-nums tracking-tight text-emerald-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-rose-100/50 relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 h-40 w-40 bg-rose-50 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="p-3 bg-rose-500 rounded-2xl shadow-lg shadow-rose-200">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-rose-600 bg-rose-50 px-3 py-1 rounded-full border border-rose-100">Pengeluaran</span>
                </div>
                <h3 class="text-gray-400 font-bold text-sm uppercase tracking-wider mb-2">Total Pengeluaran</h3>
                <p class="text-4xl font-black text-gray-900 tabular-nums tracking-tight text-rose-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Weekly Trend -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 h-[400px] flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Trend Mingguan</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu</span>
                    <select wire:model.live="filterWaktu" class="bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-500 focus:ring-blue-600 px-3 py-2">
                        <option>Minggu ke-1</option>
                        <option>Minggu ke-2</option>
                        <option>Minggu ke-3</option>
                        <option>Minggu ke-4</option>
                    </select>
                </div>
            </div>
            <div wire:ignore class="flex-1">
                <canvas id="trendChart"></canvas>
            </div>
            <div class="flex justify-center space-x-6 mt-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pemasukan</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pengeluaran</span>
                </div>
            </div>
        </div>

        <!-- Category Pie -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 h-[400px] flex flex-col">
            <div class="flex flex-col space-y-3 mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori</span>
                    <select wire:model.live="filterKategori" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-500 focus:ring-blue-600 px-2 py-1">
                        <option>Mingguan</option>
                        <option>Bulanan</option>
                        <option>Tahunan</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cabang</span>
                    <select wire:model.live="filterCabang" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-500 focus:ring-blue-600 px-2 py-1">
                        <option value="Semua">Semua</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div wire:ignore class="flex-1 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Table -->
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Aktivitas Terbaru</h3>
                <p class="text-gray-400 text-xs font-medium mt-1">Pergerakan keuangan terbaru di seluruh unit.</p>
            </div>
            <a href="{{ route('transactions') }}" class="px-6 py-2 bg-gray-50 text-blue-600 rounded-xl text-xs font-black hover:bg-blue-600 hover:text-white transition-all duration-300">Lihat Semua Transaksi</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="pb-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">ID Transaksi</th>
                        <th class="pb-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Unit</th>
                        <th class="pb-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Kategori</th>
                        <th class="pb-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Tanggal</th>
                        <th class="pb-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentTransactions as $transaction)
                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                        <td class="py-4">
                            <span class="text-xs font-black text-gray-900 tabular-nums">#TX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-4">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500 border border-gray-200">
                                    {{ substr($transaction->unit->name, 0, 2) }}
                                </div>
                                <span class="text-xs font-bold text-gray-700">{{ $transaction->unit->name }}</span>
                            </div>
                        </td>
                        <td class="py-4">
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wider border border-blue-100">{{ $transaction->category }}</span>
                        </td>
                        <td class="py-4">
                            <span class="text-xs font-bold text-gray-400 italic">{{ $transaction->transaction_date->format('M d, Y') }}</span>
                        </td>
                        <td class="py-4 text-right">
                            <span class="text-sm font-black {{ $transaction->type === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let trendChart, categoryChart;

            const initCharts = () => {
                const weeklyData = @json($weeklyData);
                const categoryData = @json($categoryData);

                // Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                if (trendChart) trendChart.destroy();
                trendChart = new Chart(trendCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.map(d => {
                            const date = new Date(d.date);
                            return date.toLocaleDateString('id-ID', { weekday: 'short' });
                        }),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: weeklyData.map(d => d.income),
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                                barThickness: 15
                            },
                            {
                                label: 'Pengeluaran',
                                data: weeklyData.map(d => d.expense),
                                backgroundColor: '#f43f5e',
                                borderRadius: 8,
                                barThickness: 15
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false }, border: { display: false }, ticks: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: 'bold', size: 10 }, color: '#9ca3af' } }
                        }
                    }
                });

                // Category Chart
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                if (categoryChart) categoryChart.destroy();
                categoryChart = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.map(d => d.category),
                        datasets: [{
                            data: categoryData.map(d => d.total),
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'],
                            borderWidth: 0,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 8, padding: 15, font: { weight: 'bold', size: 9 }, color: '#6b7280' } }
                        }
                    }
                });
            };

            initCharts();

            // Re-init on Livewire update
            Livewire.on('chartUpdated', () => {
                initCharts();
            });
        });
    </script>
    @endpush
</div>
