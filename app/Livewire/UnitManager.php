<?php

namespace App\Livewire;

use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isEditing = false;
    public $unitId;
    public $name;
    public $address;
    public $google_maps_url;
    public $initial_balance = 0;
    public $member_count_input;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'google_maps_url' => 'nullable|url',
        'initial_balance' => 'nullable|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['unitId', 'name', 'address', 'google_maps_url', 'initial_balance', 'member_count_input']);
        $this->isEditing = true;
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->address = $unit->address;
        $this->google_maps_url = $unit->google_maps_url;
        $this->initial_balance = 0; // Don't show initial balance on edit
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->unitId) {
            Unit::find($this->unitId)->update([
                'name' => $this->name,
                'address' => $this->address,
                'google_maps_url' => $this->google_maps_url,
            ]);
        } else {
            $unit = Unit::create([
                'name' => $this->name,
                'address' => $this->address,
                'google_maps_url' => $this->google_maps_url,
            ]);

            // Handle Dana Awal
            if ($this->initial_balance > 0) {
                $unit->transactions()->create([
                    'user_id' => auth()->id(),
                    'type' => 'pemasukan',
                    'amount' => $this->initial_balance,
                    'category' => 'Dana Awal',
                    'description' => 'Saldo awal unit baru',
                    'transaction_date' => now(),
                ]);
            }
        }

        $this->isEditing = false;
        session()->flash('message', 'Unit berhasil disimpan.');
    }

    public function delete($id)
    {
        Unit::find($id)->delete();
        session()->flash('message', 'Unit berhasil dihapus.');
    }

    public function render()
    {
        $units = Unit::query()
            ->withCount('users')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        // Add balance calculation to each unit
        $units->getCollection()->transform(function($unit) {
            $pemasukan = $unit->transactions()->where('type', 'pemasukan')->sum('amount');
            $pengeluaran = $unit->transactions()->where('type', 'pengeluaran')->sum('amount');
            $unit->balance = $pemasukan - $pengeluaran;
            return $unit;
        });

        return view('livewire.unit-manager', [
            'units' => $units,
        ])->layout('layouts.app');
    }
}
