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

    public function viewDetail($id)
    {
        $this->selectedTransaction = Transaction::with(['unit', 'user'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTransaction = null;
    }

    public function updated($propertyName)
    {
        $this->dispatch('chartUpdated', [
            'weeklyData' => $this->getWeeklyData(),
            'categoryData' => $this->getCategoryData(),
            'incomePercentage' => $this->getIncomePercentage(),
            'expensePercentage' => $this->getExpensePercentage(),
        ]);
    }

    private function applyDateFilter($query)
    {
        if ($this->filterKategori === 'Mingguan') {
            $query->where('transaction_date', '>=', now()->subDays(6)->toDateString());
        } elseif ($this->filterKategori === 'Bulanan') {
            $query->where('transaction_date', '>=', now()->startOfMonth()->toDateString());
        } elseif ($this->filterKategori === 'Tahunan') {
            $query->where('transaction_date', '>=', now()->startOfYear()->toDateString());
        }
        return $query;
    }

    private function getWeeklyData()
    {
        $user = auth()->user();
        $query = Transaction::query();
        
        // Weekly Trend always shows global data for Admins, 
        // but remains restricted for regular Unit users.
        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        }
        
        // Get start of current week (Monday)
        $startOfWeek = now()->startOfWeek();
        
        // Fetch existing data for the current week
        $existingData = $query->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
            )
            ->where('transaction_date', '>=', $startOfWeek->toDateString())
            ->where('transaction_date', '<=', now()->endOfWeek()->toDateString())
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Generate all 7 days of the week
        $weeklyData = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            
            if (isset($existingData[$date])) {
                $weeklyData->push($existingData[$date]);
            } else {
                $weeklyData->push([
                    'date' => $date,
                    'income' => 0,
                    'expense' => 0
                ]);
            }
        }

        return $weeklyData;
    }

    private function getCategoryData()
    {
        $query = $this->getBaseQuery();
        $query = $this->applyDateFilter($query);
        return $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
    }

    private function getIncomePercentage()
    {
        $query = $this->getBaseQuery();
        $query = $this->applyDateFilter($query);
        
        $totalPemasukan = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $totalSum = $totalPemasukan + $totalPengeluaran;
        
        return $totalSum > 0 ? round(($totalPemasukan / $totalSum) * 100) : 0;
    }

    private function getExpensePercentage()
    {
        $query = $this->getBaseQuery();
        $query = $this->applyDateFilter($query);
        
        $totalPemasukan = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $totalSum = $totalPemasukan + $totalPengeluaran;
        
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
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'recentTransactions' => $recentTransactions,
            'weeklyData' => $this->getWeeklyData(),
            'categoryData' => $this->getCategoryData(),
            'incomePercentage' => $this->getIncomePercentage(),
            'expensePercentage' => $this->getExpensePercentage(),
            'units' => \App\Models\Unit::all(),
        ])->layout('layouts.app');
    }
}
