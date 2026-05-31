<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Transaction $selectedTransaction = null;

    public string $filterKategori = 'Mingguan';
    public string $filterWaktu    = 'Minggu ke-1';
    public string $filterCabang   = 'Semua';

    // ─── Bulan Indonesia ─────────────────────────────────────────────────────
    private const BULAN = [
        'Januari' => 1, 'Februari' => 2, 'Maret'    => 3, 'April'    => 4,
        'Mei'     => 5, 'Juni'     => 6, 'Juli'      => 7, 'Agustus'  => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
    ];

    private const BULAN_LIST = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember',
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->filterWaktu = 'Minggu ke-' . min(4, (int) ceil(now()->day / 7));
    }

    // ─── Detail Transaksi ─────────────────────────────────────────────────────

    public function viewDetail(int $id): void
    {
        $this->selectedTransaction = Transaction::with(['unit', 'user'])->findOrFail($id);
    }

    public function closeDetail(): void
    {
        $this->selectedTransaction = null;
    }

    // ─── Filter Handlers ──────────────────────────────────────────────────────

    public function updatedFilterKategori(string $value): void
    {
        $this->filterWaktu = match ($value) {
            'Mingguan' => 'Minggu ke-' . min(4, (int) ceil(now()->day / 7)),
            'Bulanan'  => self::BULAN_LIST[now()->month - 1] . ' ' . now()->year,
            'Tahunan'  => (string) now()->year,
            default    => $this->filterWaktu,
        };

        $this->dispatchChartUpdate();
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName !== 'filterKategori') {
            $this->dispatchChartUpdate();
        }
    }

    // ─── Chart Dispatch ───────────────────────────────────────────────────────

    private function dispatchChartUpdate(): void
    {
        $this->dispatch('chartUpdated', [
            'weeklyData'        => $this->getChartData(),
            'categoryData'      => $this->getCategoryData(),
            'incomePercentage'  => $this->getIncomePercentage(),
            'expensePercentage' => $this->getExpensePercentage(),
            'chartTitle'        => $this->getChartTitle(),
        ]);
    }

    private function getChartTitle(): string
    {
        return match ($this->filterKategori) {
            'Bulanan' => 'Trend Bulanan',
            'Tahunan' => 'Trend Tahunan',
            default   => 'Trend Mingguan',
        };
    }

    // ─── Query Helpers ────────────────────────────────────────────────────────

    private function getBaseQuery()
    {
        $user  = auth()->user();
        $query = Transaction::query();

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterCabang !== 'Semua') {
            $query->where('unit_id', $this->filterCabang);
        }

        return $query;
    }

    private function applyFrequencyFilter($query): void
    {
        if ($this->filterKategori === 'Mingguan') {
            $weekNum  = (int) str_replace('Minggu ke-', '', $this->filterWaktu);
            $startDay = ($weekNum - 1) * 7 + 1;
            $endDay   = $weekNum === 4 ? 31 : $weekNum * 7;
            $start    = Carbon::createFromDate(now()->year, now()->month, $startDay)->startOfDay();
            $end      = Carbon::createFromDate(now()->year, now()->month, min($endDay, now()->daysInMonth))->endOfDay();
            $query->whereBetween('transaction_date', [$start, $end]);

        } elseif ($this->filterKategori === 'Bulanan') {
            [$bulanStr, $yearStr] = array_pad(explode(' ', $this->filterWaktu, 2), 2, now()->year);
            $monthNum = self::BULAN[$bulanStr] ?? now()->month;
            $query->whereYear('transaction_date', (int) $yearStr)
                  ->whereMonth('transaction_date', $monthNum);

        } elseif ($this->filterKategori === 'Tahunan') {
            $year = (int) $this->filterWaktu;
            if ($year > 0) {
                $query->whereYear('transaction_date', $year);
            }
        }
    }

    // ─── Chart Data ───────────────────────────────────────────────────────────

    private function getChartData()
    {
        $query = $this->getBaseQuery();
        $this->applyFrequencyFilter($query);

        if ($this->filterKategori === 'Mingguan') {
            $data = $query->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $weekNum  = (int) str_replace('Minggu ke-', '', $this->filterWaktu);
            $startDay = ($weekNum - 1) * 7 + 1;
            $result   = collect();

            for ($d = 0; $d < 7; $d++) {
                $day = $startDay + $d;
                if ($day > now()->daysInMonth) break;
                $date = Carbon::createFromDate(now()->year, now()->month, $day)->toDateString();
                $result->push($data[$date] ?? ['date' => $date, 'income' => 0, 'expense' => 0]);
            }

            return $result;
        }

        if ($this->filterKategori === 'Bulanan') {
            [$bulanStr, $yearStr] = array_pad(explode(' ', $this->filterWaktu, 2), 2, now()->year);
            $monthNum = self::BULAN[$bulanStr] ?? now()->month;
            $year     = (int) $yearStr;

            $rows = $query->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
                )
                ->groupBy('date')
                ->get();

            $weeks = [
                ['label' => 'Minggu 1', 'income' => 0, 'expense' => 0, 'date' => "$year-$monthNum-01"],
                ['label' => 'Minggu 2', 'income' => 0, 'expense' => 0, 'date' => "$year-$monthNum-08"],
                ['label' => 'Minggu 3', 'income' => 0, 'expense' => 0, 'date' => "$year-$monthNum-15"],
                ['label' => 'Minggu 4', 'income' => 0, 'expense' => 0, 'date' => "$year-$monthNum-22"],
            ];

            foreach ($rows as $row) {
                $day = (int) Carbon::parse($row->date)->day;
                $wi  = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                $weeks[$wi]['income']  += $row->income;
                $weeks[$wi]['expense'] += $row->expense;
            }

            return collect($weeks);
        }

        if ($this->filterKategori === 'Tahunan') {
            $year = (int) $this->filterWaktu;

            $rows = $query->select(
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
                )
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            return collect(range(1, 12))->map(fn ($m) => [
                'date'    => "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01",
                'label'   => substr(self::BULAN_LIST[$m - 1], 0, 3),
                'income'  => $rows[$m]->income ?? 0,
                'expense' => $rows[$m]->expense ?? 0,
            ]);
        }

        return collect();
    }

    private function getCategoryData()
    {
        $query = $this->getBaseQuery();
        $this->applyFrequencyFilter($query);

        return $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
    }

    /**
     * Hitung persentase pemasukan & pengeluaran dalam satu query.
     */
    private function getRatioData(): array
    {
        $query = $this->getBaseQuery();
        $this->applyFrequencyFilter($query);

        $row = $query->selectRaw(
            'SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
             SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran'
        )->first();

        $pemasukan   = (float) ($row->pemasukan ?? 0);
        $pengeluaran = (float) ($row->pengeluaran ?? 0);
        $total       = $pemasukan + $pengeluaran;

        return [
            'income'  => $total > 0 ? round(($pemasukan / $total) * 100) : 0,
            'expense' => $total > 0 ? round(($pengeluaran / $total) * 100) : 0,
        ];
    }

    private function getIncomePercentage(): int
    {
        return $this->getRatioData()['income'];
    }

    private function getExpensePercentage(): int
    {
        return $this->getRatioData()['expense'];
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $user  = auth()->user();
        $query = $this->getBaseQuery();

        // Hitung saldo, pemasukan, pengeluaran dalam satu query
        $summary = (clone $query)->selectRaw(
            'SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
             SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran'
        )->first();

        $totalPemasukan   = (float) ($summary->pemasukan ?? 0);
        $totalPengeluaran = (float) ($summary->pengeluaran ?? 0);

        $recentTransactions = (clone $query)
            ->with(['unit', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $ratio = $this->getRatioData();

        return view('livewire.dashboard', [
            'totalPemasukan'     => $totalPemasukan,
            'totalPengeluaran'   => $totalPengeluaran,
            'saldo'              => $totalPemasukan - $totalPengeluaran,
            'recentTransactions' => $recentTransactions,
            'weeklyData'         => $this->getChartData(),
            'categoryData'       => $this->getCategoryData(),
            'incomePercentage'   => $ratio['income'],
            'expensePercentage'  => $ratio['expense'],
            'chartTitle'         => $this->getChartTitle(),
            'units'              => $user->isAdmin() ? Unit::orderBy('name')->get() : collect(),
            'hasUnreadNotif'     => $user->notifications()->where('is_read', false)->exists(),
        ])->layout('layouts.app');
    }
}
