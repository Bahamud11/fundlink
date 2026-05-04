<?php

namespace App\Livewire;

use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitManager extends Component
{
    use WithPagination;

    public $isEditing = false;
    public $unitId;
    public $name;
    public $address;
    public $google_maps_url;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'google_maps_url' => 'nullable|url',
    ];

    public function create()
    {
        $this->reset(['unitId', 'name', 'address', 'google_maps_url']);
        $this->isEditing = true;
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->address = $unit->address;
        $this->google_maps_url = $unit->google_maps_url;
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
            Unit::create([
                'name' => $this->name,
                'address' => $this->address,
                'google_maps_url' => $this->google_maps_url,
            ]);
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
        return view('livewire.unit-manager', [
            'units' => Unit::latest()->paginate(10),
        ])->layout('layouts.app');
    }
}
