<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination, WithFileUploads;

    public $filterUnit = '';
    public $filterRange = 'Mingguan';
    public $filterFrequency = 'Minggu ke-1';
    public $isCreating = false;

    public $unit_id;
    public $type = 'pemasukan';
    public $amount;
    public $category;
    public $description;
    public $transaction_date;
    public $attachment;
    public $selectedTransaction = null;

    public function viewDetail($id)
    {
        $this->selectedTransaction = Transaction::with(['unit', 'user'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTransaction = null;
    }

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
        if (!auth()->user()->isAdmin()) {
            $this->unit_id = auth()->user()->unit_id;
        }
        // Set default frequency based on current date
        $this->filterFrequency = 'Minggu ke-' . min(4, (int) ceil(now()->day / 7));
    }

    public function updatedFilterRange($value)
    {
        // Reset frequency to sensible default when range changes
        if ($value === 'Mingguan') {
            $this->filterFrequency = 'Minggu ke-' . (int) ceil(now()->day / 7);
        } elseif ($value === 'Bulanan') {
            $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $this->filterFrequency = $namaBulan[now()->month - 1] . ' ' . now()->year;
        } elseif ($value === 'Tahunan') {
            $this->filterFrequency = (string) now()->year;
        }
        $this->resetPage();
    }

    public function updatedFilterFrequency()
    {
        $this->resetPage();
    }

    public function updatedFilterUnit()
    {
        $this->resetPage();
    }

    private function applyFrequencyFilter($query)
    {
        if ($this->filterRange === 'Mingguan') {
            // Minggu ke-1 = hari 1-7, ke-2 = 8-14, ke-3 = 15-21, ke-4 = 22-akhir bulan
            $weekNum = (int) str_replace('Minggu ke-', '', $this->filterFrequency);
            $startDay = ($weekNum - 1) * 7 + 1;
            $endDay   = $weekNum === 4 ? 31 : $weekNum * 7;

            // We filter by day-of-month within the current month
            $year  = now()->year;
            $month = now()->month;
            $start = \Carbon\Carbon::createFromDate($year, $month, $startDay)->startOfDay();
            $end   = \Carbon\Carbon::createFromDate($year, $month, min($endDay, now()->daysInMonth))->endOfDay();

            $query->whereBetween('transaction_date', [$start, $end]);

        } elseif ($this->filterRange === 'Bulanan') {
            // filterFrequency = "Januari 2025", "Februari 2025", dst (Indonesian month names)
            $bulanMap = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
            ];
            $parts = explode(' ', $this->filterFrequency);
            $monthNum = $bulanMap[$parts[0]] ?? now()->month;
            $year     = isset($parts[1]) ? (int) $parts[1] : now()->year;
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $monthNum);

        } elseif ($this->filterRange === 'Tahunan') {
            $year = (int) $this->filterFrequency;
            if ($year > 0) {
                $query->whereYear('transaction_date', $year);
            }
        }

        return $query;
    }

    public function save()
    {
        $user = auth()->user();

        $rules = [
            'unit_id' => 'required|exists:units,id',
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $data = [
            'unit_id' => $this->unit_id,
            'user_id' => $user->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'category' => $this->category,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date,
        ];

        if ($this->attachment) {
            $data['attachment_path'] = $this->attachment->store('attachments', 'public');
        }

        Transaction::create($data);

        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            if ($admin->id !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Transaksi Baru',
                    'message' => "Transaksi {$this->type} sebesar Rp " . number_format($this->amount, 0, ',', '.') . " ditambahkan oleh {$user->name}.",
                    'type' => 'transaction',
                    'is_read' => false,
                ]);
            }
        }

        $this->reset(['amount', 'category', 'description', 'attachment']);
        if ($user->isAdmin()) {
            $this->reset('unit_id');
        }
        $this->isCreating = false;

        session()->flash('message', 'Transaksi berhasil disimpan.');
    }

    public function exportPdf()
    {
        $user = auth()->user();
        $query = Transaction::with(['unit', 'user']);

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterUnit) {
            $query->where('unit_id', $this->filterUnit);
        }

        $this->applyFrequencyFilter($query);

        $transactions = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->get();

        $total_pemasukan = $transactions->where('type', 'pemasukan')->sum('amount');
        $total_pengeluaran = $transactions->where('type', 'pengeluaran')->sum('amount');

        $unit_name = $this->filterUnit ? Unit::find($this->filterUnit)->name : ($user->isAdmin() ? 'Semua Cabang' : $user->unit->name);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transactions', [
            'transactions' => $transactions,
            'range' => $this->filterRange,
            'frequency' => $this->filterFrequency,
            'unit_name' => $unit_name,
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan_Transaksi_' . now()->format('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $user = auth()->user();
        $query = Transaction::with(['unit', 'user']);

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterUnit) {
            $query->where('unit_id', $this->filterUnit);
        }

        $this->applyFrequencyFilter($query);

        return view('livewire.transaction-manager', [
            'transactions' => $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(10),
            'units' => Unit::all(),
        ])->layout('layouts.app');
    }
}
