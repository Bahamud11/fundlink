<?php

namespace App\Livewire;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UnitManager extends Component
{
    use WithPagination;

    // ─── State ────────────────────────────────────────────────────────────────
    public string  $search          = '';
    public bool    $isEditing       = false;
    public ?Unit   $selectedUnit    = null;
    public ?int    $unitId          = null;
    public string  $name            = '';
    public ?string $address         = null;
    public ?string $google_maps_url = null;
    public float   $initial_balance = 0;

    protected function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'initial_balance' => 'nullable|numeric|min:0',
        ];
    }

    // ─── Search ───────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ─── Detail Panel ─────────────────────────────────────────────────────────

    public function viewDetail(int $id): void
    {
        $unit          = Unit::withCount('users')->findOrFail($id);
        $unit->balance = $this->calcBalance($unit);
        $this->selectedUnit = $unit;
    }

    public function closeDetail(): void
    {
        $this->selectedUnit = null;
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
    }

    public function edit(int $id): void
    {
        $unit                 = Unit::findOrFail($id);
        $this->unitId         = $unit->id;
        $this->name           = $unit->name;
        $this->address        = $unit->address;
        $this->google_maps_url = $unit->google_maps_url;
        $this->initial_balance = 0;
        $this->selectedUnit   = null;
        $this->isEditing      = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->unitId) {
            Unit::findOrFail($this->unitId)->update([
                'name'            => $this->name,
                'address'         => $this->address,
                'google_maps_url' => $this->google_maps_url,
            ]);
        } else {
            $unit = Unit::create([
                'name'            => $this->name,
                'address'         => $this->address,
                'google_maps_url' => $this->google_maps_url,
            ]);

            if ($this->initial_balance > 0) {
                $unit->transactions()->create([
                    'user_id'          => auth()->id(),
                    'type'             => 'pemasukan',
                    'amount'           => $this->initial_balance,
                    'category'         => 'Dana Awal',
                    'description'      => 'Saldo awal unit baru',
                    'transaction_date' => now(),
                ]);
            }
        }

        $this->isEditing = false;
        $this->resetForm();
        $this->dispatch('swal-success', message: 'Unit berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        Unit::findOrFail($id)->delete();
        $this->selectedUnit = null;
        $this->dispatch('swal-success', message: 'Unit berhasil dihapus.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->unitId          = null;
        $this->name            = '';
        $this->address         = null;
        $this->google_maps_url = null;
        $this->initial_balance = 0;
    }

    private function calcBalance(Unit $unit): float
    {
        $row = $unit->transactions()
            ->selectRaw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
                         SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran')
            ->first();

        return (float) ($row->pemasukan ?? 0) - (float) ($row->pengeluaran ?? 0);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        // Hitung saldo semua unit dalam satu query (hindari N+1)
        $balances = DB::table('transactions')
            ->selectRaw('unit_id,
                SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as pemasukan,
                SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as pengeluaran')
            ->groupBy('unit_id')
            ->get()
            ->keyBy('unit_id');

        $units = Unit::query()
            ->withCount('users')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $units->getCollection()->transform(function (Unit $unit) use ($balances) {
            $b = $balances[$unit->id] ?? null;
            $unit->balance = $b ? (float) $b->pemasukan - (float) $b->pengeluaran : 0.0;
            return $unit;
        });

        return view('livewire.unit-manager', compact('units'))->layout('layouts.app');
    }
}
