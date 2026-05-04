<?php

namespace App\Livewire;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $isEditing = false;
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role = 'user';
    public $unit_id;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required|in:admin,user',
            'unit_id' => 'required_if:role,user|nullable|exists:units,id',
        ];
    }

    public function create()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'role', 'unit_id']);
        $this->isEditing = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->unit_id = $user->unit_id;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'unit_id' => $this->role === 'admin' ? null : $this->unit_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::find($this->userId)->update($data);
        } else {
            User::create($data);
        }

        $this->isEditing = false;
        session()->flash('message', 'Pengguna berhasil disimpan.');
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.user-manager', [
            'users' => User::with('unit')->latest()->paginate(10),
            'units' => Unit::all(),
        ])->layout('layouts.app');
    }
}
