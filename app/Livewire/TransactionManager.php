<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination, WithFileUploads;

    public $filterUnit = '';
    public $isCreating = false;
    
    // Form fields
    public $type = 'pemasukan';
    public $amount;
    public $category;
    public $description;
    public $transaction_date;
    public $attachment;

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
    }

    public function save()
    {
        $user = auth()->user();
        
        $rules = [
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $data = [
            'unit_id' => $user->unit_id,
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
        $this->isCreating = false;
        
        session()->flash('message', 'Transaksi berhasil disimpan.');
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
