<?php

namespace App\Livewire;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    // ─── State ────────────────────────────────────────────────────────────────
    public string  $search       = '';
    public bool    $isEditing    = false;
    public ?User   $selectedUser = null;
    public ?int    $userId       = null;
    public string  $name         = '';
    public string  $email        = '';
    public string  $password     = '';
    public string  $role         = 'user';
    public ?int    $unit_id      = null;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
            'role'     => 'required|in:admin,user',
            'unit_id'  => 'required_if:role,user|nullable|exists:units,id',
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
        $user            = User::with('unit')->findOrFail($id);
        $user->is_online = $this->isOnline($id);
        $this->selectedUser = $user;
    }

    public function closeDetail(): void
    {
        $this->selectedUser = null;
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
    }

    public function edit(int $id): void
    {
        $user           = User::findOrFail($id);
        $this->userId   = $user->id;
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->role     = $user->role;
        $this->unit_id  = $user->unit_id;
        $this->password = '';
        $this->selectedUser = null;
        $this->isEditing    = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role'    => $this->role,
            'unit_id' => $this->role === 'admin' ? null : $this->unit_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::findOrFail($this->userId)->update($data);
        } else {
            User::create($data);
        }

        $this->isEditing = false;
        $this->resetForm();
        $this->dispatch('swal-success', message: 'Pengguna berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        // Cegah hapus diri sendiri
        abort_if($id === auth()->id(), 403, 'Tidak dapat menghapus akun sendiri.');

        User::findOrFail($id)->delete();
        $this->selectedUser = null;
        $this->dispatch('swal-success', message: 'Pengguna berhasil dihapus.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->userId   = null;
        $this->name     = '';
        $this->email    = '';
        $this->password = '';
        $this->role     = 'user';
        $this->unit_id  = null;
    }

    private function isOnline(int $userId): bool
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->exists();
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $users = User::with('unit')
            ->when($this->search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', '%' . $this->search . '%')
                   ->orWhere('email', 'like', '%' . $this->search . '%')
            ))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Ambil semua user_id aktif dalam satu query (hindari N+1)
        $onlineIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->pluck('user_id')
            ->flip();

        $users->getCollection()->transform(function (User $user) use ($onlineIds) {
            $user->is_online = $onlineIds->has($user->id);
            return $user;
        });

        return view('livewire.user-manager', [
            'users' => $users,
            'units' => Unit::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
