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

        $transactions = $query->latest('transaction_date')->get();
        
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

        return view('livewire.transaction-manager', [
            'transactions' => $query->latest('transaction_date')->paginate(10),
            'units' => Unit::all(),
        ])->layout('layouts.app');
    }
}
