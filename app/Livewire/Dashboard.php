<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $filterKategori = 'Mingguan';
    public $filterWaktu = 'Minggu ke-1';
    public $filterCabang = 'Semua';
    public $selectedTransaction = null;

    public function mount()
    {
        $this->filterWaktu = 'Minggu ke-' . min(4, (int) ceil(now()->day / 7));
    }

    public function viewDetail($id)
    {
        $this->selectedTransaction = Transaction::with(['unit', 'user'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTransaction = null;
    }

    public function updatedFilterKategori($value)
    {
        $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        if ($value === 'Mingguan') {
            $this->filterWaktu = 'Minggu ke-' . min(4, (int) ceil(now()->day / 7));
        } elseif ($value === 'Bulanan') {
            $this->filterWaktu = $namaBulan[now()->month - 1] . ' ' . now()->year;
        } elseif ($value === 'Tahunan') {
            $this->filterWaktu = (string) now()->year;
        }
        $this->dispatchChartUpdate();
    }

    public function updated($propertyName)
    {
        if ($propertyName !== 'filterKategori') {
            $this->dispatchChartUpdate();
        }
    }

    private function dispatchChartUpdate()
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
        return match($this->filterKategori) {
            'Bulanan' => 'Trend Bulanan',
            'Tahunan' => 'Trend Tahunan',
            default   => 'Trend Mingguan',
        };
    }

    private function applyFrequencyFilter($query)
    {
        $namaBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                      'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        if ($this->filterKategori === 'Mingguan') {
            $weekNum  = (int) str_replace('Minggu ke-', '', $this->filterWaktu);
            $startDay = ($weekNum - 1) * 7 + 1;
            $endDay   = $weekNum === 4 ? 31 : $weekNum * 7;
            $year     = now()->year;
            $month    = now()->month;
            $start    = \Carbon\Carbon::createFromDate($year, $month, $startDay)->startOfDay();
            $end      = \Carbon\Carbon::createFromDate($year, $month, min($endDay, now()->daysInMonth))->endOfDay();
            $query->whereBetween('transaction_date', [$start, $end]);

        } elseif ($this->filterKategori === 'Bulanan') {
            $parts    = explode(' ', $this->filterWaktu);
            $monthNum = $namaBulan[$parts[0]] ?? now()->month;
            $year     = isset($parts[1]) ? (int) $parts[1] : now()->year;
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $monthNum);

        } elseif ($this->filterKategori === 'Tahunan') {
            $year = (int) $this->filterWaktu;
            if ($year > 0) {
                $query->whereYear('transaction_date', $year);
            }
        }

        return $query;
    }

    private function getChartData()
    {
        $user  = auth()->user();
        $query = Transaction::query();

        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterCabang !== 'Semua') {
            $query->where('unit_id', $this->filterCabang);
        }

        $this->applyFrequencyFilter($query);

        if ($this->filterKategori === 'Mingguan') {
            // Group by day-of-week
            $data = $query->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Fill all 7 days of the selected week
            $namaBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                          'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            $weekNum  = (int) str_replace('Minggu ke-', '', $this->filterWaktu);
            $startDay = ($weekNum - 1) * 7 + 1;
            $result   = collect();
            for ($d = 0; $d < 7; $d++) {
                $day  = $startDay + $d;
                if ($day > now()->daysInMonth) break;
                $date = \Carbon\Carbon::createFromDate(now()->year, now()->month, $day)->toDateString();
                $result->push($data[$date] ?? ['date' => $date, 'income' => 0, 'expense' => 0]);
            }
            return $result;

        } elseif ($this->filterKategori === 'Bulanan') {
            // Group by week-of-month (4 bars)
            $parts    = explode(' ', $this->filterWaktu);
            $namaBulan2 = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                           'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            $monthNum = $namaBulan2[$parts[0]] ?? now()->month;
            $year     = isset($parts[1]) ? (int) $parts[1] : now()->year;
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->daysInMonth;

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
                $day = (int) \Carbon\Carbon::parse($row->date)->day;
                $wi  = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                $weeks[$wi]['income']  += $row->income;
                $weeks[$wi]['expense'] += $row->expense;
            }

            return collect($weeks);

        } elseif ($this->filterKategori === 'Tahunan') {
            // Group by month (12 bars)
            $year = (int) $this->filterWaktu;
            $namaBulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

            $rows = $query->select(
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
                )
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $result = collect();
            for ($m = 1; $m <= 12; $m++) {
                $result->push([
                    'date'    => "$year-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-01",
                    'label'   => substr($namaBulanList[$m - 1], 0, 3),
                    'income'  => $rows[$m]->income ?? 0,
                    'expense' => $rows[$m]->expense ?? 0,
                ]);
            }
            return $result;
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

    private function getIncomePercentage()
    {
        $query = $this->getBaseQuery();
        $this->applyFrequencyFilter($query);

        $totalPemasukan   = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $totalSum         = $totalPemasukan + $totalPengeluaran;

        return $totalSum > 0 ? round(($totalPemasukan / $totalSum) * 100) : 0;
    }

    private function getExpensePercentage()
    {
        $query = $this->getBaseQuery();
        $this->applyFrequencyFilter($query);

        $totalPemasukan   = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $totalSum         = $totalPemasukan + $totalPengeluaran;

        return $totalSum > 0 ? round(($totalPengeluaran / $totalSum) * 100) : 0;
    }

    private function getBaseQuery()
    {
        $user = auth()->user();
        $query = Transaction::query();
        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterCabang !== 'Semua') {
            $query->where('unit_id', $this->filterCabang);
        }
        return $query;
    }

    public function render()
    {
        $query = $this->getBaseQuery();

        $totalPemasukan = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $recentTransactions = (clone $query)->with(['unit', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'totalPemasukan'     => $totalPemasukan,
            'totalPengeluaran'   => $totalPengeluaran,
            'saldo'              => $saldo,
            'recentTransactions' => $recentTransactions,
            'weeklyData'         => $this->getChartData(),
            'categoryData'       => $this->getCategoryData(),
            'incomePercentage'   => $this->getIncomePercentage(),
            'expensePercentage'  => $this->getExpensePercentage(),
            'chartTitle'         => $this->getChartTitle(),
            'units'              => \App\Models\Unit::all(),
        ])->layout('layouts.app');
    }
}
