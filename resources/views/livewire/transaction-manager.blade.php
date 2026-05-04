<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Transaksi Keuangan</h2>
                <p class="text-gray-400 font-medium mt-1">
                    {{ auth()->user()->isAdmin() ? 'Riwayat lengkap arus kas yayasan.' : 'Kelola transaksi unit Anda.' }}
                </p>
            </div>
            @if(!auth()->user()->isAdmin())
                <button wire:click="$toggle('isCreating')" class="px-8 py-3 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-200 hover:scale-105 transition-all duration-300">
                    {{ $isCreating ? 'Batal' : 'Transaksi Baru' }}
                </button>
            @endif
        </div>
    </x-slot>

    @if($isCreating)
        <!-- Transaction Form (User Only) -->
        <div class="max-w-3xl mx-auto bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 mb-10">
            <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center space-x-3">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span>Catat Transaksi Baru</span>
            </h3>

            <form wire:submit.prevent="save" class="space-y-8">
                <!-- Type Toggle -->
                <div class="flex p-1 bg-gray-100 rounded-2xl w-fit">
                    <button type="button" wire:click="$set('type', 'pemasukan')" 
                        class="px-8 py-2 rounded-xl text-xs font-black transition-all duration-200 {{ $type === 'pemasukan' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        PEMASUKAN
                    </button>
                    <button type="button" wire:click="$set('type', 'pengeluaran')" 
                        class="px-8 py-2 rounded-xl text-xs font-black transition-all duration-200 {{ $type === 'pengeluaran' ? 'bg-white text-rose-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                        PENGELUARAN
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Amount -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Nominal (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-400 text-sm">Rp</span>
                            <input type="number" wire:model="amount" class="w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="0">
                        </div>
                        @error('amount') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Kategori</label>
                        <input type="text" wire:model="category" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900" placeholder="e.g. Dana BOS, Listrik">
                        @error('category') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal</label>
                        <input type="date" wire:model="transaction_date" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900">
                        @error('transaction_date') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>

                    <!-- Attachment -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Bukti Transaksi</label>
                        <input type="file" wire:model="attachment" class="w-full text-xs font-bold text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        @error('attachment') <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-bold text-gray-900 h-32" placeholder="Detail transaksi..."></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-12 py-4 bg-gray-900 text-white rounded-2xl text-sm font-black shadow-xl shadow-gray-200 hover:bg-blue-600 transition-all duration-300">
                        Konfirmasi Transaksi
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- History Card -->
    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
            <h3 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Transaksi</h3>
            
            @if(auth()->user()->isAdmin())
                <!-- Admin Filter -->
                <div class="flex items-center space-x-4 w-full md:w-auto">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Filter per Unit</span>
                    <select wire:model.live="filterUnit" class="bg-gray-50 border-none rounded-2xl px-6 py-3 text-xs font-bold text-gray-700 focus:ring-blue-600 flex-1 md:flex-none md:min-w-[200px]">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Kode Ref</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Unit / PIC</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Kategori</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Tanggal</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Bukti</th>
                        <th class="pb-6 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $transaction)
                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                        <td class="py-6">
                            <span class="text-xs font-black text-gray-900 tabular-nums bg-gray-100 px-3 py-1 rounded-lg">#FL-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-gray-800">{{ $transaction->unit->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 mt-1">PIC: {{ $transaction->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-6">
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wider border border-blue-100">{{ $transaction->category }}</span>
                        </td>
                        <td class="py-6 text-xs font-bold text-gray-400 italic">
                            {{ $transaction->transaction_date->format('M d, Y') }}
                        </td>
                        <td class="py-6">
                            @if($transaction->attachment_path)
                                <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" class="p-2 bg-blue-50 text-blue-600 rounded-xl inline-block hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm shadow-blue-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-gray-300 uppercase italic">Tanpa Bukti</span>
                            @endif
                        </td>
                        <td class="py-6 text-right">
                            <span class="text-sm font-black {{ $transaction->type === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-50 rounded-full mb-4">
                                    <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-black text-gray-400">Tidak ada transaksi ditemukan untuk pilihan ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
