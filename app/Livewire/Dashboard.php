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

    public function updated($propertyName)
    {
        $this->dispatch('chartUpdated');
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = Transaction::query();
        
        // Base unit restriction
        if (!$user->isAdmin()) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($this->filterCabang !== 'Semua') {
            $query->where('unit_id', $this->filterCabang);
        }

        // Stats calculation
        $totalPemasukan = (clone $query)->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $recentTransactions = (clone $query)->with(['unit', 'user'])
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        // Data for bar chart (Trend Mingguan)
        $weeklyData = (clone $query)->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
            )
            ->where('transaction_date', '>=', now()->startOfWeek())
            ->where('transaction_date', '<=', now()->endOfWeek())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data for category chart (Pie/Donut)
        $categoryDataQuery = (clone $query)->select('category', DB::raw('SUM(amount) as total'));
        
        // Apply time filters for category chart
        if ($this->filterKategori === 'Mingguan') {
            $categoryDataQuery->where('transaction_date', '>=', now()->startOfWeek());
        } elseif ($this->filterKategori === 'Bulanan') {
            $categoryDataQuery->where('transaction_date', '>=', now()->startOfMonth());
        } elseif ($this->filterKategori === 'Tahunan') {
            $categoryDataQuery->where('transaction_date', '>=', now()->startOfYear());
        }

        $categoryData = $categoryDataQuery->groupBy('category')->get();

        return view('livewire.dashboard', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'recentTransactions' => $recentTransactions,
            'weeklyData' => $weeklyData,
            'categoryData' => $categoryData,
            'units' => \App\Models\Unit::all(),
        ])->layout('layouts.app');
    }
}
