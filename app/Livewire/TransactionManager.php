<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination, WithFileUploads;

    // ─── Filter State ─────────────────────────────────────────────────────────
    public string $filterUnit      = '';
    public string $filterRange     = 'Mingguan';
    public string $filterFrequency = 'Minggu ke-1';

    // ─── Modal State ──────────────────────────────────────────────────────────
    public bool $isCreating = false;
    public bool $isEditing  = false;
    public ?int $editingId  = null;

    // ─── Form Fields ──────────────────────────────────────────────────────────
    public ?int    $unit_id          = null;
    public string  $type             = 'pemasukan';
    public ?float  $amount           = null;
    public string  $category         = '';
    public string  $description      = '';
    public string  $transaction_date = '';
    public         $attachment       = null;

    // ─── Detail Panel ─────────────────────────────────────────────────────────
    public ?Transaction $selectedTransaction = null;

    private const BULAN = [
        'Januari' => 1, 'Februari' => 2, 'Maret'    => 3, 'April'    => 4,
        'Mei'     => 5, 'Juni'     => 6, 'Juli'      => 7, 'Agustus'  => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
    ];

    public const KATEGORI_PEMASUKAN = [
        'Dana BOS'          => 'Dana BOS',
        'Donasi'            => 'Donasi',
        'Infaq'             => 'Infaq',
        'Zakat'             => 'Zakat',
        'Iuran Siswa'       => 'Iuran Siswa',
        'Bantuan Pemerintah'=> 'Bantuan Pemerintah',
        'Hibah'             => 'Hibah',
        'Pendapatan Usaha'  => 'Pendapatan Usaha',
        'Lainnya'           => 'Lainnya',
    ];

    public const KATEGORI_PENGELUARAN = [
        'Gaji Pegawai'      => 'Gaji Pegawai',
        'Listrik & Air'     => 'Listrik & Air',
        'Internet'          => 'Internet',
        'Pemeliharaan'      => 'Pemeliharaan',
        'Alat Tulis Kantor' => 'Alat Tulis Kantor',
        'Konsumsi'          => 'Konsumsi',
        'Kegiatan Siswa'    => 'Kegiatan Siswa',
        'Transportasi'      => 'Transportasi',
        'Kebersihan'        => 'Kebersihan',
        'Perlengkapan'      => 'Perlengkapan',
        'Lainnya'           => 'Lainnya',
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->filterFrequency  = 'Minggu ke-' . min(4, (int) ceil(now()->day / 7));

        if (!auth()->user()->isAdmin()) {
            $this->unit_id = auth()->user()->unit_id;
        }
    }

    // ─── Detail Panel ─────────────────────────────────────────────────────────

    public function viewDetail(int $id): void
    {
        $this->selectedTransaction = Transaction::with(['unit', 'user'])->findOrFail($id);
    }

    public function closeDetail(): void
    {
        $this->selectedTransaction = null;
    }

    // ─── Edit / Delete (Admin Only) ───────────────────────────────────────────

    public function editTransaction(int $id): void
    {
        $this->authorizeAdmin();

        $transaction = Transaction::findOrFail($id);

        $this->editingId        = $transaction->id;
        $this->unit_id          = $transaction->unit_id;
        $this->type             = $transaction->type;
        $this->amount           = $transaction->amount;
        $this->category         = $transaction->category;
        $this->description      = $transaction->description ?? '';
        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->attachment       = null;
        $this->selectedTransaction = null;
        $this->isEditing        = true;
    }

    public function update(): void
    {
        $this->authorizeAdmin();

        $this->validate([
            'unit_id'          => 'required|exists:units,id',
            'type'             => 'required|in:pemasukan,pengeluaran',
            'amount'           => 'required|numeric|min:1',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
            'attachment'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $transaction = Transaction::findOrFail($this->editingId);

        $data = [
            'unit_id'          => $this->unit_id,
            'type'             => $this->type,
            'amount'           => $this->amount,
            'category'         => $this->category,
            'description'      => $this->description ?: null,
            'transaction_date' => $this->transaction_date,
        ];

        if ($this->attachment) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }
            $data['attachment_path'] = $this->attachment->store('attachments', 'public');
        }

        $transaction->update($data);

        $this->resetEditState();
        $this->dispatch('swal-success', message: 'Transaksi berhasil diperbarui.');
    }

    public function deleteTransaction(int $id): void
    {
        $this->authorizeAdmin();

        $transaction = Transaction::findOrFail($id);

        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        $this->selectedTransaction = null;
        $this->dispatch('swal-success', message: 'Transaksi berhasil dihapus.');
    }

    public function resetEditState(): void
    {
        $this->isEditing        = false;
        $this->editingId        = null;
        $this->type             = 'pemasukan';
        $this->transaction_date = now()->format('Y-m-d');
        $this->attachment       = null;
        $this->reset(['amount', 'category', 'description']);

        if (!auth()->user()->isAdmin()) {
            $this->unit_id = auth()->user()->unit_id;
        } else {
            $this->unit_id = null;
        }
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function save(): void
    {
        $user = auth()->user();

        $this->validate([
            'unit_id'          => 'required|exists:units,id',
            'type'             => 'required|in:pemasukan,pengeluaran',
            'amount'           => 'required|numeric|min:1',
            'category'         => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
            'attachment'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'unit_id'          => $this->unit_id,
            'user_id'          => $user->id,
            'type'             => $this->type,
            'amount'           => $this->amount,
            'category'         => $this->category,
            'description'      => $this->description ?: null,
            'transaction_date' => $this->transaction_date,
        ];

        if ($this->attachment) {
            $data['attachment_path'] = $this->attachment->store('attachments', 'public');
        }

        Transaction::create($data);

        // Notifikasi ke semua admin (kecuali diri sendiri)
        \App\Models\User::where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->get()
            ->each(fn ($admin) => \App\Models\Notification::create([
                'user_id' => $admin->id,
                'title'   => 'Transaksi Baru',
                'message' => "Transaksi {$this->type} sebesar Rp " . number_format($this->amount, 0, ',', '.') . " ditambahkan oleh {$user->name}.",
                'type'    => 'transaction',
                'is_read' => false,
            ]));

        $this->reset(['amount', 'category', 'description', 'attachment']);
        if ($user->isAdmin()) {
            $this->unit_id = null;
        }
        $this->isCreating = false;

        $this->dispatch('swal-success', message: 'Transaksi berhasil disimpan.');
    }

    // ─── Export PDF ───────────────────────────────────────────────────────────

    public function exportPdf()
    {
        $user  = auth()->user();
        $query = Transaction::with(['unit', 'user']);

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterUnit) {
            $query->where('unit_id', $this->filterUnit);
        }

        $this->applyFrequencyFilter($query);

        $transactions     = $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->get();
        $total_pemasukan  = $transactions->where('type', 'pemasukan')->sum('amount');
        $total_pengeluaran = $transactions->where('type', 'pengeluaran')->sum('amount');

        $unit_name = $this->filterUnit
            ? Unit::findOrFail($this->filterUnit)->name
            : ($user->isAdmin() ? 'Semua Cabang' : $user->unit->name);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transactions', compact(
            'transactions', 'unit_name', 'total_pemasukan', 'total_pengeluaran'
        ) + ['range' => $this->filterRange, 'frequency' => $this->filterFrequency]);

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'Laporan_Transaksi_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    // ─── Filter Handlers ──────────────────────────────────────────────────────

    public function updatedType(): void
    {
        // Reset kategori saat tipe transaksi berubah agar tidak mismatch
        $this->category = '';
    }

    public function updatedFilterRange(string $value): void
    {
        $this->filterFrequency = match ($value) {
            'Mingguan' => 'Minggu ke-' . min(4, (int) ceil(now()->day / 7)),
            'Bulanan'  => array_keys(self::BULAN)[now()->month - 1] . ' ' . now()->year,
            'Tahunan'  => (string) now()->year,
            default    => $this->filterFrequency,
        };
        $this->resetPage();
    }

    public function updatedFilterFrequency(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUnit(): void
    {
        $this->resetPage();
    }

    // ─── Query Helpers ────────────────────────────────────────────────────────

    private function applyFrequencyFilter($query): void
    {
        if ($this->filterRange === 'Mingguan') {
            $weekNum  = (int) str_replace('Minggu ke-', '', $this->filterFrequency);
            $startDay = ($weekNum - 1) * 7 + 1;
            $endDay   = $weekNum === 4 ? 31 : $weekNum * 7;
            $start    = Carbon::createFromDate(now()->year, now()->month, $startDay)->startOfDay();
            $end      = Carbon::createFromDate(now()->year, now()->month, min($endDay, now()->daysInMonth))->endOfDay();
            $query->whereBetween('transaction_date', [$start, $end]);

        } elseif ($this->filterRange === 'Bulanan') {
            [$bulanStr, $yearStr] = array_pad(explode(' ', $this->filterFrequency, 2), 2, now()->year);
            $monthNum = self::BULAN[$bulanStr] ?? now()->month;
            $query->whereYear('transaction_date', (int) $yearStr)
                  ->whereMonth('transaction_date', $monthNum);

        } elseif ($this->filterRange === 'Tahunan') {
            $year = (int) $this->filterFrequency;
            if ($year > 0) {
                $query->whereYear('transaction_date', $year);
            }
        }
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $user  = auth()->user();
        $query = Transaction::with(['unit', 'user']);

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterUnit) {
            $query->where('unit_id', $this->filterUnit);
        }

        $this->applyFrequencyFilter($query);

        return view('livewire.transaction-manager', [
            'transactions'       => $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(10),
            'units'              => Unit::orderBy('name')->get(),
            'kategoriPemasukan'  => self::KATEGORI_PEMASUKAN,
            'kategoriPengeluaran'=> self::KATEGORI_PENGELUARAN,
        ])->layout('layouts.app');
    }
}
